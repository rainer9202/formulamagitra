<x-filament::page>
    <form wire:submit.prevent="register">
        {{$this->form}}
        <div class="row" style="margin-top: 20px">
            <button type="submit" class="filament-button py-2  gap-1 font-medium rounded-lg border focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-5 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action">
                Guardar
            </button>
        </div>
    </form>
</x-filament::page>