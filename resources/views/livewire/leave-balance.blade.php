<div>
    <table class="table-default">
        <thead>
            <tr>
                <th colspan="4">Leave Balance of {{ $user }} </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>Year</th>
                <th>Entitled</th>
                <th>Used</th>
                <th>Remaining</th>
            </tr>
            @php
                $totalEntitled = 0;
                $totalUsed = 0;
                $totalRemaining = 0;
            @endphp
            @foreach($balances as $year => $data)
            <tr>
                @php
                    $totalEntitled += $data['entitled'];
                    $totalUsed += $data['used'];
                    $totalRemaining += $data['remaining'];
                @endphp
                <td>{{ $year }}</td>
                <td>{{ (float) $data['entitled'] }}</td>

                <td>{{ $data['used'] }}</td>
                <td>{{ $data['remaining'] }}</td>
            </tr>
            @endforeach
            <tr>
                <th>Total</th>
                <td></td>
                <td></td>
                <td>{{ $totalRemaining }}</td>
            </tr>
        </tbody>
    </table>
</div>