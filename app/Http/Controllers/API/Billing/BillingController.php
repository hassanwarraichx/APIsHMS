<?php

namespace App\Http\Controllers\API\Billing;

use App\DTOs\BillingDTO\CreateBillingDTO;
use App\Exports\SingleBillExport;
use App\Exports\SinglePrescriptionExport;
use App\Filters\Billing\PendingBillingFilter;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\StoreBillRequest;
use App\Http\Resources\Billing\BillingResource;
use App\Models\Appointment;
use App\Services\Billing\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Maatwebsite\Excel\Facades\Excel;

class BillingController extends Controller
{
    protected BillingService $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    public function pendingAppointments(Request $request): JsonResponse
    {
        $query = app(Pipeline::class)
            ->send(Appointment::with(['patient.user', 'doctor.user', 'prescription']))
            ->through([
                PendingBillingFilter::class,
            ])
            ->thenReturn();

        $perPage = $request->get('per_page', 10);

        return ResponseHelper::success(
            BillingResource::collection($query->paginate($perPage)),
            'Appointments pending billing fetched successfully.'
        );
    }

    public function store(StoreBillRequest $request, Appointment $appointment): JsonResponse
    {
        try {
            $data= $request->all();
            $dto = new CreateBillingDTO($data);
            $bill = $this->billingService->createBill($appointment, $dto);

            return ResponseHelper::success(
                new BillingResource($bill),
                'Bill generated successfully.',
                201
            );
        } catch (\Throwable $e) {
            return ResponseHelper::error('Failed to generate bill.', 500, $e->getMessage());
        }
    }

    public function exportBill(Appointment $appointment)
    {
        return Excel::download(new SingleBillExport($appointment), 'bill_' . $appointment->id . '.xlsx');
    }

    public function exportPrescription(Appointment $appointment)
    {
        return Excel::download(new SinglePrescriptionExport($appointment), 'prescription_' . $appointment->id . '.xlsx');
    }
}
