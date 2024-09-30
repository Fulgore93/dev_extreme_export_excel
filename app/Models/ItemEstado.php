<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemEstado extends Model
{
    use HasFactory;

    protected  $table = "items_estados";  
    public $primaryKey='id'; 
}
