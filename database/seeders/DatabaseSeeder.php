<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Item;
use App\Models\ItemAccion;
use App\Models\ItemEstado;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
        $estadosFijos = [
            ['nombre' => 'Disponible', 'descripcion' => 'El item está disponible', 'estado' => 1],
            ['nombre' => 'No disponible', 'descripcion' => 'El item no está disponible', 'estado' => 1],
            ['nombre' => 'En tránsito', 'descripcion' => 'El item está en tránsito', 'estado' => 1],
            ['nombre' => 'Dañado', 'descripcion' => 'El item está dañado', 'estado' => 1],
        ];

        foreach ($estadosFijos as $estado) {
            ItemEstado::create($estado);
        }

        $acciones = [
            ['nombre' => 'Crear', 'descripcion' => 'El item se ha creado', 'estado' => 1],
            ['nombre' => 'Actualizar', 'descripcion' => 'El item se ha actualizado', 'estado' => 1],
            ['nombre' => 'Agrega stock', 'descripcion' => 'El item se le agregó stock', 'estado' => 1],
            ['nombre' => 'Descontinuado', 'descripcion' => 'El item está descontinuado', 'estado' => 1],
        ];
        foreach ($acciones as $accion) {
            ItemAccion::create($accion);
        }

        Item::factory()
            ->count(1000) // número de registros en tabla item
            ->hasDetalle(5) // Si deseas agregar detalles por cada item
            ->create();
    }
}
