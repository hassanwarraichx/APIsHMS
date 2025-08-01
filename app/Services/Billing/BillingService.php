<?php

namespace App\Services\Billing;

use App\DTOs\BillingDTO\CreateBillingDTO;
use App\Models\Appointment;
use App\Models\Bill;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function createBill(Appointment $appointment, CreateBillingDTO $dto): Bill
    {
        return DB::transaction(function () use ($appointment, $dto) {
            return $appointment->bill()->create(array_merge(
                $dto->toArray(),
                ['total' => $dto->total()]
            ));
        });
    }



}
