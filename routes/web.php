<?php

use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('todojunto');

Route::get('/javascript', function () {
    return view('javascript');
})->name('javascript');

Route::get('/agrupacion', function () {
    return view('agrupacion');
})->name('agrupacion');

Route::get('/maestrodetalle', function () {
    return view('maestrodetalle');
})->name('maestrodetalle');

Route::get('/maestrodetalle_detalle_aparte', function () {
    return view('maestrodetalle_detalle_aparte');
})->name('maestrodetalle_detalle_aparte');

// Route::get('/list', function () {
    
//     $items = [
//         ['id' => 1, 'nombre' => 'Item 1', 'descripcion' => 'Descripción item 1 lorem ipsum ultra largo ahhhh esto es una descripción muy larga de nosé cuantos caracteres', 'fecha' => '2024-08-13', 'precio' => 65832, 'stock' => 20,'estado' => 1,
//         'detalle' => [
//             ['id' => 1, 'item_id' => 1, 'accion' => 'crear', 'descripcion' => 'se creó el item', 'fecha' => '2024-06-01'],
//             ['id' => 2, 'item_id' => 1, 'accion' => 'editar', 'descripcion' => 'se añadió stock', 'fecha' => '2024-06-10'],
//             ['id' => 3, 'item_id' => 1, 'accion' => 'editar', 'descripcion' => 'se añadió stock', 'fecha' => '2024-06-28'],
//         ]],
//         ['id' => 2, 'nombre' => 'Item 2', 'descripcion' => 'Descripción item 2', 'fecha' => '2024-07-10', 'precio' => 525, 'stock' => 20,'estado' => 1,
//         'detalle' => [
//             ['id' => 4, 'item_id' => 2, 'accion' => 'crear', 'descripcion' => 'se creó el item', 'fecha' => '2024-07-01'],
//         ]],
//         ['id' => 3, 'nombre' => 'Item 3', 'descripcion' => 'Descripción item 3', 'fecha' => '2024-07-10', 'precio' => 3879, 'stock' => 10,'estado' => 1,
//         'detalle' => [
//             ['id' => 5, 'item_id' => 3, 'accion' => 'crear', 'descripcion' => 'se creó el item', 'fecha' => '2024-07-01'],
//             ['id' => 6, 'item_id' => 3, 'accion' => 'editar', 'descripcion' => 'se añadió stock', 'fecha' => '2024-07-10'],
//         ]],
//         ['id' => 4, 'nombre' => 'Item 4', 'descripcion' => 'Descripción item 4', 'fecha' => '2024-06-28', 'precio' => 10990, 'stock' => 20,'estado' => 0,
//         'detalle' => [
//             ['id' => 7, 'item_id' => 4, 'accion' => 'crear', 'descripcion' => 'se creó el item', 'fecha' => '2024-06-20'],
//             ['id' => 8, 'item_id' => 4, 'accion' => 'editar', 'descripcion' => 'se añadió stock', 'fecha' => '2024-06-21'],
//         ]],
//     ];
    
//     return response()->json($items);
// })->name('list');

Route::get('/list', [ItemController::class, 'list'])->name('list');
Route::get('/list_maestro', [ItemController::class, 'list_maestro'])->name('maestro');
Route::get('/list_detalle/{id}', [ItemController::class, 'list_detalle'])->name('detalle');
