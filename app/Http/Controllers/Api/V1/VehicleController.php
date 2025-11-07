<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ExternalVehicleService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

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
     *     path="/api/v1/vehicles/search",
     *     summary="Search vehicles (avis_03_oct table)",
     *     description="Searches external vehicle database using header inputs",
     *     tags={"Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="search_type",
     *         in="query",
     *         description="Type of identifier (vehicle_id or veh_chassis_number)",
     *         required=true,
     *
     *         @OA\Schema(type="string", enum={"vehicle_id","veh_chassis_number"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="search_query",
     *         in="query",
     *         description="Value of the identifier to search, example 251144",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Search completed",
     *
     *         @OA\JsonContent(
     *             type="object",
     *             required={"success", "message", "data", "meta"},
     *
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Search completed"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="vehicles",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="vehicle_id", type="integer", example=251144),
     *                         @OA\Property(property="make", type="string", example="TOYOTA"),
     *                         @OA\Property(property="model", type="string", example="COROLLA AXIO"),
     *                         @OA\Property(property="chassis_model", type="string", example="NKE165"),
     *                         @OA\Property(property="chassis_number", type="string", example="7250165"),
     *                         @OA\Property(property="veh_cc", type="string", example="1490"),
     *                         @OA\Property(property="veh_year", type="string", example="2021"),
     *                         @OA\Property(property="veh_color", type="string", example="SILVER"),
     *                         @OA\Property(property="veh_buy_date", type="string", format="date", example="2025-10-09"),
     *                         @OA\Property(property="veh_auc_ship_number", type="string", example="2204"),
     *                         @OA\Property(property="veh_net_weight", type="string", example="1140"),
     *                         @OA\Property(property="veh_m3", type="string", nullable=true, example=null),
     *                         @OA\Property(property="veh_l", type="string", example="440"),
     *                         @OA\Property(property="veh_h", type="string", example="146"),
     *                         @OA\Property(property="veh_w", type="string", example="169"),
     *                         @OA\Property(property="veh_n1", type="string", example="名古屋"),
     *                         @OA\Property(property="veh_n2", type="string", example="508"),
     *                         @OA\Property(property="veh_n3", type="string", example="さ"),
     *                         @OA\Property(property="veh_n4", type="string", example="1410"),
     *                         @OA\Property(property="veh_buy_price", type="integer", example=1292000),
     *                         @OA\Property(property="yard_date_in", type="string", format="date", example="0000-00-00"),
     *                         @OA\Property(property="rikso_from_place_id", type="integer", example=165),
     *                         @OA\Property(property="rikso_to_place_id", type="integer", example=215),
     *                         @OA\Property(property="rikso_cost", type="integer", example=5000),
     *                         @OA\Property(property="rikso_company", type="string", example="EIKO SHOUN"),
     *                         @OA\Property(
     *                             property="images",
     *                             type="array",
     *
     *                             @OA\Items(
     *                                 type="string",
     *                                 format="uri",
     *                                 example="https://senda.us/autocraft/avisnew/images/veh_images/img_01760044939.png"
     *                             )
     *                         )
     *                     )
     *                 )
     *             ),
     *
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(
     *                     property="timestamp",
     *                     type="string",
     *                     format="date-time",
     *                     example="2025-11-07T01:58:04.676067Z"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function search(Request $request): JsonResponse
    {
        // Accept only query parameters for input
        $input = [
            'search_type' => $request->query('search_type'),
            'search_query' => $request->query('search_query'),
        ];

        $validator = Validator::make($input, [
            'search_type' => 'required|string|in:vehicle_id,veh_chassis_number',
            'search_query' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray(), 422);
        }

        // Get the user infor
        $user = $request->user();

        // Detect if request is from Android device
        $userAgent = $request->userAgent() ?? '';
        $isAndroid = stripos($userAgent, 'Android') !== false;

        // Log API request with user and device information
        Log::info('Vehicle Search API Request', [
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'user_name' => $user?->name,
            'is_android' => $isAndroid,
            'user_agent' => $userAgent,
            'ip_address' => $request->ip(),
            'search_type' => $input['search_type'],
            'search_query' => $input['search_query'],
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        $service = new ExternalVehicleService;

        try {
            $results = $service->getVehicleDetails(
                (string)$input['search_type'],
                (string)$input['search_query']
            );

            Log::info('Vehicle Search API Success', [
                'user_id' => $user?->id,
                'is_android' => $isAndroid,
                'results_count' => is_array($results) ? count($results) : 0,
                'search_type' => $input['search_type'],
                'search_query' => $input['search_query'],
            ]);

            return $this->successResponse('Search completed', [
                'vehicles' => $results,
            ]);
        } catch (QueryException $e) {
            Log::error('Vehicle Search API - Database Query Error', [
                'user_id' => $user?->id,
                'is_android' => $isAndroid,
                'error' => $e->getMessage(),
                'search_type' => $input['search_type'],
                'search_query' => $input['search_query'],
            ]);

            return $this->errorResponse('External database query failed', [
                'sql' => method_exists($e, 'getSql') ? $e->getSql() : null,
                'bindings' => method_exists($e, 'getBindings') ? $e->getBindings() : [],
                'error' => $e->getMessage(),
            ], 502);
        } catch (\Throwable $e) {
            Log::error('Vehicle Search API - General Error', [
                'user_id' => $user?->id,
                'is_android' => $isAndroid,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'search_type' => $input['search_type'],
                'search_query' => $input['search_query'],
            ]);

            return $this->errorResponse('External database error', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ], 502);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/vehicles/upload-images",
     *     summary="Upload vehicle images",
     *     description="Uploads images for a vehicle. Images are not stored yet, but the structure is ready for implementation.",
     *     tags={"Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 required={"vehicle_id", "images"},
     *
     *                 @OA\Property(
     *                     property="vehicle_id",
     *                     type="integer",
     *                     description="The vehicle ID",
     *                     example=251144
     *                 ),
     *                 @OA\Property(
     *                     property="images[]",
     *                     type="array",
     *                     description="Array of image files",
     *
     *                     @OA\Items(
     *                         type="string",
     *                         format="binary"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Images uploaded successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *             required={"success", "message", "data", "meta"},
     *
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Images uploaded successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="vehicle",
     *                     type="object",
     *                     @OA\Property(property="vehicle_id", type="integer", example=251144),
     *                     @OA\Property(
     *                         property="images",
     *                         type="array",
     *                         description="Array of image URLs including newly uploaded ones",
     *
     *                         @OA\Items(
     *                             type="string",
     *                             format="uri",
     *                             example="https://senda.us/autocraft/avisnew/images/veh_images/img_01760044939.png"
     *                         )
     *                     )
     *                 )
     *             ),
     *
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(
     *                     property="timestamp",
     *                     type="string",
     *                     format="date-time",
     *                     example="2025-11-07T01:58:04.676067Z"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vehicle not found"
     *     )
     * )
     */
    public function uploadImages(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|integer',
            'images' => 'required|array|min:1',
            'images.*' => 'required|file|image|max:2048', // max 2MB per image
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray(), 422);
        }

        $user = $request->user();

        Log::info('Vehicle Image Upload API Request', [
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'vehicle_id' => $request->vehicle_id,
            'images_count' => count($request->file('images', [])),
        ]);

        $service = new ExternalVehicleService;

        try {
            $results = $service->getVehicleDetails('vehicle_id', (string)$request->vehicle_id);

            if (empty($results)) {
                return $this->errorResponse('Vehicle not found', [], 404);
            }

            $vehicle = $results[0];

            $uploadedImages = [];
            $uploadedFiles = $request->file('images', []);

            foreach ($uploadedFiles as $index => $file) {
                $dummyFileName = 'uploaded_' . $request->vehicle_id . '_' . time() . '_' . ($index + 1) . '.' . $file->getClientOriginalExtension();
                $dummyUrl = 'https://senda.us/autocraft/avisnew/images/veh_images/uploaded/' . $dummyFileName;
                $uploadedImages[] = $dummyUrl;
            }

            $existingImages = $vehicle['images'] ?? [];
            $vehicle['images'] = array_merge($existingImages, $uploadedImages);

            return $this->successResponse('Images uploaded successfully', [
                'vehicle' => $vehicle,
            ]);

        } catch (QueryException $e) {

            Log::error('Vehicle Image Upload API - Database Query Error', [
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
                'vehicle_id' => $request->vehicle_id,
            ]);

            return $this->errorResponse('External database query failed', [
                'error' => $e->getMessage(),
            ], 502);

        } catch (Throwable $e) {

            Log::error('Vehicle Image Upload API - General Error', [
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'vehicle_id' => $request->vehicle_id,
            ]);

            return $this->errorResponse('Failed to upload images', [
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
