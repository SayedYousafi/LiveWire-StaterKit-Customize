<?php

namespace App\Livewire;

use App\Models\Taric;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class Tarics extends Component
{
    use WithPagination;

    public $update = false;

    public $taricId;

    public $search = '';

    public $title = 'Tarics';

    public $code;

    public $name_en;

    public $name_de;
    public $name_cn;

    public $reguler_artikel;

    public $duty_rate;

    public $description_de;

    public $description_en;

    public function render()
    {
        return view('livewire.tarics')->with([
            'tarics' => Taric::withCount('items')->search($this->search)->paginate(25),
        ]);
    }

    public function Save()
    {
        $validated = $this->validate([
            'name_en' => 'required',
            'name_de' => 'required',
            'code' => 'required',
        ]);
        $done = Taric::create([
            'name_en' => $this->name_en,
            'name_de' => $this->name_de,
            'name_cn' => $this->name_cn,
            'reguler_artikel' => $this->reguler_artikel,
            'duty_rate' => $this->duty_rate,
            'description_de' => $this->description_de,
            'description_en' => $this->description_en,
        ]);
        if ($done) {
            Flux::modal('myModal')->close();
            session()->flash('success', 'taric added susscessfully');
            $this->reset();
        } else {
            session()->flash('error', 'Something went wrong in creating new taric');
        }
    }

    public function edit($id)
    {
        $this->taricId = $id;
        $this->update = true;
        Flux::modal('myModal')->show();
        $taric = Taric::findOrFail($id);
        $this->name_en = $taric->name_en;
         $this->name_cn = $taric->name_cn;
        $this->name_de = $taric->name_de;
        $this->description_de = $taric->description_de;
        $this->description_en = $taric->description_en;
        $this->duty_rate = $taric->duty_rate;
        $this->reguler_artikel = $taric->reguler_artikel;
        $this->code = $taric->code;

    }

    public function Update()
    {
        $validated = $this->validate([
            'name_en' => 'required',
            'name_de' => 'required',
            'code' => 'required',
        ]);
        $done = Taric::where('id', $this->taricId)->update([
            'name_en' => $this->name_en,
            'name_cn' => $this->name_cn,
            'name_de' => $this->name_de,
            'reguler_artikel' => $this->reguler_artikel,
            'duty_rate' => $this->duty_rate,
            'description_de' => $this->description_de,
            'description_en' => $this->description_en,
        ]);
        if ($done) {
            Flux::modal('myModal')->close();
            session()->flash('success', 'taric updated susscessfully');
            $this->reset();
        } else {
            session()->flash('error', 'Something went wrong in updating tarics');
        }
    }

    public function delete($id)
    {
        dd('Delete is temporary blocked');
        $taric = Taric::findOrFail($id);
        $taric->delete();
        $this->update = false;
        session()->flash('success', 'taric deleted susscessfully');
    }

    public function cancel()
    {
        $this->update = false;
        $this->reset();
    }
}
