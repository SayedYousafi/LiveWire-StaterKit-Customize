<flux:modal name="leaveDetailsModal" class="md:w-96" :dismissible="false">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Leave Balance Details</flux:heading>
            <flux:text class="mt-2">Details.</flux:text>
        </div>

        <table class="table-default">
        
        <tbody>
            <tr>
                <th>Year</th>
                <th>Entitled</th>
                <th>Used</th>
                <th>Remaining</th>
            </tr>
            @foreach($balances as $year => $data)
            <tr>
                <td>{{ $year }}</td>
                <td>{{ $data['entitled'] }}</td>
                <td>{{ $data['used'] }}</td>
                <td>{{ $data['remaining'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="flex">
            <flux:spacer />

            <flux:button wire:click='close' variant="danger" icon='arrow-left-start-on-rectangle'>Close</flux:button>
        </div>
    </div>
</flux:modal>