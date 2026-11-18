<?php

use App\Models\Proveedor;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    #[Url]
    public $buscar = '';

    public function updatedBuscar()
    {
        $this->resetPage();
    }

    // Cuando el formulario guarda un proveedor, la tabla se vuelve a dibujar
    #[On('proveedor-guardado')]
    public function refrescar()
    {
    }

    public function with(): array
    {
        $consulta = Proveedor::query();

        if ($this->buscar) {
            $consulta->where(function ($q) {
                $q->where('razon_social', 'like', '%' . $this->buscar . '%')
                  ->orWhere('ruc', 'like', '%' . $this->buscar . '%');
            });
        }

        return [
            'proveedores' => $consulta->orderBy('razon_social')->paginate(10),
        ];
    }
};
?>

<div>
    <input
        type="text"
        wire:model.live.debounce.400ms="buscar"
        placeholder="Buscar por razón social o RUC"
        class="border rounded px-3 py-2 mb-4 w-full"
    >

    <table class="w-full text-left border-collapse bg-amber-600">
        <thead>
            <tr>
                <th class="p-2">RUC</th>
                <th class="p-2">Razón social</th>
                <th class="p-2">Teléfono</th>
                <th class="p-2">Correo</th>
                <th class="p-2"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($proveedores as $proveedor)
                <tr wire:key="proveedor-{{ $proveedor->id }}" class="border-b border-gray-300 hover:bg-amber-400">
                    <td class="p-2">{{ $proveedor->ruc }}</td>
                    <td class="p-2">{{ $proveedor->razon_social }}</td>
                    <td class="p-2">{{ $proveedor->telefono }}</td>
                    <td class="p-2">{{ $proveedor->email }}</td>
                    <td class="p-2">
                        <button type="button"
                            wire:click="$dispatch('editar-proveedor', { id: {{ $proveedor->id }} })">Editar</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-white">Sin datos</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $proveedores->links() }}
    </div>
</div>
