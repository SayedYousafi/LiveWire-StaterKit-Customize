<?php
namespace App\Livewire;

use App\Models\WorkProfile;
use Flux\Flux;
use Livewire\Component;

class WorkingProfiles extends Component
{
    public $title        = 'Work Profiles';
    public $working_days = [];
    public $entitlement  = '';
    public $name         = '';
    public $public_holiday;
    public $editId;
    public function render()
    {
        $profiles = WorkProfile::all();
        return view('livewire.working-profiles')->with([
            'profiles' => $profiles,
        ]);
    }

    public function save()
    {
        //dd($this->working_days);
        WorkProfile::create([
            'name'         => $this->name,
            'entitlement'  => $this->entitlement,
            'working_days' => $this->working_days,
            'public_holiday' => $this->public_holiday,
        ]);
        session()->flash('success', 'Profile created successfully');
        Flux::modal('working-profile')->close();
    }

    public function delete($id)
    {
        $delete = WorkProfile::findOrFail($id);
        $delete->delete();

        session()->flash('success', 'Profile deleted successfully');
    }
    public function edit($id)
    {
        $edit = WorkProfile::findOrFail($id);
        $this->editId = $id;
        $this->name = $edit->name;
        $this->entitlement = $edit->entitlement;
        $this->working_days = $edit->working_days;
        $this->public_holiday = $edit->public_holiday;
        Flux::modal('working-profile')->show();   
    }

    public function update()
    {
        WorkProfile::where('id', $this->editId)->update([
            'name'         => $this->name,
            'entitlement'  => $this->entitlement,
            'working_days' => $this->working_days,
            'public_holiday' => $this->public_holiday
        ]);
        session()->flash('success', 'Profile created successfully');
        Flux::modal('working-profile')->close();
    }
    public function cancel()
    {
        $this->reset();
    }
}
