<?php

namespace Database\Factories;
use App\Models\Item;
use App\Models\ItemEstado;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->word,
            'descripcion' => $this->faker->sentence,
            'precio' => $this->faker->numberBetween(100, 900000),
            'stock' => $this->faker->numberBetween(1, 100),
            'estado_id' => ItemEstado::inRandomOrder()->first()->id, // Selecciona un estado existente
        ];
    }
}
