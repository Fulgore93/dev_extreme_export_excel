<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAccion extends Model
{
    use HasFactory;

    protected  $table = "items_acciones";  
    public $primaryKey='id'; 
}
