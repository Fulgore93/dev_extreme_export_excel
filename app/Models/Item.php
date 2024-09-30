<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected  $table = "items";  
    public $primaryKey='id'; 
   
    public function estado() // tiene un estado
    {
        return $this->belongsTo(ItemEstado::class);
    }

    public function detalle() // tiene detalles
    {
        return $this->hasMany(ItemDetalle::class, 'item_id', 'id');
    } 
}
