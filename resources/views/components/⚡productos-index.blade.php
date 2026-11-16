<?php

use Livewire\Component;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\Categoria;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

new class extends Component {
    use WithPagination;
    public $search = '';
    public $categoria_id = '';
    public $almacen_id = '';
    public $filtro_stock = '';

    // Al cambiar la búsqueda o cualquier filtro, vuelve a la página 1
    public function updating($propiedad)
    {
        if (in_array($propiedad, ['search', 'categoria_id', 'almacen_id', 'filtro_stock'])) {
            $this->resetPage();
        }
    }

    public function with(): array
    {
        // select('productos.*') evita que el join sobrescriba el id del producto
        $consulta = Producto::select('productos.*')->with(['categoria', 'cantidad.almacen']);

        // Búsqueda por nombre o código
        if ($this->search) {
            $consulta->where(function ($q) {
                $q->where('productos.nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('productos.codigo', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por categoría
        if ($this->categoria_id) {
            $consulta->where('productos.categoria_id', $this->categoria_id);
        }

        // Filtro por almacén
        if ($this->almacen_id) {
            $consulta->join('stock__almacen', 'productos.id', '=', 'stock__almacen.producto_id');
            $consulta->where('stock__almacen.almacen_id', $this->almacen_id);
        }

        // Filtro por estado de stock (en el almacén elegido, o en cualquiera)
        if ($this->filtro_stock === 'agotado') {
            if ($this->almacen_id) {
                $consulta->where('stock__almacen.stock', '<=', 0);
            } else {
                $consulta->whereHas('cantidad', function($q) {
                    $q->where('stock', '<=', 0);
                });
            }
        } elseif ($this->filtro_stock === 'disponible') {
            if ($this->almacen_id) {
                $consulta->where('stock__almacen.stock', '>', 0);
            } else {
                $consulta->whereHas('cantidad', function($q) {
                    $q->where('stock', '>', 0);
                });
            }
        }

        return [
            'productos' => $consulta->orderBy('productos.nombre')->paginate(10),
            'almacenes' => Almacen::orderBy('nombre')->get(),
            'categorias' => Categoria::orderBy('nombre')->get(),
        ];
    }
};

?>

<div>
    <div style="margin-bottom: 20px; display: flex; gap: 10px;">
        <input type="text"
            wire:model.live.debounce.400ms="search"
            placeholder="Buscar por nombre o código">
        <select wire:model.live="categoria_id">
            <option value="">Todas las categorías</option>
            @foreach($categorias as $categoria)
                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
            @endforeach
        </select>
        <select wire:model.live="almacen_id">
            <option value="">Todos los almacenes</option>
            @foreach($almacenes as $almacen)
                <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
            @endforeach
        </select>
        <select wire:model.live="filtro_stock">
            <option value="">Estado de stock (Todos)</option>
            <option value="disponible">Con Stock</option>
            <option value="agotado">Agotados</option>
        </select>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Almacén</th>
            </tr>
        </thead>
        @forelse ($productos as $producto)
            @php
                // Si hay almacén elegido muestra el stock de ESE almacén; si no, el del primero
                $fila = $almacen_id
                    ? $producto->cantidad->firstWhere('almacen_id', $almacen_id)
                    : $producto->cantidad->first();
            @endphp
            <tr wire:key="producto-{{ $producto->id }}">
                <td>{{ $producto->codigo }}</td>
                <td>{{ $producto->nombre }}</td>
                <td>{{ $producto->categoria?->nombre }}</td>
                <td>{{ $fila?->stock }}</td>
                <td>{{ $fila?->almacen?->nombre }}</td>
            </tr>
        @empty
            <tr><td colspan="5">Sin datos</td></tr>
        @endforelse
    </table>
    {{ $productos->links() }}
</div>
