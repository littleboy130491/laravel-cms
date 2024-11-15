<!-- resources/views/filament/resources/template-resource/pages/edit-template-file.blade.php -->

<x-filament::page>
    <x-filament::form wire:submit="save">
        {{ $this->form }}
    </x-filament::form>
</x-filament::page>
