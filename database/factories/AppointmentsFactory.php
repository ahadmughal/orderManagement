<?php

namespace Database\Factories;

use App\Models\Appointments;
use App\Models\Clients;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointments>
 */
class AppointmentsFactory extends Factory
{

    protected $model = Appointments::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Clients::factory()->create()->id,
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'start_time' => $startTime  = $this->faker->dateTimeBetween('-1 year', '+1 year'),
            'end_time' => Carbon::parse($startTime)->addHours(2),
            'status' => rand(1,3),
        ];
    }
}
