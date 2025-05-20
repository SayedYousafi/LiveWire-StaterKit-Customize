<div class="px-4 py-6 space-y-4">

    @if(session('success'))
        <flux:callout variant="success" heading="{{ session('success') }}" />
    @endif

    <flux:modal.trigger name="imageModal" class="mb-2">
        <flux:button  class="!bg-blue-800 text-white! hover:!bg-blue-700" icon="plus-circle">
            Select an image from server
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="imageModal">
        <div class="p-4 space-y-4 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 rounded-lg shadow-lg w-full max-w-5xl">

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Select an Image from the Server</h2>

                <div class="w-1/2">
                    <flux:input wire:model.live.debounce.500ms="search" placeholder="Search files by name..." />
                </div>
            </div>

            <div class="flex flex-wrap gap-4" wire:key="files-list">
                @foreach($files as $file)
                    <div onclick="selectImage('{{ $file['path'] }}', '{{ $file['name'] }}')"
                         class="cursor-pointer border p-2 rounded shadow-sm hover:shadow-md dark:border-gray-600">
                        <img src="{{ $file['path'] }}" alt="{{ $file['name'] }}"
                             class="w-24 h-24 object-cover mb-1 rounded">
                        <p class="text-sm truncate">{{ $file['name'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $files->links() }}
            </div>

        </div>
    </flux:modal>

    <div x-data="{ show: false }" class="space-y-2">
        <p x-show="show" class="text-sm font-semibold">Selected Image Preview:</p>
        <img id="selectedImagePreview" src="" alt="Selected Image Preview"
             x-show="show" class="max-w-xs rounded shadow-md" />
        <p id="selectedImageName" x-show="show" class="text-sm font-medium"></p>
        <button id="saveButton" x-show="show"
                class="bg-gray-800 text-white px-3 py-1 text-sm rounded mt-2 hover:bg-gray-900"
                wire:click="saveImage('')">Save</button>
        <hr class="border-t dark:border-gray-700" />
    </div>

    <div x-data="{ open: false }" class="space-y-2">

        {{-- <label for="actual-btn" @click="open = true"
               class="text-indigo-600! font-semibold! cursor-pointer! hover:underline">Upload an image from local</label> --}}
<label for="actual-btn" @click="open = true"
               class="text-indigo-600! font-semibold! cursor-pointer! hover:underline">Upload an image from local</label>

        <div x-show="open" x-transition>
            <form wire:submit.prevent="save" enctype="multipart/form-data" class="space-y-4 mt-2">

                <input type="file" wire:model="photo" id="actual-btn" class="hidden" />

                @if ($photo)
                    <div>
                        <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="max-w-xs mb-2 rounded shadow-md">
                        <p class="text-sm">{{ $photo->getClientOriginalName() }}</p>
                    </div>
                @endif

                <button type="submit"
                        class="bg-gray-800 text-white px-3 py-1 text-sm rounded hover:bg-gray-900 float-end">
                    Upload
                </button>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    
    function selectImage(imagePath, imageName) {
    const previewImage = document.getElementById('selectedImagePreview');
    const imageNameElement = document.getElementById('selectedImageName');
    const saveButton = document.getElementById('saveButton');

    previewImage.src = imagePath;
    previewImage.style.display = 'block';

    imageNameElement.textContent = imageName;
    imageNameElement.style.display = 'block';

    saveButton.setAttribute('wire:click', `saveImage('${imageName}')`);
    saveButton.style.display = 'inline-block';

    Flux.modal('imageModal').close()
}

</script>


@endpush
