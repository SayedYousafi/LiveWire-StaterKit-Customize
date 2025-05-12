<div class="container mx-auto">
    <flux:modal.trigger name="postModal">
    <flux:button icon='plus-circle'>New post</flux:button>
</flux:modal.trigger>

<flux:modal name="postModal" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Post Detail</flux:heading>
            {{-- <flux:text class="mt-2">Make changes to your personal details.</flux:text> --}}
        </div>

        <flux:input wire:model='title' label="Title:" placeholder="Title of your post" />

        <flux:textarea wire:model='body' label="Body:" placeholder="Body of your post" />

        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="ghost" icon='x-circle'
            x-on:click="Flux.modal('postModal').close()"
            >Cancel</flux:button>
            @if ($update)
            <flux:button wire:click='updatePost' type="submit" variant="primary" icon='pencil-square'>Save changes</flux:button>
            @else
            
            <flux:button wire:click='save' type="submit" variant="primary" icon='plus-circle'>Add post</flux:button>
            @endif

        </div>
    </div>
</flux:modal>
@if(session('success'))
<flux:callout heading="{{ session('success') }}" variant='success' icon='check-circle'/>
{{-- <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}." /> --}}
@endif
<div class="relative overflow-x-auto">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr class="bg-white dark:bg-gray-800">
                <th scope="col" class="px-6 py-3">
                    ID
                </th>
                <th scope="col" class="px-6 py-3">
                    Title
                </th>
                <th scope="col" class="px-6 py-3">
                    Body
                </th>
                <th scope="col" class="px-6 py-3">
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($posts as $post )
            <tr>
                <td class="px-6 py-4">
                    {{ $post->id }}
                </td>
                <td class="px-6 py-4">
                    {{ $post->title }}
                </td>
                <td class="px-6 py-4">
                    {{ $post->body }} 
                </td>
                <td class="px-6 py-4">
                   <flux:button variant='primary' icon='pencil-square' wire:click='edit({{ $post->id }})'>Edit</flux:button>
                </td>
                <td class="px-6 py-4">
                    <flux:button variant='danger' icon='minus-circle' wire:confirm='Are you sure?' wire:click='delete({{ $post->id }})'>Delete</flux:button>
                 </td>
            </tr>
                            
            @empty
            <tr>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    No records found
                </th>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

</div>
