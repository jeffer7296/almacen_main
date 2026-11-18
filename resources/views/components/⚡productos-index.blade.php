<?php

use Livewire\Component;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\Categoria;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

new class extends Component {
    use WithPagination;

    // Cada filtro vive en la URL: sobrevive al F5 y se puede compartir
    #[Url]
    public $buscar = '';

    #[Url]
    public $categoria_id = '';

    #[Url]
    public $almacen_id = '';

    // FILTRO PROPIO: nivel de stock (agotado / bajo / normal)
    #[Url]
    public $nivel = '';

    // Hasta esta cantidad (inclusive) un producto se considera con "stock bajo"
    const STOCK_BAJO = 10;

    // Al cambiar cualquier filtro se vuelve a la página 1
    public function updatedBuscar()
    {
        $this->resetPage();
    }

    public function updatedCategoriaId()
    {
        $this->resetPage();
    }

    public function updatedAlmacenId()
    {
        $this->resetPage();
    }

    public function updatedNivel()
    {
        $this->resetPage();
    }

    // Cuando el formulario guarda un producto, la tabla se vuelve a dibujar
    #[On('producto-guardado')]
    public function refrescar()
    {
    }

    public function limpiar()
    {
        $this->reset(['buscar', 'categoria_id', 'almacen_id', 'nivel']);
        $this->resetPage();
    }

    public function with(): array
    {
        // select('productos.*') evita que el join pise el id del producto
        $consulta = Producto::select('productos.*')->with(['categoria', 'cantidad.almacen']);

        // Búsqueda por nombre o código
        if ($this->buscar) {
            $consulta->where(function ($q) {
                $q->where('productos.nombre', 'like', '%' . $this->buscar . '%')
                  ->orWhere('productos.codigo', 'like', '%' . $this->buscar . '%');
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

        // Filtro propio: nivel de stock
        if ($this->nivel) {
            // Rango de stock según el nivel elegido: [mínimo, máximo] (null = sin límite)
            $rangos = [
                'agotado' => [null, 0],
                'bajo'    => [1, self::STOCK_BAJO],
                'normal'  => [self::STOCK_BAJO + 1, null],
            ];
            [$min, $max] = $rangos[$this->nivel] ?? [null, null];

            $aplicar = function ($q, $columna) use ($min, $max) {
                if ($min !== null) {
                    $q->where($columna, '>=', $min);
                }
                if ($max !== null) {
                    $q->where($columna, '<=', $max);
                }
            };

            if ($this->almacen_id) {
                // Con almacén elegido: se mira el stock de ESE almacén
                $aplicar($consulta, 'stock__almacen.stock');
            } else {
                // Sin almacén: basta con que algún almacén tenga ese nivel
                $consulta->whereHas('cantidad', function ($q) use ($aplicar) {
                    $aplicar($q, 'stock');
                });
            }
        }

        return [
            'productos'  => $consulta->orderBy('productos.nombre')->paginate(10),
            'almacenes'  => Almacen::orderBy('nombre')->get(),
            'categorias' => Categoria::orderBy('nombre')->get(),
        ];
    }
};

?>

<div>
    <div style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
        <input type="text"
            wire:model.live.debounce.400ms="buscar"
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

        <select wire:model.live="nivel">
            <option value="">Cualquier nivel de stock</option>
            <option value="agotado">Agotado (0)</option>
            <option value="bajo">Stock bajo (1 a 10)</option>
            <option value="normal">Stock normal (más de 10)</option>
        </select>

        <button type="button" wire:click="limpiar">Limpiar filtros</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Almacén</th>
                <th></th>
            </tr>
        </thead>
        @forelse ($productos as $producto)
            @php
                // Con almacén elegido muestra el stock de ESE almacén; si no, el del primero
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
                <td>
                    <button type="button"
                        wire:click="$dispatch('editar-producto', { id: {{ $producto->id }} })">Editar</button>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">Sin datos</td></tr>
        @endforelse
    </table>

    <p>Mostrando {{ $productos->firstItem() ?? 0 }} a {{ $productos->lastItem() ?? 0 }} de {{ $productos->total() }}</p>

    {{ $productos->links() }}
</div>
