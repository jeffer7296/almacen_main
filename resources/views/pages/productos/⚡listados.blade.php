<?php

use Livewire\Component;
use App\Models\Producto;
use Livewire\Attributes\Computed;


new class extends Component
{ 
};
?>

@extends('layouts.app')
@section('menu')
<nav class="flex flex-col gap-2">
    <a href="/productos">Productos</a>
    <a href="/proveedores">Proveedores</a>
</nav>
@endsection

@section('contenido')

@livewire('productos-index')

@endsection

@section('formulario')
@livewire('productos-form')
@endsection