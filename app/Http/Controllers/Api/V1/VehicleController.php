<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use App\Models\ConsigneeDetail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    /**
     * Display a listing of vehicles
     */
    public function index(Request $request): JsonResponse
    {
        $query = Vehicle::with(['creator', 'photos', 'consigneeDetails']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('chassis_model', 'like', "%{$search}%")
                    ->orWhere('plate_number', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by make
        if ($request->has('make')) {
            $query->where('make', $request->get('make'));
        }

        // Filter by year range
        if ($request->has('year_from')) {
            $query->where('year', '>=', $request->get('year_from'));
        }
        if ($request->has('year_to')) {
            $query->where('year', '<=', $request->get('year_to'));
        }

        $vehicles = $query->paginate($request->get('per_page', 15));

        return $this->successResponse('Vehicles retrieved successfully', [
            'vehicles' => $vehicles->items(),
            'pagination' => [
                'current_page' => $vehicles->currentPage(),
                'last_page' => $vehicles->lastPage(),
                'per_page' => $vehicles->perPage(),
                'total' => $vehicles->total(),
            ],
        ]);
    }

    /**
     * Store a newly created vehicle
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'serial_number' => 'required|string|unique:vehicles',
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'chassis_model' => 'required|string|max:255',
            'cc' => 'required|integer|min:0',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'color' => 'required|string|max:100',
            'vehicle_buy_date' => 'required|date',
            'auction_ship_number' => 'required|string|max:255',
            'net_weight' => 'required|numeric|min:0',
            'area' => 'required|string|max:255',
            'length' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'plate_number' => 'nullable|string|max:20',
            'buying_price' => 'required|numeric|min:0',
            'expected_yard_date' => 'required|date',
            'rikso_from' => 'nullable|string|max:255',
            'rikso_to' => 'nullable|string|max:255',
            'rikso_cost' => 'nullable|numeric|min:0',
            'rikso_company' => 'nullable|string|max:255',
            'auction_sheet' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'tohon_copy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'status' => 'sometimes|in:pending,in_yard,ready,sold',
            // Consignee details
            'consignee_name' => 'required|string|max:255',
            'consignee_address' => 'required|string',
            'consignee_phone' => 'required|string|max:20',
            'consignee_email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }

        $vehicleData = $request->except(['consignee_name', 'consignee_address', 'consignee_phone', 'consignee_email', 'auction_sheet', 'tohon_copy']);
        $vehicleData['created_by'] = auth()->id();

        // Handle file uploads
        if ($request->hasFile('auction_sheet')) {
            $vehicleData['auction_sheet'] = $request->file('auction_sheet')->store('documents', 'public');
        }
        if ($request->hasFile('tohon_copy')) {
            $vehicleData['tohon_copy'] = $request->file('tohon_copy')->store('documents', 'public');
        }

        $vehicle = Vehicle::create($vehicleData);

        // Create consignee details
        $vehicle->consigneeDetails()->create([
            'name' => $request->consignee_name,
            'address' => $request->consignee_address,
            'phone' => $request->consignee_phone,
            'email' => $request->consignee_email,
        ]);

        $vehicle->load(['creator', 'photos', 'consigneeDetails']);

        return $this->successResponse('Vehicle created successfully', [
            'vehicle' => $vehicle,
        ], 201);
    }

    /**
     * Display the specified vehicle
     */
    public function show(Vehicle $vehicle): JsonResponse
    {
        $vehicle->load(['creator', 'photos', 'consigneeDetails', 'tasks']);

        return $this->successResponse('Vehicle retrieved successfully', [
            'vehicle' => $vehicle,
        ]);
    }

    /**
     * Update the specified vehicle
     */
    public function update(Request $request, Vehicle $vehicle): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'serial_number' => 'sometimes|string|unique:vehicles,serial_number,' . $vehicle->id,
            'make' => 'sometimes|string|max:255',
            'model' => 'sometimes|string|max:255',
            'chassis_model' => 'sometimes|string|max:255',
            'cc' => 'sometimes|integer|min:0',
            'year' => 'sometimes|integer|min:1900|max:' . date('Y'),
            'color' => 'sometimes|string|max:100',
            'vehicle_buy_date' => 'sometimes|date',
            'auction_ship_number' => 'sometimes|string|max:255',
            'net_weight' => 'sometimes|numeric|min:0',
            'area' => 'sometimes|string|max:255',
            'length' => 'sometimes|numeric|min:0',
            'width' => 'sometimes|numeric|min:0',
            'height' => 'sometimes|numeric|min:0',
            'plate_number' => 'nullable|string|max:20',
            'buying_price' => 'sometimes|numeric|min:0',
            'expected_yard_date' => 'sometimes|date',
            'rikso_from' => 'nullable|string|max:255',
            'rikso_to' => 'nullable|string|max:255',
            'rikso_cost' => 'nullable|numeric|min:0',
            'rikso_company' => 'nullable|string|max:255',
            'auction_sheet' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'tohon_copy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'status' => 'sometimes|in:pending,in_yard,ready,sold',
            // Consignee details
            'consignee_name' => 'sometimes|string|max:255',
            'consignee_address' => 'sometimes|string',
            'consignee_phone' => 'sometimes|string|max:20',
            'consignee_email' => 'sometimes|email|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }

        $updateData = $request->except(['consignee_name', 'consignee_address', 'consignee_phone', 'consignee_email', 'auction_sheet', 'tohon_copy']);

        // Handle file uploads
        if ($request->hasFile('auction_sheet')) {
            if ($vehicle->auction_sheet) {
                Storage::disk('public')->delete($vehicle->auction_sheet);
            }
            $updateData['auction_sheet'] = $request->file('auction_sheet')->store('documents', 'public');
        }
        if ($request->hasFile('tohon_copy')) {
            if ($vehicle->tohon_copy) {
                Storage::disk('public')->delete($vehicle->tohon_copy);
            }
            $updateData['tohon_copy'] = $request->file('tohon_copy')->store('documents', 'public');
        }

        $vehicle->update($updateData);

        // Update consignee details if provided
        if ($request->hasAny(['consignee_name', 'consignee_address', 'consignee_phone', 'consignee_email'])) {
            $consigneeData = $request->only(['consignee_name', 'consignee_address', 'consignee_phone', 'consignee_email']);
            $vehicle->consigneeDetails()->updateOrCreate(
                ['vehicle_id' => $vehicle->id],
                $consigneeData
            );
        }

        $vehicle->load(['creator', 'photos', 'consigneeDetails']);

        return $this->successResponse('Vehicle updated successfully', [
            'vehicle' => $vehicle,
        ]);
    }

    /**
     * Remove the specified vehicle
     */
    public function destroy(Vehicle $vehicle): JsonResponse
    {
        // Delete associated files
        if ($vehicle->auction_sheet) {
            Storage::disk('public')->delete($vehicle->auction_sheet);
        }
        if ($vehicle->tohon_copy) {
            Storage::disk('public')->delete($vehicle->tohon_copy);
        }

        // Delete photos
        foreach ($vehicle->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $vehicle->delete();

        return $this->successResponse('Vehicle deleted successfully');
    }

    /**
     * Upload photo for vehicle
     */
    public function uploadPhoto(Request $request, Vehicle $vehicle): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'photo_type' => 'required|in:exterior,interior,engine,document,other',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }

        $photoPath = $request->file('photo')->store('vehicle-photos', 'public');

        $photo = $vehicle->photos()->create([
            'photo_path' => $photoPath,
            'photo_type' => $request->photo_type,
            'uploaded_by' => auth()->id(),
        ]);

        return $this->successResponse('Photo uploaded successfully', [
            'photo' => $photo,
        ], 201);
    }

    /**
     * Delete photo from vehicle
     */
    public function deletePhoto(Vehicle $vehicle, VehiclePhoto $photo): JsonResponse
    {
        if ($photo->vehicle_id !== $vehicle->id) {
            return $this->errorResponse('Photo not found for this vehicle', [], 404);
        }

        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return $this->successResponse('Photo deleted successfully');
    }

    /**
     * Search vehicles
     */
    public function search(Request $request): JsonResponse
    {
        $query = Vehicle::with(['creator', 'photos', 'consigneeDetails']);

        if ($request->has('q')) {
            $search = $request->get('q');
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('chassis_model', 'like', "%{$search}%")
                    ->orWhere('plate_number', 'like', "%{$search}%");
            });
        }

        $vehicles = $query->limit(20)->get();

        return $this->successResponse('Search completed', [
            'vehicles' => $vehicles,
        ]);
    }

    /**
     * Get vehicle statistics
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_vehicles' => Vehicle::count(),
            'by_status' => Vehicle::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'by_make' => Vehicle::selectRaw('make, count(*) as count')
                ->groupBy('make')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->pluck('count', 'make'),
            'by_year' => Vehicle::selectRaw('year, count(*) as count')
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->limit(10)
                ->pluck('count', 'year'),
        ];

        return $this->successResponse('Statistics retrieved successfully', [
            'stats' => $stats,
        ]);
    }

    /**
     * Success response helper
     */
    private function successResponse(string $message, array $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'timestamp' => now()->toISOString(),
            ],
        ], $status);
    }

    /**
     * Error response helper
     */
    private function errorResponse(string $message, array $errors = [], int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => [
                'timestamp' => now()->toISOString(),
            ],
        ], $status);
    }
}
