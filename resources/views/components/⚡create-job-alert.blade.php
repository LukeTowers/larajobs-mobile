<?php

use App\Models\JobAlert;
use Flux\Flux;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    #[Reactive]
    #[Validate('required_without_all:location,salary|nullable|string|max:255')]
    public string $query = '';

    #[Reactive]
    #[Validate('required_without_all:query,salary|nullable|string|max:255')]
    public string $location = '';

    #[Reactive]
    #[Validate('required_without_all:location,query|nullable|string|max:255')]
    public string $salary = '';

    #[NoReturn]
    public function createAlert(): void
    {
        $this->validate();

        JobAlert::create([
            'query' => $this->query,
            'location' => $this->location,
            'salary' => $this->salary,
        ]);

        Flux::toast('Alert created!', variant: 'success');
        Flux::modal('alert-modal')->close();
    }
};
?>

<div class="space-y-6">
    <div>
        <flux:heading size="lg">Create Job Alert</flux:heading>
        <flux:subheading>Save your current filters to get notified about new jobs.</flux:subheading>
    </div>

    <div class="space-y-4">
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="text-sm text-red-500">{{ $error }}</div>
            @endforeach
        @endif
        @if($query)
            <div class="flex flex-col gap-1">
                <flux:label>Keywords</flux:label>
                <div class="text-sm font-medium">{{ $query }}</div>
            </div>
        @endif

        @if($location)
            <div class="flex flex-col gap-1">
                <flux:label>Location</flux:label>
                <div class="text-sm font-medium">{{ $location }}</div>
            </div>
        @endif

        @if($salary)
            <div class="flex flex-col gap-1">
                <flux:label>Salary</flux:label>
                <div class="text-sm font-medium">{{ $salary }}</div>
            </div>
        @endif
    </div>

    <div class="flex gap-2">
        <flux:spacer/>
        <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
        </flux:modal.close>
        <flux:button variant="primary" wire:click="createAlert">Save Alert</flux:button>
    </div>
</div>
