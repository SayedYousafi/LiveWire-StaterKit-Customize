<?php
namespace App\Livewire;

use App\Models\Team;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class Teams extends Component
{
    use WithPagination;

    public bool $enableEdit = false;
    public bool $isUpdate   = false;

    public $TeamId;
    public string $search = '';
    public string $title  = 'Teams';

    public $city,
    $company,
    $contact_dob,
    $contact_number,
    $contact_person,
    $contact_relationship,
    $country,
    $designation,
    $dob,
    $email_business,
    $email_private,
    $first_name,
    $gender,
    $house_no,
    $join_date,
    $last_name,
    $marital_status,
    $middle_name,
    $mobile,
    $note,
    $phone,
    $photo,
    $status,
    $street,
    $user_id,
    $zip_code;

    protected array $rules = [
        'first_name'    => 'required',
        'email_private' => 'required',
        'mobile'        => 'required',
    ];

    public function render()
    {
        return view('livewire.teams')->with([
            'Teams' => Team::search($this->search)->orderBy('id')->paginate(100),
            'title' => $this->title,
        ]);
    }

    public function save()
    {
        $this->validate();

        $created = Team::create($this->getTeamData());

        if (! $created) {
            session()->flash('error', 'Something went wrong in creating new Team');
            return;
        }

        Flux::modal('myModal')->close();
        session()->flash('success', 'Team added successfully');
        $this->reset();
    }

    public function edit($id)
    {
        $Team = Team::findOrFail($id);

        $this->TeamId   = $id;
        $this->isUpdate = true;
        $this->fillTeamData($Team);

        Flux::modal('myModal')->show();
    }

    public function update()
    {
        $this->validate();

        $updated = Team::where('id', $this->TeamId)->update($this->getTeamData());

        if (! $updated) {
            session()->flash('error', 'Something went wrong in updating Team');
            return;
        }

        Flux::modal('myModal')->close();
        session()->flash('success', 'Team updated successfully');
        $this->reset();
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
