<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Notifications\AppointmentCreatedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppointmentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Appointment $appointment;

    /**
     * Create a new job instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->appointment->doctor && $this->appointment->doctor->user) {
            $this->appointment->doctor->user->notify(new AppointmentCreatedNotification($this->appointment));
        }

         if ($this->appointment->patient && $this->appointment->patient->user) {
             $this->appointment->patient->user->notify(new AppointmentCreatedNotification($this->appointment));
         }
    }
}
