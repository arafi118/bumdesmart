<?php

namespace App\Livewire;

use Livewire\Component;

class Pengaturan extends Component
{
    public $title;

    public function render()
    {
        $this->title = 'Pengaturan';

        return view('livewire.Pengaturan')->layout('layouts.app', ['title' => $this->title]);
    }
}
