<?php

namespace Database\Factories;
use App\Models\ItemDetalle;
use App\Models\ItemAccion;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemDetalleFactory extends Factory
{
    protected $model = ItemDetalle::class;

    public function definition()
    {
        return [
            'accion_id' => ItemAccion::inRandomOrder()->first()->id, // Relación con ItemAccion
            'item_id' => Item::factory(), // Relación con Item
            'descripcion' => $this->faker->sentence,
            'estado' => $this->faker->numberBetween(0, 1),
        ];
    }
}