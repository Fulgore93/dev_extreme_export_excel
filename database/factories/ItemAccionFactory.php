<?php

namespace Database\Factories;
use App\Models\ItemAccion;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemAccionFactory extends Factory
{
    protected $model = ItemAccion::class;

    public function definition()
    {
        
        $acciones = [
            ['nombre' => 'Crear', 'descripcion' => 'El item se ha creado', 'estado' => 1],
            ['nombre' => 'Actualizar', 'descripcion' => 'El item se ha actualizado', 'estado' => 1],
            ['nombre' => 'Agrega stock', 'descripcion' => 'El item se le agregó stock', 'estado' => 1],
            ['nombre' => 'Descontinuado', 'descripcion' => 'El item está descontinuado', 'estado' => 1],
        ];

        $accionSeleccionado = $this->faker->randomElement($acciones);

        return [
            'nombre' => $accionSeleccionado['nombre'],
            'descripcion' => $accionSeleccionado['descripcion'],
            'estado' => $accionSeleccionado['estado'],
        ];
    }
}