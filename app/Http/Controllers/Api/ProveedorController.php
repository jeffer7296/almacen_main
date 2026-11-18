<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProveedorRequest;
use App\Http\Resources\ProveedorResource;
use App\Models\Proveedor;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProveedorResource::collection(Proveedor::orderBy('razon_social')->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProveedorRequest $request)
    {
        $proveedor = Proveedor::create($request->validated());
        return (new ProveedorResource($proveedor))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Proveedor $proveedor)
    {
        return new ProveedorResource($proveedor);
    }
}
