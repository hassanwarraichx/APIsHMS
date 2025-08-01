<?php

namespace App\DTOs\MedicineDTO;

use App\DTOs\BaseDTO;

class CreateMedicineDTO extends BaseDTO
{
    public string $name;
    public string $brand;
    public int $stock;
    public string $expiry_date;
    public float $price;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->brand = $data['brand'];
        $this->stock = $data['stock'];
        $this->expiry_date = $data['expiry_date'];
        $this->price = $data['price'];
    }

}
