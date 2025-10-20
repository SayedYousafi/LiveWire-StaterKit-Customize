<?php
namespace App\Livewire;

use App\Models\Cargo;
use App\Models\Cargo_type;
use App\Models\Customer;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class Cargos extends Component
{
    use WithPagination;

    public $cargoId;

    public string $search = '';

    public string $title = 'Cargos';

    public $isUpdate = false;

    public bool $enableEdit = false;

    public $cargo_no;

    public $cargo_status = 'Open';

    public $cargo_type_id;

    public $customer_id;

    public $dep_date;

    public $eta;

    public $remark;

    public $shipped_at;
    public $online_track;
    public $filterByStatus = '';

    public function render()
    {

        $query = Cargo::with(['cargoType', 'customer']);

        if ($this->filterByStatus && $this->filterByStatus !== 'All') {
            $query->where('cargo_status', $this->filterByStatus);
        } elseif ($this->filterByStatus !== 'All' && ! $this->filterByStatus) {
            // On page load (no filter selected)
            $query->where('cargo_status', '!=', 'Arrived');
        }

        $cargos = $query->orderBy('id', 'desc')->paginate(30);

        $customers   = Customer::select('id', 'customer_company_name', 'company_subname', 'delivery_subname')->get();
        $cargo_types = Cargo_type::all();

        return view('livewire.cargos')->with([
            'cargos'      => $cargos,
            'customers'   => $customers,
            'cargo_types' => $cargo_types,
        ]);
    }

    public function save()
    {
        $validate = $this->validate([
            'cargo_no'      => 'required',
            'customer_id'   => 'required',
            'cargo_type_id' => 'required',
        ]);

        $created = Cargo::create($this->getCargoData());

        if (! $created) {
            session()->flash('error', 'Something went wrong in creating new Cargo');
            return;
        }

        Flux::modal('myModal')->close();
        session()->flash('success', 'Cargo added successfully');
        $this->reset();
    }

    public function edit($id)
    {
        $cargo = Cargo::findOrFail($id);

        $this->cargoId  = $id;
        $this->isUpdate = true;
        $this->fill($cargo->only(array_keys($this->getCargoData())));

        Flux::modal('myModal')->show();
    }

    public function update()
    {
        $validated = $this->validate([
            'cargo_no'      => 'required',
            'customer_id'   => 'required',
            'cargo_type_id' => 'required',

        ]);

        $updated = Cargo::where('id', $this->cargoId)->update($this->getCargoData());

        if (! $updated) {
            session()->flash('error', 'Something went wrong in updating Cargo');
            return;
        }

        Flux::modal('myModal')->close();
        session()->flash('success', 'Cargo updated successfully');
        $this->reset();
    }

    public function delete($id)
    {
        dd('Delete is temporarily blocked');

        $cargo = Cargo::findOrFail($id);
        $cargo->delete();

        $this->isUpdate = false;
        session()->flash('success', 'Cargo deleted successfully');
    }

    public function cancel()
    {
        $this->isUpdate = false;
        $this->reset();
    }

    private function getCargoData(): array
    {
        return [
            'cargo_no'      => $this->cargo_no,
            'cargo_status'  => $this->cargo_status,
            'cargo_type_id' => $this->cargo_type_id,
            'customer_id'   => $this->customer_id,
            'dep_date'      => $this->dep_date,
            'eta'           => $this->eta,
            'remark'        => $this->remark,
            'shipped_at'    => $this->shipped_at,
            'online_track'  => $this->online_track,
        ];
    }

    public function arrive($id)
    {
        //dd('You need to have a db field to capture date of arrival', $id);
        $updated = Cargo::where('id', $id)->update([
            'cargo_status' => 'Arrived',
            'eta'          => date('Y-m-d'),
        ]);
    }
}
