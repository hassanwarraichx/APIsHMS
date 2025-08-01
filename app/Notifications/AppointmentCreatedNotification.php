<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class AppointmentCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Appointment $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Define the channels for notification delivery.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Mail message sent to the user.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🩺 New Appointment Scheduled')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new appointment has been scheduled for you.')
            ->line('🧑‍⚕️ Doctor: ' . optional($this->appointment->doctor->user)->name)
            ->line('🕒 Time: ' . \Carbon\Carbon::parse($this->appointment->appointment_time)->format('d M Y, h:i A'))
            ->line('📝 Notes: ' . ($this->appointment->notes ?? 'No notes'))
            ->action('View Appointment', url('/patient/dashboard'))
            ->line('Thank you for using our Hospital Management System!');
    }

    /**
     * Save the notification to the database.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Appointment Scheduled',
            'message' => 'You have a new appointment on ' .
                \Carbon\Carbon::parse($this->appointment->appointment_time)->format('d M Y, h:i A'),
            'appointment_id' => $this->appointment->id,
            'doctor_name' => optional($this->appointment->doctor->user)->name,
            'type' => 'appointment-created',
        ];
    }

    /**
     * Broadcast data (for real-time front-end updates).
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Appointment Scheduled',
            'message' => 'You have a new appointment on ' .
                \Carbon\Carbon::parse($this->appointment->appointment_time)->format('d M Y, h:i A'),
            'appointment_id' => $this->appointment->id,
            'doctor_name' => optional($this->appointment->doctor->user)->name,
            'type' => 'appointment-created',
        ]);
    }
}
