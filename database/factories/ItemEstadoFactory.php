<?php

namespace Database\Factories;
use App\Models\ItemEstado;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemEstadoFactory extends Factory
{
    protected $model = ItemEstado::class;

    public function definition()
    {
        $estadosFijos = [
            ['nombre' => 'Disponible', 'descripcion' => 'El item está disponible', 'estado' => 1],
            ['nombre' => 'No disponible', 'descripcion' => 'El item no está disponible', 'estado' => 1],
            ['nombre' => 'En tránsito', 'descripcion' => 'El item está en tránsito', 'estado' => 1],
            ['nombre' => 'Dañado', 'descripcion' => 'El item está dañado', 'estado' => 1],
        ];

        $estadoSeleccionado = $this->faker->randomElement($estadosFijos);

        return [
            'nombre' => $estadoSeleccionado['nombre'],
            'descripcion' => $estadoSeleccionado['descripcion'],
            'estado' => $estadoSeleccionado['estado'],
        ];
    }
}