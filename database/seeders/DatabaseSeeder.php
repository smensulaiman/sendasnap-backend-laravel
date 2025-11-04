<?php

namespace Database\Seeders;

use App\Models\ConsigneeDetail;
use App\Models\Task;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Sulaiman',
            'email' => 'sulaiman@sendasnap.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1234567890',
        ]);

        // Create manager user
        $manager = User::create([
            'name' => 'Shiroyama',
            'email' => 'acj.shiroyama@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'phone' => '+1234567891',
        ]);

        // Create employee users
        $employee1 = User::create([
            'name' => 'Kasahara',
            'email' => 'acj.document@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'phone' => '+1234567892',
        ]);

        $employee2 = User::create([
            'name' => 'Akunova Alisa',
            'email' => 'acjl.infomation@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'phone' => '+1234567893',
        ]);

        // Create sample vehicles
        $vehicles = [
            [
                'serial_number' => 'VH001',
                'make' => 'Toyota',
                'model' => 'Camry',
                'chassis_model' => 'ACV40',
                'cc' => 2400,
                'year' => 2020,
                'color' => 'White',
                'vehicle_buy_date' => '2024-01-15',
                'auction_ship_number' => 'AS001',
                'net_weight' => 1500.50,
                'area' => 'Tokyo',
                'length' => 4.8,
                'width' => 1.8,
                'height' => 1.5,
                'plate_number' => 'ABC-123',
                'buying_price' => 25000.00,
                'expected_yard_date' => '2024-02-01',
                'rikso_from' => 'Tokyo Port',
                'rikso_to' => 'Yokohama Yard',
                'rikso_cost' => 500.00,
                'rikso_company' => 'Rikso Transport',
                'status' => 'in_yard',
                'created_by' => $admin->id,
            ],
            [
                'serial_number' => 'VH002',
                'make' => 'Honda',
                'model' => 'Civic',
                'chassis_model' => 'FC1',
                'cc' => 1500,
                'year' => 2019,
                'color' => 'Black',
                'vehicle_buy_date' => '2024-01-20',
                'auction_ship_number' => 'AS002',
                'net_weight' => 1300.25,
                'area' => 'Osaka',
                'length' => 4.6,
                'width' => 1.8,
                'height' => 1.4,
                'plate_number' => 'DEF-456',
                'buying_price' => 20000.00,
                'expected_yard_date' => '2024-02-05',
                'rikso_from' => 'Osaka Port',
                'rikso_to' => 'Kobe Yard',
                'rikso_cost' => 400.00,
                'rikso_company' => 'Rikso Transport',
                'status' => 'ready',
                'created_by' => $manager->id,
            ],
            [
                'serial_number' => 'VH003',
                'make' => 'Nissan',
                'model' => 'Altima',
                'chassis_model' => 'L33',
                'cc' => 2500,
                'year' => 2021,
                'color' => 'Silver',
                'vehicle_buy_date' => '2024-01-25',
                'auction_ship_number' => 'AS003',
                'net_weight' => 1600.75,
                'area' => 'Nagoya',
                'length' => 4.9,
                'width' => 1.8,
                'height' => 1.5,
                'plate_number' => 'GHI-789',
                'buying_price' => 28000.00,
                'expected_yard_date' => '2024-02-10',
                'rikso_from' => 'Nagoya Port',
                'rikso_to' => 'Toyota Yard',
                'rikso_cost' => 600.00,
                'rikso_company' => 'Rikso Transport',
                'status' => 'pending',
                'created_by' => $admin->id,
            ],
        ];

        foreach ($vehicles as $vehicleData) {
            $vehicle = Vehicle::create($vehicleData);

            // Create consignee details for each vehicle
            ConsigneeDetail::create([
                'vehicle_id' => $vehicle->id,
                'name' => 'Sample Consignee '.$vehicle->serial_number,
                'address' => '123 Sample Street, Sample City, Sample Country',
                'phone' => '+1234567890',
                'email' => 'consignee'.$vehicle->id.'@example.com',
            ]);

            // Create sample photos for each vehicle
            VehiclePhoto::create([
                'vehicle_id' => $vehicle->id,
                'photo_path' => 'sample-photos/vehicle-'.$vehicle->id.'-1.jpg',
                'photo_type' => 'exterior',
                'uploaded_by' => $admin->id,
            ]);

            VehiclePhoto::create([
                'vehicle_id' => $vehicle->id,
                'photo_path' => 'sample-photos/vehicle-'.$vehicle->id.'-2.jpg',
                'photo_type' => 'interior',
                'uploaded_by' => $admin->id,
            ]);
        }

        // Create sample tasks
        $tasks = [
            [
                'title' => 'Inspection and Documentation',
                'description' => 'Complete thorough inspection of vehicle and prepare all necessary documentation.',
                'work_date' => '2024-02-01',
                'work_time' => '09:00',
                'status' => 'pending',
                'priority' => 'high',
                'vehicle_id' => 1,
                'assigned_to' => $employee1->id,
                'created_by' => $manager->id,
                'due_date' => '2024-02-03',
            ],
            [
                'title' => 'Cleaning and Detailing',
                'description' => 'Clean and detail the vehicle interior and exterior.',
                'work_date' => '2024-02-02',
                'work_time' => '10:00',
                'status' => 'running',
                'priority' => 'medium',
                'vehicle_id' => 2,
                'assigned_to' => $employee2->id,
                'created_by' => $manager->id,
                'due_date' => '2024-02-04',
            ],
            [
                'title' => 'Quality Check',
                'description' => 'Perform final quality check before vehicle is ready for sale.',
                'work_date' => '2024-02-03',
                'work_time' => '14:00',
                'status' => 'completed',
                'priority' => 'urgent',
                'vehicle_id' => 2,
                'assigned_to' => $employee1->id,
                'created_by' => $admin->id,
                'due_date' => '2024-02-03',
                'completed_at' => now(),
            ],
            [
                'title' => 'Paperwork Processing',
                'description' => 'Process all necessary paperwork for vehicle registration.',
                'work_date' => '2024-02-04',
                'work_time' => '11:00',
                'status' => 'pending',
                'priority' => 'medium',
                'vehicle_id' => 3,
                'assigned_to' => $employee2->id,
                'created_by' => $manager->id,
                'due_date' => '2024-02-06',
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }

        // Create tasks for today for quick demo (no factory required)
        Task::create([
            'title' => 'Today: Yard Audit',
            'description' => 'Perform quick audit of vehicles in yard',
            'work_date' => now()->toDateString(),
            'work_time' => '10:30',
            'status' => 'pending',
            'priority' => 'medium',
            'vehicle_id' => 1,
            'assigned_to' => $employee1->id,
            'created_by' => $manager->id,
            'due_date' => now()->toDateString(),
        ]);

        Task::create([
            'title' => 'Today: Wash & Prep',
            'description' => 'Wash the vehicle and prepare for photos',
            'work_date' => now()->toDateString(),
            'work_time' => '13:00',
            'status' => 'running',
            'priority' => 'high',
            'vehicle_id' => 2,
            'assigned_to' => $employee2->id,
            'created_by' => $admin->id,
            'due_date' => now()->toDateString(),
        ]);

        // Generate a personal access token for the admin for API testing
        $token = $admin->createToken('seeded-ui-token')->plainTextToken;
        file_put_contents(storage_path('app/seeded_token.txt'), $token);
    }
}
