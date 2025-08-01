<?php

namespace App\Exports;

use App\Models\Appointment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SinglePrescriptionExport implements FromCollection, WithHeadings
{
    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function collection()
    {
        $prescription = $this->appointment->prescription;
        $medications = $prescription ? json_decode($prescription->medications, true) : [];

        return collect([
            [
                'Doctor' => $this->appointment->doctor->user->name ?? 'N/A',
                'Appointment Date' => Carbon::parse($this->appointment->appointment_time)->format('d M Y h:i A'),
                'Notes' => $prescription->notes ?? 'N/A',
                'Medications' => collect($medications)->pluck('name')->implode(', '),
                'Doses' => collect($medications)->pluck('dose')->implode(', '),
                'Durations' => collect($medications)->pluck('duration')->implode(', '),
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Doctor',
            'Appointment Date',
            'Notes',
            'Medications',
            'Doses',
            'Durations',
        ];
    }
}
