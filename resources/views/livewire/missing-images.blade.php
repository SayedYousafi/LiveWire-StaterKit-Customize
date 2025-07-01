<div class="contianer">

<flux:heading size='xl' class="mb-3 text-center">Pictures in the server; not used in any item</flux:heading>

<div class="space-y-2">
    @if ($missingImages->isEmpty())
        <div class="text-gray-500 italic">
            No images found – all pictures are assigned to items.
        </div>
    @else
       @foreach ($missingImages as $image)
    <div class="flex items-center justify-between bg-gray-100 p-2 rounded mb-2">
        <div class="shrink-0 text-sm text-gray-700 px-10">
            {{ $loop->iteration }}
        </div>
        <div class="flex items-center gap-3 flex-1">
            <img 
                src="{{ asset('storage/' . $image) }}" 
                alt="Image" 
                class="w-16 h-16 object-cover rounded border"
            />
            <span>{{ $image }}</span>
        </div>
        <div class="flex gap-2 shrink-0">
            <flux:button as a icon='eye' variant='primary'
                href="{{ asset('storage/' . $image) }}" 
                target="_blank"
                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
            >
                View large
            </flux:button>
            <flux:button 
                wire:click="deleteImage('{{ $image }}')" 
                icon='minus-circle' 
                variant='danger'
            >
                Delete
            </flux:button>
        </div>
    </div>
@endforeach


    @endif
</div>


</div>