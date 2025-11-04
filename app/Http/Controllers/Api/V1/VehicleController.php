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

/**
 * @OA\Tag(
 *     name="Vehicles",
 *     description="API endpoints for vehicle management"
 * )
 */
class VehicleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/vehicles",
     *     summary="List vehicles",
     *     description="Get a paginated list of vehicles with optional filtering",
     *     tags={"Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by serial number, make, model, chassis model, or plate number",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending","in_yard","ready","sold"})
     *     ),
     *     @OA\Parameter(
     *         name="make",
     *         in="query",
     *         description="Filter by make",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Vehicles retrieved successfully"
     *     )
     * )
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

        $transformedVehicles = $vehicles->getCollection()->map(function ($vehicle) {
            return $this->transformVehicle($vehicle);
        });

        return $this->successResponse('Vehicles retrieved successfully', [
            'vehicles' => $transformedVehicles,
            'pagination' => [
                'current_page' => $vehicles->currentPage(),
                'last_page' => $vehicles->lastPage(),
                'per_page' => $vehicles->perPage(),
                'total' => $vehicles->total(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/vehicles",
     *     summary="Create vehicle",
     *     description="Create a new vehicle with consignee details",
     *     tags={"Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"serial_number","make","model","chassis_model","cc","year","color","vehicle_buy_date","auction_ship_number","net_weight","area","length","width","height","buying_price","expected_yard_date","consignee_name","consignee_address","consignee_phone","consignee_email"},
     *             @OA\Property(property="serial_number", type="string", example="SN123456"),
     *             @OA\Property(property="make", type="string", example="Toyota"),
     *             @OA\Property(property="model", type="string", example="Camry"),
     *             @OA\Property(property="chassis_model", type="string", example="CH-123"),
     *             @OA\Property(property="cc", type="integer", example=2000),
     *             @OA\Property(property="year", type="integer", example=2020),
     *             @OA\Property(property="color", type="string", example="Black"),
     *             @OA\Property(property="vehicle_buy_date", type="string", format="date", example="2024-01-01"),
     *             @OA\Property(property="auction_ship_number", type="string", example="SHIP-001"),
     *             @OA\Property(property="net_weight", type="number", example=1500.5),
     *             @OA\Property(property="area", type="string", example="Tokyo"),
     *             @OA\Property(property="length", type="number", example=4.5),
     *             @OA\Property(property="width", type="number", example=1.8),
     *             @OA\Property(property="height", type="number", example=1.5),
     *             @OA\Property(property="plate_number", type="string", example="ABC-123"),
     *             @OA\Property(property="buying_price", type="number", example=25000.00),
     *             @OA\Property(property="expected_yard_date", type="string", format="date", example="2024-02-01"),
     *             @OA\Property(property="consignee_name", type="string", example="John Doe"),
     *             @OA\Property(property="consignee_address", type="string", example="123 Main St"),
     *             @OA\Property(property="consignee_phone", type="string", example="+1234567890"),
     *             @OA\Property(property="consignee_email", type="string", format="email", example="john@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Vehicle created successfully"
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
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
            'vehicle' => $this->transformVehicle($vehicle),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/vehicles/{id}",
     *     summary="Get vehicle",
     *     description="Get a specific vehicle by ID",
     *     tags={"Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Vehicle ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Vehicle retrieved successfully"
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Vehicle not found"
     *     )
     * )
     */
    public function show(Vehicle $vehicle): JsonResponse
    {
        $vehicle->load(['creator', 'photos', 'consigneeDetails', 'tasks']);

        return $this->successResponse('Vehicle retrieved successfully', [
            'vehicle' => $this->transformVehicle($vehicle),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/vehicles/{id}",
     *     summary="Update vehicle",
     *     description="Update an existing vehicle",
     *     tags={"Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Vehicle ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="serial_number", type="string", example="SN123456"),
     *             @OA\Property(property="make", type="string", example="Toyota"),
     *             @OA\Property(property="status", type="string", enum={"pending","in_yard","ready","sold"})
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Vehicle updated successfully"
     *     )
     * )
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
            'vehicle' => $this->transformVehicle($vehicle),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/vehicles/{id}",
     *     summary="Delete vehicle",
     *     description="Delete a vehicle and all associated files",
     *     tags={"Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Vehicle ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Vehicle deleted successfully"
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/v1/vehicles/{id}/photos",
     *     summary="Upload vehicle photo",
     *     description="Upload a photo for a vehicle",
     *     tags={"Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Vehicle ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"photo","photo_type"},
     *                 @OA\Property(property="photo", type="string", format="binary"),
     *                 @OA\Property(property="photo_type", type="string", enum={"exterior","interior","engine","document","other"})
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Photo uploaded successfully"
     *     )
     * )
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

        $photo->load('uploader');

        return $this->successResponse('Photo uploaded successfully', [
            'photo' => [
                'id' => $photo->id,
                'vehicle_id' => $photo->vehicle_id,
                'photo_path' => $photo->photo_path,
                'photo_url' => Storage::disk('public')->url($photo->photo_path),
                'photo_type' => $photo->photo_type,
                'uploaded_by' => $photo->uploaded_by,
                'uploader' => $photo->uploader ? [
                    'id' => $photo->uploader->id,
                    'name' => $photo->uploader->name,
                ] : null,
                'created_at' => $photo->created_at,
                'updated_at' => $photo->updated_at,
            ],
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/vehicles/{id}/photos/{photo}",
     *     summary="Delete vehicle photo",
     *     description="Delete a photo from a vehicle",
     *     tags={"Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Vehicle ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="photo",
     *         in="path",
     *         description="Photo ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Photo deleted successfully"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/v1/vehicles/search",
     *     summary="Search vehicles",
     *     description="Search vehicles by query string",
     *     tags={"Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Search completed"
     *     )
     * )
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
            'vehicles' => $vehicles->map(function ($vehicle) {
                return $this->transformVehicle($vehicle);
            }),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/vehicles/stats",
     *     summary="Get vehicle statistics",
     *     description="Get statistics about vehicles",
     *     tags={"Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Statistics retrieved successfully"
     *     )
     * )
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
     * Transform vehicle data with full photo URLs
     */
    private function transformVehicle(Vehicle $vehicle): array
    {
        $vehicleArray = $vehicle->toArray();
        
        // Transform photos to include full URLs
        if (isset($vehicleArray['photos']) && is_array($vehicleArray['photos'])) {
            $vehicleArray['photos'] = array_map(function ($photo) {
                return [
                    'id' => $photo['id'],
                    'vehicle_id' => $photo['vehicle_id'],
                    'photo_path' => $photo['photo_path'],
                    'photo_url' => Storage::disk('public')->url($photo['photo_path']),
                    'photo_type' => $photo['photo_type'],
                    'uploaded_by' => $photo['uploaded_by'],
                    'created_at' => $photo['created_at'],
                    'updated_at' => $photo['updated_at'],
                ];
            }, $vehicleArray['photos']);
        }
        
        // Transform document URLs if they exist
        if (isset($vehicleArray['auction_sheet']) && $vehicleArray['auction_sheet']) {
            $vehicleArray['auction_sheet_url'] = Storage::disk('public')->url($vehicleArray['auction_sheet']);
        }
        if (isset($vehicleArray['tohon_copy']) && $vehicleArray['tohon_copy']) {
            $vehicleArray['tohon_copy_url'] = Storage::disk('public')->url($vehicleArray['tohon_copy']);
        }
        
        return $vehicleArray;
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
