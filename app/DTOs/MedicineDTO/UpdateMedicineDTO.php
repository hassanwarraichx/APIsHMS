<?php

namespace App\DTOs\MedicineDTO;

use App\DTOs\BaseDTO;

class UpdateMedicineDTO extends BaseDTO
{
    public ?string $name;
    public ?string $brand;
    public ?int $stock;
    public ?string $expiry_date;
    public ?float $price;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? null;
        $this->brand = $data['brand'] ?? null;
        $this->stock = $data['stock'] ?? null;
        $this->expiry_date = $data['expiry_date'] ?? null;
        $this->price = $data['price'] ?? null;
    }
}
