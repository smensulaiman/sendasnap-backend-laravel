<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_can_get_vehicles_list()
    {
        Vehicle::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/v1/vehicles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'vehicles',
                    'pagination'
                ]
            ]);
    }

    public function test_can_create_vehicle()
    {
        $vehicleData = [
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
            'status' => 'pending',
            'consignee_name' => 'Test Consignee',
            'consignee_address' => '123 Test Street',
            'consignee_phone' => '+1234567890',
            'consignee_email' => 'test@example.com',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/v1/vehicles', $vehicleData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'vehicle'
                ]
            ]);

        $this->assertDatabaseHas('vehicles', [
            'serial_number' => 'VH001',
            'make' => 'Toyota',
        ]);
    }

    public function test_can_get_vehicle_stats()
    {
        Vehicle::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/v1/vehicles/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'stats'
                ]
            ]);
    }
}
