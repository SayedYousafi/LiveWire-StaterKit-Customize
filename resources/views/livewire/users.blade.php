<?php
use App\Models\User;
use App\Models\WorkProfile;
use Illuminate\Support\Facades\Hash;

use function Livewire\Volt\{state, computed};
state(['role', 'work_profile', 'editId', 'name', 'password', 'password_confirmation' ]);
$workProfiles=computed(fn()=>WorkProfile::all());

$users = computed(fn()=>User::with('workProfile')->get());

$edit = function($id, $name){
    $this->editId = $id;
    $this->name=$name;
    $user = User::findOrFail($id);
    $this->work_profile = $user->work_profile_id;
    $this->role = $user->role;
    Flux::modal('user-modal')->show();
};

$update = function()
{
    User::where('id',$this->editId)->update([
        'role' => $this->role,
        'work_profile_id' => $this->work_profile,
    ]);
    Flux::modal('user-modal')->close();
    session()->flash('success', 'user profile updated successfuly');
};

$selectUser = function($id, $name)
{
    $this->editId = $id;
    $this->name=$name;
    $user = User::findOrFail($id);
    $this->work_profile = $user->work_profile_id;
    $this->role = $user->role;
    Flux::modal('user-password')->show();
};

$changePassword = function() {
    $validated = $this->validate([
        'password' => 'required|confirmed|min:4',
        'password_confirmation' => 'required',
    ]);

    $user = User::find($this->editId);

    if (!$user) {
        session()->flash('error', 'User not found.');
        return;
    }

    $user->password = Hash::make($validated['password']);
    $user->save();

    Flux::modal('user-password')->close();
    session()->flash('success', "Password changed/updated successfully");

    $this->password = '';
    $this->password_confirmation = '';
};
?>

<div>
    @include('partials.user-modal')
    @include('partials.user-password-modal')

    <div class="flex justify-between mb-2">
        <flux:button icon='plus-circle' size='sm' href="{{ route('register') }}"
            class="!bg-blue-800 !text-white hover:!bg-blue-700 ">New User</flux:button>
        <flux:text size='xl'>Users</flux:text>
        <flux:input class="md:w-50" wire:model.live="search" icon="magnifying-glass" placeholder="Search users"
            size='sm' />
    </div>
    @if (session('success'))
    <flux:callout icon='check-circle' variant='success' heading="{{ session('success') }}" class='mb-2' />
    @endif
    <table class="table-default">
        <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Name</th>
                <th>Work profile</th>
                <th>Email</th>
                <th>Role</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($this->users as $user )
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->workProfile->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>

                <td>
                    <flux:button wire:confirm='Are you sure' icon='x-circle' size='sm' variant='danger'
                        wire:click='delete({{ $user->id }})'>
                        Delete</flux:button>
                </td>
                <td>
                    <flux:button icon='pencil-square' size='sm' variant='primary'
                        wire:click="edit({{ $user->id}}, '{{ $user->name }}')">
                        Edit</flux:button>
                </td>
                <td>
                    <flux:button icon='arrow-path-rounded-square' size='sm' variant='filled'
                        wire:click="selectUser({{ $user->id }}, '{{ $user->name }}')">
                        Change Password</flux:button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>