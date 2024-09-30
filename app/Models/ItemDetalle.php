<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemDetalle extends Model
{
    use HasFactory;

    protected  $table = "items_detalles";  
    public $primaryKey='id'; 
   
    public function accion() // tiene una accion
    {
        return $this->belongsTo(ItemAccion::class);
    }
}
