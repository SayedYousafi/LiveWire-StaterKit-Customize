<?php
use App\Models\User;
use function Livewire\Volt\{state, computed};

//3 ways to list users

//1. state(users:fn()=>User::all());
//2. state('users',fn()=>User::all());
//3. computed($users)) // in blade you must use the word $this to access all records;
$users = computed(fn()=>User::all());
?>

<div>
    <table class="table-default">
        <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th colspan="2">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($this->users as $user )
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td><flux:button 
                    icon='x-circle'
                    size='sm'
                    variant='danger'>
                    Delete</flux:button>
                </td>
                <td><flux:button 
                    icon='pencil-square'
                    size='sm'
                    variant='primary'>
                    Edit</flux:button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>