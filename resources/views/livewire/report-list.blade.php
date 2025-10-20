<div class="container mt-4">
    <h2 class="mb-3 text-primary">Generated CSV Files fro download</h2>

    <table class="table table-default">
        <thead>
            <tr>
                <th>#</th>
                <th>File Name</th>
                <th>Date Exported</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($files as $file)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $file['name'] }}</td>
                <td>{{ $file['date_exported'] }}</td>
                <td>
                    <flux:button icon='arrow-down-on-square-stack'
                    variant='primary' size='sm'
                                wire:click="download('{{ $file['path'] }}')">
                        <i class="bi bi-download"></i> Download
                    </flux:button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center text-muted">No reports available</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>