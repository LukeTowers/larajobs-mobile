<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class JobSearchForm extends Form
{
    #[Validate('nullable|string|max:2')]
    public $query = '';

    #[Validate('nullable|string|max:255')]
    public $location = '';

    #[Validate('nullable|string|max:255')]
    public $salary = '';
}
