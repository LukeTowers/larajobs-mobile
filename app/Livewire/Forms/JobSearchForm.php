<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class JobSearchForm extends Form
{
    #[Validate('required_without_all:location,salary|nullable|string|max:255')]
    public $query = '';

    #[Validate('required_without_all:query,salary|nullable|string|max:255')]
    public $location = '';

    #[Validate('required_without_all:location,query|nullable|string|max:255')]
    public $salary = '';
}
