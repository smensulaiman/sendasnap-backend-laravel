<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ExternalVehicleService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
     *     path="/api/v1/vehicles/search",
     *     summary="Search vehicles (senda.us avis_03_oct table)",
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
     *         description="Search completed"
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
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        // Get authenticated user (works with bearer token from Android/iOS/any client)
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
                (string) $input['search_type'],
                (string) $input['search_query']
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
}
