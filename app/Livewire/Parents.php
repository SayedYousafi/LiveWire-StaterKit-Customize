<?php
namespace App\Livewire;

use App\Models\Parents as Parentz;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class Parents extends Component
{
    use WithPagination;

    public bool $showInactive = false;
    public bool $enableEdit = false;
    public bool $isUpdate = false;
   
    public string $search = '';
    public string $title  = 'Parents';

    public $parentId;
    public $de_id, $de_no, $id, $is_active, $is_nwv, $is_var_unilingual;
    public $name_cn, $name_de, $name_en;
    public $rank, $taric_id;
    public $var_de_1, $var_de_2, $var_de_3;
    public $var_en_1, $var_en_2, $var_en_3;

    protected array $rules = [

        'name_en' => 'required|string|max:255',
        'name_cn' => 'nullable|string|max:255',
        'name_de' => 'nullable|string|max:255',
    ];
    public function updatedShowInactive()
    {
        $this->resetPage(); // forces re-evaluation of the query and pagination
    }

    public function render()
    {
        $parentsQuery = Parentz::search($this->search)->withCount('items');

        if (! $this->showInactive) {
            $parentsQuery->where('is_active', '1');
        }

        return view('livewire.parents')->with([
            'Parents' => $parentsQuery->orderBy('id', 'desc')->paginate(100),
            'title'   => $this->title,
        ]);

    }

    public function save()
    {
        $this->validate();

        $created = Parentz::create($this->getParentData());

        if (! $created) {
            session()->flash('error', 'Something went wrong in creating new Parent');
            return;
        }

        Flux::modal('myModal')->close();
        session()->flash('success', 'Parent added successfully');
        $this->reset();
    }

    public function edit($id)
    {
        $parent = Parentz::findOrFail($id);

        $this->parentId = $id;
        $this->isUpdate = true;
        $this->fill($parent->only(array_keys($this->getParentData())));

        Flux::modal('myModal')->show();
    }

    public function update()
    {
        $this->validate();

        $updated = Parentz::where('id', $this->parentId)->update($this->getParentData());

        if (! $updated) {
            session()->flash('error', 'Something went wrong in updating Parent');
            return;
        }

        Flux::modal('myModal')->close();
        session()->flash('success', 'Parent updated successfully');
        $this->reset();
    }

    public function delete($id)
    {
        dd('Delete is temporarily blocked');
        $parent = Parentz::findOrFail($id);
        $parent->delete();

        $this->isUpdate = false;
        session()->flash('success', 'Parent deleted successfully');
    }

    public function cancel()
    {
        $this->isUpdate = false;
    }

    private function getParentData(): array
    {
        return [
            'de_id'             => $this->de_id,
            'de_no'             => $this->de_no,
            'is_active'         => $this->is_active,
            'is_nwv'            => $this->is_nwv,
            'is_var_unilingual' => $this->is_var_unilingual,
            'name_en'           => $this->name_en,
            'name_cn'           => $this->name_cn,
            'name_de'           => $this->name_de,
            'rank'              => $this->rank,
            'taric_id'          => $this->taric_id,
            'var_de_1'          => $this->var_de_1,
            'var_de_2'          => $this->var_de_2,
            'var_de_3'          => $this->var_de_3,
            'var_en_1'          => $this->var_en_1,
            'var_en_2'          => $this->var_en_2,
            'var_en_3'          => $this->var_en_3,
        ];
    }
}
