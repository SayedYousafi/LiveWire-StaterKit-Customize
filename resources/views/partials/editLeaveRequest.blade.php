<flux:modal name="leaveModal" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Adjusting the Problem</flux:heading>
            {{-- <flux:text class="mt-2">Make changes to QTY.</flux:text> --}}
        </div>
<form wire:submit="leaveRequest" class="my-6 w-full space-y-6">
            <flux:input wire:model.live="dateFrom" :label="__('Date from:')" type="date" autofocus
                autocomplete="Date from" />
            <flux:input wire:model.live="dateTo" :label="__('Date to:')" type="date" autocomplete="Date to" />
            @if (!is_null($this->daysDifference))
            <div>
                No. of days: {{ $this->daysDifference }}
            </div>
            @endif
            <flux:textarea wire:model='reason' label='Reason:' />
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button icon='plus-circle' variant="primary" type="submit" class="w-full">{{ __('Save') }}
                    </flux:button>
                </div>
            </div>
        </form>
        <x-action-message class="me-3" on="leave-requested">
            <flux:callout variant='success'
                heading="{{ __('Leave request successfully registered, wait for approval from admin') }}" />
        </x-action-message>
    </div>
</flux:modal>