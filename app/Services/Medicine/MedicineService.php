<?php

namespace App\Services\Medicine;

use App\DTOs\MedicineDTO\CreateMedicineDTO;
use App\DTOs\MedicineDTO\UpdateMedicineDTO;
use App\Models\Medicine;

class MedicineService
{
    public function create(CreateMedicineDTO $dto): Medicine
    {
        return Medicine::create($dto->toArray());

    }
    public function update(Medicine $medicine, UpdateMedicineDTO $dto): Medicine
    {
        $updateData = [];

        if (!is_null($dto->name)) {
            $updateData['name'] = $dto->name;
        }

        if (!is_null($dto->brand)) {
            $updateData['brand'] = $dto->brand;
        }

        if (!is_null($dto->stock)) {
            $updateData['stock'] = $dto->stock;
        }

        if (!is_null($dto->expiry_date)) {
            $updateData['expiry_date'] = $dto->expiry_date;
        }

        if (!is_null($dto->price)) {
            $updateData['price'] = $dto->price;
        }

        $medicine->update($updateData);

        return $medicine;
    }


    public function delete(Medicine $medicine): void
    {
        $medicine->delete();
    }

    public function lowStock(int $threshold = 10)
    {
        return Medicine::where('stock', '<', $threshold)->get();
    }


    public function nearExpiry(int $days = 30)
    {
        return Medicine::whereBetween('expiry_date', [now(), now()->addDays($days)])->get();
    }

    public function all()
    {
        return Medicine::latest()->get();
    }

    public function find(int $id): ?Medicine
    {
        return Medicine::find($id);
    }
}
