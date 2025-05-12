<?php

namespace App\Livewire;

use App\Models\Post as ModelsPost;
use Flux\Flux;
use Livewire\Component;

class Post extends Component
{
    public $title, $body, $postId, $update = false;
    public function render()
    {
        return view('livewire.posts')->with([
            'posts' => ModelsPost::all(),
        ]);
    }

    public function save()
    {
        $this->update=false;
        $validated = $this->validate([
            'title' => 'required',
            'body' => 'required', 
        ]);
        
        ModelsPost::create($validated);
        Flux::modal('postModal')->close();
        session()->flash('success','Post added successfully !');
        $this->reset();
    }

    public function edit($id)
    {
        $this->postId  = $id;
        $this->update = true;
        $post = ModelsPost::findOrFail($id);  
        $this->title = $post->title;
        $this->body = $post->body;
        Flux::modal('postModal')->show();
    }

    public function updatePost()
    {
        $validated = $this->validate([
            'title' => 'required',
            'body' => 'required', 
        ]);
        
        ModelsPost::where('id', $this->postId)->update($validated);
        Flux::modal('postModal')->close();
        session()->flash('success','Post updated successfully !');
        $this->reset();
    }

    public function delete ($id)
    {
        $post = ModelsPost::findOrFail($id);
        $post->delete();  
        session()->flash('success','Post deleted successfully !');
    }
}
