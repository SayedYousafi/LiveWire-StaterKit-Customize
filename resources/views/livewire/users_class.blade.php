<?php

use App\Models\User;

use Livewire\Volt\Component;

new class extends Component {

    public function with()
    {
       return [
        'users' => User::all(),
    ];
        //dd( $users);
    }
        
}; ?>

<div>
    @php
         dd($users);
    @endphp

</div>
