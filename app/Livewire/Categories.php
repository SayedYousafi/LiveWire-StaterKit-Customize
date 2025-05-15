<?php

namespace App\Livewire;

use App\Models\Category;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Category management')]
class Categories extends Component
{
    public bool $enableEdit = false;
    public bool $isUpdate=false;
    public $name, $is_ignored_value,  $categoryId;
    public function render()
    {
        return view('livewire.categories')->with([
            'categories' => Category::all(),
        ]);
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required',
            'is_ignored_value' => 'required',
        ]);
        $done = Category::create($validated);
        if($done)
        {
            session()->flash('success', 'Category added successfully !');
            Flux::modal('myModal')->close();
        }
        $this->reset();
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $id;
        $this->isUpdate = true;
        Flux::modal('myModal')->show();
        $this->name = $category->name;
        $this->is_ignored_value = $category->is_ignored_value;
    }

    public function update()
    {
        $validated = $this->validate([
            'name' => 'required',
            'is_ignored_value' => 'required',
        ]);
        $done = Category::where('id', $this->categoryId)->update($validated);
        if($done)
        {
            session()->flash('success', 'Category updated successfully !');
            Flux::modal('myModal')->close();
        }
        $this->reset();
        $this->isUpdate = false;
    }

    public function delete($id)
    {
        dd($id);
        $category = Category::findOrFail($id);
        $done= $category->delete();
        if($done){
            session()->flash('success', 'Category deleted successfully !');
        }   
    }

      public function cancel()
    {
        $this->isUpdate = false;
        $this->enableEdit = false;
         $this->reset();
    }
}
