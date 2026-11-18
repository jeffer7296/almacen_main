<?php

use Livewire\Component;

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

@livewire('proveedores-index')

@endsection

@section('formulario')
@livewire('proveedores-form')
@endsection
