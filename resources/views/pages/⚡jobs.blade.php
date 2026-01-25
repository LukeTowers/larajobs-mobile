<?php

use App\Models\JobListing;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    public string $query = '';

    public string $location = '';

    public string $salary = '';

    public int $limit = 10;

    public function loadMore(): void
    {
        $this->limit += 10;
    }

    public function with(): array
    {
        $query = JobListing::latest('pub_date');

        if ($this->query) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->query.'%')
                    ->orWhere('description', 'like', '%'.$this->query.'%')
                    ->orWhere('tags', 'like', '%'.$this->query.'%')
                    ->orWhere('company', 'like', '%'.$this->query.'%');
            });
        }

        if ($this->location) {
            $query->where('location', 'like', '%'.$this->location.'%');
        }

        if ($this->salary) {
            $query->where('salary', 'like', '%'.$this->salary.'%');
        }

        return [
            'jobs' => $query->take($this->limit)->get(),
        ];
    }
};
?>

<div>
    <flux:main container="job-list">
        <div class="space-y-6">
            <flux:card class="space-y-4">
                <div class="flex gap-2">
                    <div class="flex-1">
                        <flux:input
                            wire:model.live.debounce.300ms="query"
                            icon="magnifying-glass"
                            placeholder="Search keywords..."
                        />
                    </div>

                    <flux:modal.trigger name="alert-modal">
                        <flux:button variant="ghost" icon="bell" class="shrink-0"/>
                    </flux:modal.trigger>
                </div>

                <div x-data="{ expanded: false }">
                    <button
                        @click="expanded = ! expanded"
                        class="flex items-center gap-2 text-sm font-medium text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 transition-colors w-full"
                    >
                        <flux:icon name="adjustments-horizontal" class="size-4"/>
                        <span>Filters</span>
                        <flux:icon name="chevron-down" class="size-4 ml-auto transition-transform duration-200"
                                   ::class="expanded ? 'rotate-180' : ''"/>
                    </button>

                    <div
                        x-show="expanded"
                        x-collapse
                        class="grid gap-4 mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700"
                        style="display: none;"
                    >
                        <flux:input
                            wire:model.live.debounce.300ms="location"
                            label="Location"
                            placeholder="e.g. Remote, USA..."
                            icon="map-pin"
                        />
                        <flux:input
                            wire:model.live.debounce.300ms="salary"
                            label="Salary"
                            placeholder="e.g. $100k..."
                            icon="currency-dollar"
                        />
                    </div>
                </div>
            </flux:card>

            @island(always: true)
                <div class="grid gap-6" wire:transition>
                    @forelse($jobs as $job)
                        <flux:card class="hover:border-zinc-300 dark:hover:border-zinc-600 transition-colors">
                            <div class="flex flex-col gap-4">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div
                                        class="size-10 sm:size-12 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center shrink-0 overflow-hidden">
                                        @if($job->company_logo && !str_ends_with($job->company_logo, '/'))
                                            <img src="{{ $job->company_logo }}" alt="{{ $job->company }}"
                                                 class="size-full object-contain">
                                        @else
                                            <flux:icon name="building-office" class="size-5 sm:size-6 text-zinc-400"/>
                                        @endif
                                    </div>

                                    <div class="space-y-1 w-full min-w-0">
                                        <flux:heading size="lg" class="wrap-break-word">
                                            <a href="{{ $job->link }}" target="_blank"
                                               class="hover:underline decoration-zinc-400 underline-offset-4">
                                                {{ $job->title }}
                                            </a>
                                        </flux:heading>

                                        <div class="text-sm text-zinc-500 dark:text-zinc-400 space-y-1">
                                            @if($job->company)
                                                <div
                                                    class="font-medium text-zinc-800 dark:text-zinc-200">{{ $job->company }}</div>
                                            @endif

                                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                                                @if($job->location)
                                                    <div class="flex items-center gap-1 shrink-0">
                                                        <flux:icon name="map-pin" class="size-3.5"/>
                                                        <span>{{ $job->location }}</span>
                                                    </div>
                                                @endif

                                                @if($job->salary)
                                                    <div class="flex items-center gap-1 shrink-0">
                                                        <flux:icon name="currency-dollar" class="size-3.5"/>
                                                        <span>{{ $job->salary }}</span>
                                                    </div>
                                                @endif

                                                <div class="flex items-center gap-1 shrink-0">
                                                    <flux:icon name="calendar" class="size-3.5"/>
                                                    <span>{{ $job->pub_date?->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <flux:text class="line-clamp-3 wrap-break-word">
                                    {!! strip_tags($job->description) !!}
                                </flux:text>

                                <div class="shrink-0 w-full">
                                    <flux:button href="{{ $job->link }}" target="_blank" variant="primary" class="w-full">
                                        Apply Now
                                    </flux:button>
                                </div>
                            </div>
                        </flux:card>
                    @empty
                        <flux:card>
                            <flux:text>No jobs found.</flux:text>
                        </flux:card>
                    @endforelse
                </div>
            @endisland
            @if($jobs->isNotEmpty() && $jobs->count() >= $limit)
                <div
                    wire:intersect="loadMore"
                    class="h-10 flex items-center justify-center text-zinc-400"
                >
                    <flux:icon name="arrow-path" class="size-5 animate-spin mr-2"/>
                    Loading more...
                </div>
            @endif
        </div>
    </flux:main>

    <flux:modal name="alert-modal" class="min-w-[20rem]">
        <livewire:create-job-alert :$query :$location :$salary />
    </flux:modal>
</div>
