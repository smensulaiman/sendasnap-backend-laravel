<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'serial_number' => 'VH' . $this->faker->unique()->numberBetween(1000, 9999),
            'make' => $this->faker->randomElement(['Toyota', 'Honda', 'Nissan', 'Mazda', 'Subaru']),
            'model' => $this->faker->randomElement(['Camry', 'Civic', 'Altima', 'CX-5', 'Outback']),
            'chassis_model' => $this->faker->bothify('??##'),
            'cc' => $this->faker->numberBetween(1000, 3000),
            'year' => $this->faker->numberBetween(2015, 2024),
            'color' => $this->faker->randomElement(['White', 'Black', 'Silver', 'Red', 'Blue']),
            'vehicle_buy_date' => $this->faker->date(),
            'auction_ship_number' => 'AS' . $this->faker->numberBetween(100, 999),
            'net_weight' => $this->faker->randomFloat(2, 1000, 2000),
            'area' => $this->faker->randomElement(['Tokyo', 'Osaka', 'Nagoya', 'Yokohama']),
            'length' => $this->faker->randomFloat(2, 4.0, 5.0),
            'width' => $this->faker->randomFloat(2, 1.7, 1.9),
            'height' => $this->faker->randomFloat(2, 1.4, 1.6),
            'plate_number' => $this->faker->bothify('???-####'),
            'buying_price' => $this->faker->randomFloat(2, 15000, 50000),
            'expected_yard_date' => $this->faker->date(),
            'rikso_from' => $this->faker->city() . ' Port',
            'rikso_to' => $this->faker->city() . ' Yard',
            'rikso_cost' => $this->faker->randomFloat(2, 300, 800),
            'rikso_company' => 'Rikso Transport',
            'status' => $this->faker->randomElement(['pending', 'in_yard', 'ready', 'sold']),
            'created_by' => User::factory(),
        ];
    }
}