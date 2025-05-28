<?php
namespace App\Livewire;

use App\Models\Cargo_type;
use Flux\Flux;
use Livewire\Component;

class CargoTypes extends Component
{
    public $cargoTypeId;
    public string $search   = '';
    public string $title    = 'cargotypes';
    public $isUpdate        = false;
    public bool $enableEdit = false;

    public $cargo_type, $time_pre, $time_rec, $time_ship;

    public function render()
    {
        $cargoTypes = Cargo_type::paginate(10);
        return view('livewire.cargo-types')->with([
            'cargoTypes' => $cargoTypes,
        ]);
    }

    public function save()
    {
        $this->validate([
            'cargo_type' => 'required|string|max:255',
            'time_pre'   => 'required|integer',
            'time_rec'   => 'required|integer',
            'time_ship'  => 'required|integer',
        ]);

        $created = Cargo_type::create($this->getCargoTypeData());

        if (! $created) {
            session()->flash('error', 'Something went wrong in creating new Cargo Type');
            return;
        }

        Flux::modal('myModal')->close();
        session()->flash('success', 'Cargo Type added successfully');
        $this->reset();
    }
    public function edit($id)
    {
        $cargoType = Cargo_type::findOrFail($id);

        $this->cargoTypeId = $id;
        $this->isUpdate    = true;
        $this->fill($cargoType->only([
            'cargo_type', 'time_pre', 'time_rec', 'time_ship',
        ]));

        Flux::modal('myModal')->show();
    }

    public function update()
    {
        $this->validate([
            'cargo_type' => 'required|string|max:255',
            'time_pre'   => 'required|integer',
            'time_rec'   => 'required|integer',
            'time_ship'  => 'required|integer',
        ]);

        $updated = Cargo_type::where('id', $this->cargoTypeId)
            ->update($this->getCargoTypeData());

        if (! $updated) {
            session()->flash('error', 'Something went wrong in updating Cargo Type');
            return;
        }

        Flux::modal('myModal')->close();
        session()->flash('success', 'Cargo Type updated successfully');
        $this->reset();
    }

    public function delete($id)
    {
        dd('Delete is temporarily blocked');

        $cargoType = Cargo_type::findOrFail($id);
        $cargoType->delete();

        $this->isUpdate = false;
        session()->flash('success', 'Cargo Type deleted successfully');
    }
    private function getCargoTypeData(): array
    {
        return [
            'cargo_type' => $this->cargo_type,
            'time_pre'   => $this->time_pre,
            'time_rec'   => $this->time_rec,
            'time_ship'  => $this->time_ship,
        ];
    }

    public function cancel()
    {
        $this->isUpdate = false;
        $this->reset();
    }

}
