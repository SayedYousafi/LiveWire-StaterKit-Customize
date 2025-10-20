@php
    // Unique ID for this Livewire component instance (v3). If you're on v2, replace with: $cid = uniqid('lw-');
    $cid = $this->getId();
@endphp

<div class="px-4 py-6 space-y-4">
    @if(session('success'))
        <flux:callout variant="success" heading="{{ session('success') }}" />
    @endif

    {{-- <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-2"> --}}
<div style="display: flex; gap: 16px;">
        <div style="flex: 0 0 75%;">
            <table class="table-default w-2xl">
                <tr>
                    <td>#</td>
                    <td>File Name Path/View</td>
                    <td>Remove</td>
                </tr>
                @forelse($attachments as $att)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <a href="{{asset('storage/'. $att->path) }}" target="_blank" class="!text-blue-700 hover:!underline">
                                {{ $att->filename }}
                            </a>
                        </td>
                        <td><flux:button wire:confirm='Are you sure?' wire:click='deattach({{ $att->id }})' icon='link-slash' size='sm' variant='danger'>DeAttach</flux:button> </td>
                    </tr>
                @empty
                    <div>No attachments found for this item</div>
                @endforelse
            </table>
        </div>

        <div style="flex: 0 0 25%;">
            <!-- Button to open PDF selector modal -->
            <flux:modal.trigger name="pdfModal" class="mb-2">
                <flux:button class="!bg-blue-800 text-white! hover:!bg-blue-700 mb-3" icon="plus-circle">
                    Select a PDF File from server
                </flux:button>
            </flux:modal.trigger>

            <!-- Modal showing PDFs on server -->
            <flux:modal name="pdfModal">
                <div class="p-4 space-y-4 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 rounded-lg shadow-lg !w-full !max-w-7xl">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Select a PDF file from the Server</h2>
                        <div class="w-1/2">
                            <flux:input wire:model.live.debounce.500ms="search" placeholder="Search PDFs by name..." />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between border-b py-2 text-bold gap-4">
                            <span>File name</span> <span>View</span> <span>Uploaded at</span>
                        </div>

                        @forelse($files as $file)
                            <div class="flex items-center justify-between border-b py-2 text-sm"
                                 onclick="selectPdf_{{ $cid }}('{{ $file['path'] }}', '{{ $file['name'] }}')">
                                <span class="truncate w-2/3">{{ $file['name'] }}</span>
                                <span>
                                    <a href="{{ $file['path'] }}" target="_blank" class="text-blue-600 hover:underline">
                                        View PDF
                                    </a>
                                </span>
                                <span class="ml-2 text-gray-500">{{ date('Y-m-d H:i', $file['mtime']) }}</span>
                            </div>
                        @empty
                            <div class="text-gray-500">No PDFs found.</div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $files->links() }}
                    </div>
                </div>
            </flux:modal>

            <!-- Selected PDF info -->
            <div x-data="{ show: false }" class="space-y-2">
                <p x-show="show" class="text-sm font-semibold">Selected PDF:</p>
                <p id="selectedPdfName-{{ $cid }}" x-show="show" class="text-sm font-medium"></p>
                <a id="selectedPdfLink-{{ $cid }}" x-show="show" class="text-indigo-700 hover:underline text-sm" target="_blank">Open PDF</a>
                <button id="pdf-save-btn-{{ $cid }}"
                        x-show="show"
                        class="bg-gray-800 text-white px-3 py-1 text-sm rounded mt-2 hover:bg-gray-900"
                        wire:click="savePdf('')">
                    Save
                </button>
                {{-- <hr class="border-t dark:border-gray-700" /> --}}
               
            </div>
 {{-- <flux:separator/> --}}
            <!-- PDF uploading -->
            <div x-data="{ open: false }" class="space-y-2">
                <label for="pdf-input-{{ $cid }}" @click="open = true"
                       class="bg-pink-400 text-white px-4 py-2 rounded-lg cursor-pointer hover:bg-pink-500 inline-block">
                    Upload a PDF file from local
                </label>

                <div x-show="open" x-transition>
                    <form wire:submit.prevent="save" enctype="multipart/form-data" class="space-y-4 mt-2">
                        <!-- Hidden file input (unique id) -->
                        <input type="file"
                               wire:model.blur="pdf"
                               id="pdf-input-{{ $cid }}"
                               class="hidden"
                               accept="application/pdf" />

                        <!-- Validation error -->
                        @error('pdf')
                            <flux:callout variant="danger" icon="x-circle" heading="{{ $message }}" />
                        @enderror

                        <!-- File name preview -->
                        @if ($pdf)
                            <div>
                                <p class="text-sm font-medium text-gray-700">
                                    {{ $pdf->getClientOriginalName() }}
                                </p>
                            </div>
                        @endif

                        <!-- Submit button -->
                        <button type="submit"
                                class="bg-gray-800 text-white px-3 py-1 text-sm rounded hover:bg-gray-900 float-end">
                            Upload
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    // Component-scoped selector to avoid global collisions
    function selectPdf_{{ $cid }}(pdfPath, pdfName) {
        const cid = @json($cid);

        const pdfNameElement = document.getElementById(`selectedPdfName-${cid}`);
        const pdfLink = document.getElementById(`selectedPdfLink-${cid}`);
        const saveButton = document.getElementById(`pdf-save-btn-${cid}`);

        if (!pdfNameElement || !pdfLink || !saveButton) return;

        pdfNameElement.textContent = pdfName;
        pdfNameElement.style.display = 'block';

        pdfLink.href = pdfPath;
        pdfLink.style.display = 'inline-block';
        pdfLink.textContent = "Open PDF";

        // Set Livewire action for this specific selection
        saveButton.setAttribute('wire:click', `savePdf('${pdfName}')`);
        saveButton.style.display = 'inline-block';

        Flux.modal('pdfModal').close();
    }
</script>
@endpush
