<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ExternalVehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     *         in="header",
     *         description="Type of identifier (vehicle_id or veh_chassis_number)",
     *         required=true,
     *
     *         @OA\Schema(type="string", enum={"vehicle_id","veh_chassis_number"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="search_query",
     *         in="header",
     *         description="Value of the identifier to search",
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
        $input = [
            'search_type' => $request->header('search_type'),
            'search_query' => $request->header('search_query'),
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

        $service = new ExternalVehicleService;

        $results = $service->getVehicleDetails(
            (string) $input['search_type'],
            (string) $input['search_query']
        );

        return $this->successResponse('Search completed', [
            'vehicles' => $results,
        ]);
    }
}
