<div class="container mt-4">
    <h2 class="mb-3 text-primary">MIS MySQL Backups</h2>

        <div class="table-responsive mb-1">
            <table class="table-default">
                <thead>
                    <tr>
                        <th>File name</th>
                        <th>Backup date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($files as $file)
                        <tr>
                            <td>{{basename($file)}}</td>
                            <td>{{ substr(substr($file, -17), 0,10) }}</td>
                            <td>
                                <flux:button wire:click="download('{{ $file }}')" icon='arrow-down-tray' variant='primary' size='sm'>
                                    <i>Download</i> 
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No mysql backup file for available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

</div>
