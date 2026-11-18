<?php

use App\Models\Proveedor;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public ?Proveedor $proveedor = null;
    public $ruc = '';
    public $razon_social = '';
    public $telefono = '';
    public $email = '';
    public $direccion = '';

    protected function rules(): array
    {
        return [
            'ruc' => [
                'required',
                'digits:11',
                Rule::unique('proveedores', 'ruc')->ignore($this->proveedor?->id),
            ],
            'razon_social' => ['required', 'max:100'],
            'telefono' => ['required', 'digits_between:7,9'],
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('proveedores', 'email')->ignore($this->proveedor?->id),
            ],
            'direccion' => ['required', 'max:200'],
        ];
    }

    protected function messages(): array
    {
        return [
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.digits' => 'El RUC debe tener exactamente 11 dígitos.',
            'ruc.unique' => 'Ya existe un proveedor con ese RUC.',
            'razon_social.required' => 'La razón social es obligatoria.',
            'razon_social.max' => 'La razón social admite como máximo 100 caracteres.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.digits_between' => 'El teléfono debe tener entre 7 y 9 dígitos.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.max' => 'El correo admite como máximo 100 caracteres.',
            'email.unique' => 'Ya existe un proveedor con ese correo.',
            'direccion.required' => 'La dirección es obligatoria.',
            'direccion.max' => 'La dirección admite como máximo 200 caracteres.',
        ];
    }

    public function updated($propiedad)
    {
        // El API recorta los espacios; aquí se recortan antes de validar
        if (in_array($propiedad, ['ruc', 'telefono', 'email'])) {
            $this->$propiedad = trim((string) $this->$propiedad);
        }
        $this->validateOnly($propiedad);
    }

    #[On('editar-proveedor')]
    public function editar($id)
    {
        $this->proveedor = Proveedor::findOrFail($id);
        $this->fill($this->proveedor->only(
            'ruc', 'razon_social', 'telefono', 'email', 'direccion'
        ));
        $this->resetValidation();
    }

    public function guardar()
    {
        $datos = $this->validate();

        if ($this->proveedor) {
            $this->proveedor->update($datos);
        } else {
            Proveedor::create($datos);
        }

        $this->cancelar();
        session()->flash('ok', 'Proveedor guardado correctamente.');
        $this->dispatch('proveedor-guardado');
    }

    public function cancelar()
    {
        $this->reset();
        $this->resetValidation();
    }
};
?>

<div>
    @if (session('ok'))
        <p class="text-green-700">{{ session('ok') }}</p>
    @endif

    <form wire:submit="guardar" class="space-y-4">
        <h2 class="text-xl font-bold">{{ $proveedor ? 'Editar proveedor' : 'Nuevo proveedor' }}</h2>

        <div>
            <label for="ruc">RUC</label>
            <input id="ruc" type="text" wire:model.blur="ruc" class="border rounded px-3 py-2 w-full">
            @error('ruc')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="razon_social">Razón social</label>
            <input id="razon_social" type="text" wire:model.blur="razon_social" class="border rounded px-3 py-2 w-full">
            @error('razon_social')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="telefono">Teléfono</label>
            <input id="telefono" type="text" wire:model.blur="telefono" class="border rounded px-3 py-2 w-full">
            @error('telefono')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email">Correo</label>
            <input id="email" type="text" wire:model.blur="email" class="border rounded px-3 py-2 w-full">
            @error('email')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="direccion">Dirección</label>
            <textarea id="direccion" wire:model.blur="direccion" class="border rounded px-3 py-2 w-full"></textarea>
            @error('direccion')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-green-600 text-white rounded px-4 py-2">Guardar</button>
        @if ($proveedor)
            <button type="button" wire:click="cancelar" class="border rounded px-4 py-2">Cancelar</button>
        @endif
    </form>
</div>
