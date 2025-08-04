<?php
namespace App\Livewire;

use App\Models\Team;
use App\Models\User;
use App\Models\WorkProfile;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Gtech Teams')]
class Teams extends Component
{
    use WithPagination;

    public bool $enableEdit = false;

    public bool $isUpdate = false;

    public bool $active = true;

    public $TeamId;

    public string $search = '';

    public string $title = 'Teams';

    public $city;

    public $company;

    public $contact_dob;

    public $contact_number;

    public $contact_person;

    public $contact_relationship;

    public $country;

    public $designation;

    public $dob;

    public $email_business;

    public $email_private;

    public $first_name;

    public $gender;

    public $house_no;

    public $join_date;

    public $last_name;

    public $marital_status;

    public $middle_name;

    public $mobile;

    public $note;

    public $phone;

    public $photo;

    public $status;

    public $street;

    public $user_id;

    public $zip_code;
    public $work_profile_id;

    protected array $rules = [
        'first_name'    => 'required',
        'email_private' => 'required',
        'mobile'        => 'required',
        'join_date'     => 'required',
    ];

    public function render()
    {
        $users = User::whereDoesntHave('teams')->get();

        $workProfiles = WorkProfile::pluck('name', 'id')->toArray();
        $query        = Team::with('user.workProfile')->search($this->search);
        $query->where('status', $this->active == 0 ? 0 : 1);
        $teams = $query->orderBy('id')->paginate(100);

        return view('livewire.teams')->with([
            'Teams'        => $teams,
            'title'        => $this->title,
            'workProfiles' => $workProfiles,
            'users'        => $users,

        ]);
    }

    public function save()
    {
        if ($this->work_profile_id != null) {
            $updated = User::where('id', $this->user_id)->update([
                'work_profile_id' => $this->work_profile_id,
            ]);
        }
        if ($this->status == 'Active') {
            $this->status = 1;
        }
        if ($this->status == 'inActive') {
            $this->status = 0;
        }

        $this->validate();

        $created = Team::create($this->getTeamData());

        if (! $created) {
            session()->flash('error', 'Something went wrong in creating new Team');
            return;
        }

        Flux::modal('myModal')->close();
        session()->flash('success', 'Team added successfully');
        $this->updateWorkProfile();
        $this->reset();
    }

    public function edit($id)
    {

        $Team          = Team::findOrFail($id);
        $this->user_id = $Team->user_id;
        //dd($this->user_id);
        $this->TeamId   = $id;
        $this->isUpdate = true;
        $this->fillTeamData($Team);
        $this->userJoinDateUpdate();
        Flux::modal('myModal')->show();
    }

    public function update()
    {
        if ($this->work_profile_id != null) {
            $updated = User::where('id', $this->user_id)->update([
                'work_profile_id' => $this->work_profile_id,
            ]);
        }

        //dd($this->status);
        if ($this->status == 'Active') {
            $this->status = 1;
            $updated = User::where('id', $this->user_id)->update([
                'isActive' => 1,
            ]);
        } 
        if ($this->status == 'inActive') {
            $this->status = 0;
            //set user too as inActive
            $updated = User::where('id', $this->user_id)->update([
                'isActive' => 0,
            ]);
        }

        $this->validate();

        $updated = Team::where('id', $this->TeamId)->update($this->getTeamData());

        if (! $updated) {
            session()->flash('error', 'Something went wrong in updating Team');
            return;
        }

        Flux::modal('myModal')->close();
        session()->flash('success', 'Team updated successfully');

        $this->userJoinDateUpdate();
        $this->reset();
    }
    //update join_date of users table;
    public function userJoinDateUpdate()
    {
        // dd($this->user_id, $this->join_date);
        User::where('id', $this->user_id)->update([
            'join_date' => $this->join_date,
        ]);
    }

    public function delete($id)
    {
        dd('Delete is temporarily blocked');
        $Team = Team::findOrFail($id);
        $Team->delete();

        $this->isUpdate = false;
        session()->flash('success', 'Team deleted successfully');
    }

    public function cancel()
    {
        $this->isUpdate = false;
        $this->reset();
    }

    private function fillTeamData(Team $Team): void
    {
        $this->fill($Team->only(array_keys($this->getTeamData())));
    }

    private function getTeamData(): array
    {
        return collect([
            'city',
            'company',
            'contact_dob',
            'contact_number',
            'contact_person',
            'contact_relationship',
            'country',
            'designation',
            'dob',
            'email_business',
            'email_private',
            'first_name',
            'gender',
            'house_no',
            'join_date',
            'last_name',
            'marital_status',
            'middle_name',
            'mobile',
            'note',
            'phone',
            'photo',
            'status',
            'street',
            'user_id',
            'zip_code',
        ])->mapWithKeys(fn($field) => [$field => $this->{$field}])->toArray();
    }
}
