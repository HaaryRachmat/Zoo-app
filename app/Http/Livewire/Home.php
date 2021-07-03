<?php

namespace App\Http\Livewire;

use App\Models\Animal;
use Livewire\Component;

class Home extends Component
{
    public $animals;
    public function render()
    {
        $this->animals = Animal::all();
        return view('livewire.home')
            ->layout('layouts.home');
    }
}
