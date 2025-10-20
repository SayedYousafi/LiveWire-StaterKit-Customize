<div>
    <flux:heading size='xl' class="text-center mx-3"> UnUsed PDF files in the Server</flux:heading>

    <div x-data="{ show: false, message: '', type: 'success' }" x-on:toast.window="
        message = $event.detail.message;
        type = $event.detail.type;
        show = true;
        setTimeout(() => show = false, 3000);
    " class="fixed top-5 right-5 z-50">
        <div x-show="show" x-transition class="px-4 py-2 rounded-lg shadow-lg text-white"
            :class="type === 'success' ? 'bg-green-500' : 'bg-red-500'" x-text="message">
        </div>
    </div>
    <table class="table-default">
        <thead>
            <tr>
                <th>#</th>
                <th>File name</th>
                <th colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($unusedpdfs as $index => $path)
            @php
            $myFile = basename($path);
            @endphp

            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $myFile }}</td>
                <td>
                    <flux:button as a icon="eye" variant="primary" href="{{ asset('storage/pdfs/' . $myFile) }}"
                        target="_blank" class="!bg-blue-500 !text-white px-3 py-1 rounded hover:!bg-blue-600">
                        View
                    </flux:button>
                </td>
                <td>
                    <flux:button wire:confirm="Are you sure?" wire:click="delete('{{ $myFile }}')" icon="minus-circle"
                        variant="danger">
                        Delete
                    </flux:button>
                </td>
            </tr>
            @empty
            <tr>
                <th colspan="4">No records found</th>
            </tr>
            @endforelse
        </tbody>

    </table>


</div>