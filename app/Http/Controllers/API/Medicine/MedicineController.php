<?php

namespace App\Http\Controllers\API\Medicine;

use App\DTOs\DoctorDTO\CreateDoctorDTO;
use App\DTOs\MedicineDTO\CreateMedicineDTO;
use App\DTOs\MedicineDTO\UpdateMedicineDTO;
use App\Exports\MedicineExport;
use App\Filters\Medicine\BrandFilter;
use App\Filters\Medicine\ExpiryDateFilter;
use App\Filters\Medicine\PriceFilter;
use App\Filters\Medicine\StockFilter;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Medicine\StoreMedicineRequest;
use App\Http\Resources\Medicine\MedicineResource;
use App\Models\Medicine;
use App\Services\Medicine\MedicineService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Maatwebsite\Excel\Facades\Excel;

class MedicineController extends Controller
{
    protected MedicineService $medicineService;

    public function __construct(MedicineService $medicineService)
    {
        $this->medicineService = $medicineService;
    }

    public function index(Request $request)
    {
        $query = app(Pipeline::class)
            ->send(Medicine::query())
            ->through([
                BrandFilter::class,
                StockFilter::class,
                ExpiryDateFilter::class,
                PriceFilter::class,
            ])
            ->thenReturn();

        $perPage = $request->get('per_page', 10);

        return ResponseHelper::success(
            MedicineResource::collection($query->paginate($perPage)),
            'Filtered medicine list.'
        );
    }

    public function store(StoreMedicineRequest $request)
    {
        $dto = new CreateMedicineDTO($request->validated());
        $medicine = $this->medicineService->create($dto);

        return ResponseHelper::success(new MedicineResource($medicine), 'Medicine created successfully.');
    }

    public function show($id)
    {
        try {
            $medicine = Medicine::findOrFail($id);
            return ResponseHelper::success(new MedicineResource($medicine), 'Medicine fetched successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Medicine not found.', 404);
        }
    }

    public function update(StoreMedicineRequest $request, $id)
    {
        try {
            $medicine = Medicine::findOrFail($id);
            $dto = new UpdateMedicineDTO($request->validated());
            $updated = $this->medicineService->update($medicine, $dto);

            return ResponseHelper::success(new MedicineResource($updated), 'Medicine updated successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Medicine not found.', 404);
        }
    }

    public function destroy($id)
    {
        try {
            $medicine = Medicine::findOrFail($id);
            $this->medicineService->delete($medicine);
            return ResponseHelper::success(null, 'Medicine deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Medicine not found.', 404);
        }
    }

    public function export(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new MedicineExport, 'inventory_report.xlsx');
    }
}
