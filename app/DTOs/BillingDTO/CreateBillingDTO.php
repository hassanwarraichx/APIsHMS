<?php

namespace App\DTOs\BillingDTO;

use App\DTOs\BaseDTO;

class CreateBillingDTO extends BaseDTO
{
    public float $consultation_fee;
    public ?float $medicine_fee = 0;
    public ?float $lab_fee = 0;

    public function __construct(array $data){
        $this->consultation_fee = $data['consultation_fee'];
        $this->medicine_fee = $data['medicine_fee'];
        $this->lab_fee = $data['lab_fee'];

    }

    public function total(): float
    {
        return $this->consultation_fee + $this->medicine_fee + $this->lab_fee;
    }




}
