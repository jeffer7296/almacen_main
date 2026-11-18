<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\ProveedorController;



Route::apiResource('productos', ProductoController::class);

// parameters(): en español "proveedores" se singulariza mal, así que se indica el nombre del parámetro
Route::apiResource('proveedores', ProveedorController::class)
    ->only(['index', 'store', 'show'])
    ->parameters(['proveedores' => 'proveedor']);
