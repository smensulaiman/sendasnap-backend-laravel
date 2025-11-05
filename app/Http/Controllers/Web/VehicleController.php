<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ExternalVehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
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
            $request->string('search_type'),
            $request->string('search_query')
        );

        return response()->json([
            'success' => true,
            'message' => 'Search completed',
            'data' => [
                'vehicles' => $results,
            ],
        ]);
    }
}
