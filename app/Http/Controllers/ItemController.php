<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Models\ItemDetalle;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        try {
            // en mi php ini por defecto viene seteado en 128M
            // ini_set actualizará el memory limit para la función y volverá a su valor anterior
            // ini_set('memory_limit', '300M');

            // se obtienen 3154 elementos con esta configuración y en php.ini memory_limit = 128M
            // $items = Item::with('estado','detalle.accion')
            //     ->get()
            //     ->take(3154);
            
            // se obtienen 5442 elementos con esta configuración y en php.ini memory_limit = 128M
            $items = Item::select('id','nombre','descripcion','created_at','precio','stock','estado_id')
                ->with(
                    'estado:id,nombre',
                    'detalle:id,accion_id,item_id,descripcion,created_at',
                    'detalle.accion:id,nombre'
                )
                ->take(100)
                ->get();

            // $items = Item::with('estado','detalle.accion')
            //     ->get()
            //     ->take(1000);
            return $items;
            
            $itemsList = [];
            $items = Item::with('estado','detalle.accion')
                ->chunk(100, function ($items) use (&$itemsList) {
                    foreach ($items as $item) {
                        $itemsList[] = [
                            'id' => $item->id,
                            'nombre' => $item->nombre,
                            'descripcion' => $item->descripcion,
                            'created_at' => $item->created_at,
                            'precio' => $item->precio,
                            'stock' => $item->stock,
                            'estado' => $item->estado->nombre,
                            'detalle' => $item->detalle->map(function ($detalle) {
                                return [
                                    'id' => $detalle->id,
                                    // 'accion_nombre' => $detalle->accion->nombre,
                                    'descripcion' => $detalle->descripcion,
                                    'created_at' => $detalle->created_at,
                                ];
                            }),
                        ];
                    }
                });
            return response()->json($itemsList);
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function list_maestro(Request $request)
    {
        try {
            // en mi php ini por defecto viene seteado en 128M
            // ini_set actualizará el memory limit para la función y volverá a su valor anterior
            // ini_set('memory_limit', '300M');

            // se obtienen 3154 elementos con esta configuración y en php.ini memory_limit = 128M
            // $items = Item::with('estado','detalle.accion')
            //     ->get()
            //     ->take(3154);
            
            // se obtienen 5442 elementos con esta configuración y en php.ini memory_limit = 128M
            $items = Item::select('id','nombre','descripcion','created_at','precio','stock','estado_id')
                ->with(
                    'estado:id,nombre',
                )
                ->get()
                ->take(2);

            // $items = Item::with('estado','detalle.accion')
            //     ->get()
            //     ->take(1000);
            return $items;
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function list_detalle($id)
    {
        try {
            // en mi php ini por defecto viene seteado en 128M
            // ini_set actualizará el memory limit para la función y volverá a su valor anterior
            // ini_set('memory_limit', '300M');

            // se obtienen 3154 elementos con esta configuración y en php.ini memory_limit = 128M
            // $items = Item::with('estado','detalle.accion')
            //     ->get()
            //     ->take(3154);
            
            // se obtienen 5442 elementos con esta configuración y en php.ini memory_limit = 128M
            $detalle = ItemDetalle::with(
                    'accion:id,nombre'
                )
                ->where('item_id', $id)
                ->get();

            // $items = Item::with('estado','detalle.accion')
            //     ->get()
            //     ->take(1000);
            return $detalle;
            
            $itemsList = [];
            $items = Item::with('estado','detalle.accion')
                ->chunk(100, function ($items) use (&$itemsList) {
                    foreach ($items as $item) {
                        $itemsList[] = [
                            'id' => $item->id,
                            'nombre' => $item->nombre,
                            'descripcion' => $item->descripcion,
                            'created_at' => $item->created_at,
                            'precio' => $item->precio,
                            'stock' => $item->stock,
                            'estado' => $item->estado->nombre,
                            'detalle' => $item->detalle->map(function ($detalle) {
                                return [
                                    'id' => $detalle->id,
                                    // 'accion_nombre' => $detalle->accion->nombre,
                                    'descripcion' => $detalle->descripcion,
                                    'created_at' => $detalle->created_at,
                                ];
                            }),
                        ];
                    }
                });
            return response()->json($itemsList);
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function index(Request $request)
    {
        try {
            
            $page = $request->input('page', 1);
            $pageSize = $request->input('pageSize', 10);
            $items = Item::with(
                    // todo
                    'estado','detalle.accion'
                    // campos especificos
                    // 'estado:id,nombre',
                    // 'detalle:id,accion_id,item_id,descripcion,created_at',
                    // 'detalle.accion:id,nombre'
                )
                // ->get()
                ->paginate($pageSize, ['*'], 'page', $page);
                return response()->json([
                    'data' => $items->items(),       // Los datos de la página actual
                    'totalCount' => $items->total(), // Total de elementos
                    'pageCount' => $items->lastPage(), // Total de páginas
                    'pageSize' => $items->perPage(), // Tamaño de página
                    'currentPage' => $items->currentPage(), // Página actual
                ]);
            // $itemsList = [];

            // Item::with(
            //         'estado','detalle.accion'
            //     )
            //     ->chunk(100, function ($items) use (&$itemsList) {
            //         foreach ($items as $item) {
            //             $itemsList[] = [
            //                 'id' => $item->id,
            //                 'nombre' => $item->nombre,
            //                 // 'descripcion' => $item->descripcion,
            //                 // 'created_at' => $item->created_at,
            //                 // 'precio' => $item->precio,
            //                 // 'stock' => $item->stock,
            //                 // 'estado' => $item->estado->nombre,
            //                 'detalle' => $item->detalle->map(function ($detalle) {
            //                     return [
            //                         'id' => $detalle->id,
            //                         // 'accion_nombre' => $detalle->accion->nombre,
            //                         'descripcion' => $detalle->descripcion,
            //                         'created_at' => $detalle->created_at,
            //                     ];
            //                 }),
            //             ];
            //         }
            //     });

            // return response()->json($itemsList);
            return $items;
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
