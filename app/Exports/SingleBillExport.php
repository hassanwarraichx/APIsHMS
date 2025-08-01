<?php

namespace App\Exports;

use App\Models\Appointment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SingleBillExport implements FromCollection, WithHeadings
{
    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function collection()
    {
        $bill = $this->appointment->bill;

        return collect([
            [
                'Doctor' => $this->appointment->doctor->user->name ?? 'N/A',
                'Appointment Date' => Carbon::parse($this->appointment->appointment_time)->format('d M Y h:i A'),
                'Consultation Fee' => $bill->consultation_fee,
                'Medicine Fee' => $bill->medicine_fee,
                'Lab Fee' => $bill->lab_fee,
                'Total' => $bill->total,
                'Created At' => Carbon::parse($bill->created_at)->format('d M Y h:i A'),
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Doctor',
            'Appointment Date',
            'Consultation Fee',
            'Medicine Fee',
            'Lab Fee',
            'Total',
            'Created At',
        ];
    }
}

