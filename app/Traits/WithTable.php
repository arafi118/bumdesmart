<?php

namespace App\Traits;

use App\Utils\TableUtil;
use Livewire\WithPagination;

trait WithTable
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'id';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setSortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }


}
