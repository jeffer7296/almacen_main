<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proveedor;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        Proveedor::create([
            'ruc' => '20100070970',
            'razon_social' => 'Ferretería Industrial Cusco S.A.C.',
            'telefono' => '984111222',
            'email' => 'ventas@ferrecusco.test',
            'direccion' => 'Av. La Cultura 123, Cusco',
        ]);

        Proveedor::create([
            'ruc' => '20512345678',
            'razon_social' => 'Distribuidora Andina E.I.R.L.',
            'telefono' => '984333444',
            'email' => 'contacto@distandina.test',
            'direccion' => 'Jr. Comercio 456, Quillabamba',
        ]);

        Proveedor::create([
            'ruc' => '20601234567',
            'razon_social' => 'Herramientas del Sur S.R.L.',
            'telefono' => '984555666',
            'email' => 'pedidos@hersur.test',
            'direccion' => 'Av. Sol 789, Cusco',
        ]);
    }
}
