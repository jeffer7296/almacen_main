<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'codigo',
        'categoria_id',
        'nombre',
        'descripcion',
        'precio'
    ];

    public function cantidad()
    {
        return $this->hasMany(Stock_Almacen::class,'producto_id','id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class,'categoria_id','id');
    }
}
