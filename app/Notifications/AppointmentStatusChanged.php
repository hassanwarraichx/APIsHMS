<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Carbon\Carbon;

class AppointmentStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected Appointment $appointment;
    protected bool $shouldSendMail = true;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Disable mail notification manually.
     */
    public function withoutMail(): static
    {
        $this->shouldSendMail = false;
        return $this;
    }

    /**
     * Define the delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database', 'broadcast'];

        if ($this->shouldSendMail && $this->appointment->status === 'approved') {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Build the mail representation.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('✅ Appointment Approved')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('🎉 Your appointment on **' . Carbon::parse($this->appointment->appointment_time)->format('d M Y, h:i A') . '** with Dr. **' . optional($this->appointment->doctor?->user)->name . '** has been **APPROVED**.')
            ->action('View Appointments', url('/appointments'))
            ->line('Thank you for using our Hospital Management System.');
    }

    /**
     * Store notification data in database.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->payload();
    }

    /**
     * Real-time broadcast payload.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->payload());
    }

    /**
     * Common notification payload data.
     */
    protected function payload(): array
    {
        return [
            'title' => '🩺 Appointment ' . ucfirst($this->appointment->status),
            'message' => 'Your appointment with Dr. ' . optional($this->appointment->doctor?->user)->name .
                ' on ' . Carbon::parse($this->appointment->appointment_time)->format('d M Y, h:i A') .
                ' was ' . ucfirst($this->appointment->status) . '.',
            'appointment_id' => $this->appointment->id,
            'status' => $this->appointment->status,
            'type' => 'status-update',
        ];
    }
}
