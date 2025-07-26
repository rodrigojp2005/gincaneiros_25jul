<?php
namespace Database\Factories;

use App\Models\GincanaLocal;
use Illuminate\Database\Eloquent\Factories\Factory;

class GincanaLocalFactory extends Factory
{
    protected $model = GincanaLocal::class;

    public function definition()
    {
        return [
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];
    }
}
