<?php

namespace App\Services;

use Illuminate\Database\Connection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExternalVehicleService
{
    private ?Connection $connection = null;

    private function getConnection(): Connection
    {
        if ($this->connection === null) {
            $this->connection = DB::connection('external_mysql');
        }

        return $this->connection;
    }

    private function buildWhereClause(string $searchType, string $searchQuery): array
    {
        $allowed = [
            'vehicle_id' => 'vehicle.vehicle_id',
            'veh_chassis_number' => 'vehicle.veh_chassis_number',
        ];

        $key = strtolower($searchType);
        if (!array_key_exists($key, $allowed)) {
            $key = 'veh_chassis_number';
        }

        $column = $allowed[$key];

        return ["{$column} = ?", [$searchQuery]];
    }

    public function getVehicleDetails(string $searchType, string $searchQuery): array
    {
        [$where, $bindings] = $this->buildWhereClause($searchType, $searchQuery);

        $sql = "SELECT vehicle.vehicle_id as vehicle_id,
                make.make_name as make,
                model.model_name as model,
                vehicle.veh_chassis_model as chassis_model,
                vehicle.veh_chassis_number as chassis_number,
                vehicle.veh_cc,
                vehicle.veh_year,
                vehicle.veh_color,
                vehicle.veh_buy_date,
                vehicle.veh_auc_ship_number,
                vehicle.veh_net_weight,
                vehicle.veh_m3,
                vehicle.veh_l,
                vehicle.veh_h,
                vehicle.veh_w,
                vehicle.veh_n1,
                vehicle.veh_n2,
                vehicle.veh_n3,
                vehicle.veh_n4,
                vehicle.veh_buy_price,
                vehicle.yard_date_in,
                rikso.rikso_from_place_id,
                rikso.rikso_to_place_id,
                rikso.rikso_cost,
                rikso_company.rikso_company_name as rikso_company
            FROM tbl_vehicle AS vehicle
                LEFT JOIN tbl_vehicle_make AS make ON make.make_id = vehicle.make_id
                LEFT JOIN tbl_vehicle_model AS model ON model.model_id = vehicle.model_id
                LEFT JOIN tbl_vehicle_purchase AS purchase ON purchase.vehicle_id = vehicle.vehicle_id
                LEFT JOIN tbl_rikso_details AS rikso ON rikso.vehicle_id = vehicle.vehicle_id
                LEFT JOIN tbl_rikso_company AS rikso_company ON rikso_company.rikso_company_id = rikso.rikso_company_id
            WHERE {$where}";

        try {
            $connection = $this->getConnection();
            $rows = $connection->select($sql, $bindings);

            $vehicles = array_map(fn($r) => (array) $r, $rows);

            // Fetch images for each vehicle
            foreach ($vehicles as &$vehicle) {
                $vehicleId = $vehicle['vehicle_id'] ?? null;
                if ($vehicleId) {
                    $images = $connection->select('SELECT veh_image FROM tbl_vehicle_images WHERE vehicle_id = ?', [$vehicleId]);
                    $vehicle['images'] = array_map(fn($img) => 'https://senda.us/autocraft/avisnew/images/veh_images/' . $img->veh_image, $images);
                } else {
                    $vehicle['images'] = [];
                }
            }

            return $vehicles;
        } catch (QueryException $e) {
            Log::error('ExternalVehicleService query failed', [
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
