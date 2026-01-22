<?php

use App\Models\JobListing;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays the jobs page', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('LaraJobs');
});

it('lists jobs from the database', function () {
    JobListing::factory()->create(['title' => 'Laravel Developer']);
    JobListing::factory()->create(['title' => 'Vue.js Developer']);

    $this->get('/')
        ->assertSee('Laravel Developer')
        ->assertSee('Vue.js Developer');
});

it('searches for jobs', function () {
    JobListing::factory()->create(['title' => 'Laravel Developer']);
    JobListing::factory()->create(['title' => 'React Developer']);

    Livewire::test('pages::jobs')
        ->set('search.query', 'Laravel')
        ->assertSee('Laravel Developer')
        ->assertDontSee('React Developer');
});

it('filters jobs by location', function () {
    JobListing::factory()->create(['title' => 'Remote Job', 'location' => 'Remote']);
    JobListing::factory()->create(['title' => 'Office Job', 'location' => 'New York']);

    Livewire::test('pages::jobs')
        ->set('search.location', 'Remote')
        ->assertSee('Remote Job')
        ->assertDontSee('Office Job');
});

it('filters jobs by salary', function () {
    JobListing::factory()->create(['title' => 'High Pay', 'salary' => '$200k']);
    JobListing::factory()->create(['title' => 'Low Pay', 'salary' => '$50k']);

    Livewire::test('pages::jobs')
        ->set('search.salary', '200')
        ->assertSee('High Pay')
        ->assertDontSee('Low Pay');
});

it('creates a job alert', function () {
    Livewire::test('pages::jobs')
        ->set('search.query', 'Laravel')
        ->call('createAlert')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('job_alerts', [
        'query' => 'Laravel',
    ]);
});

it('requires at least one filter for job alert', function () {
    Livewire::test('pages::jobs')
        ->call('createAlert')
        ->assertHasErrors(['alert']);
});

it('shows the alert modal', function () {
    Livewire::test('pages::jobs')
        ->assertSee('Create Job Alert')
        ->assertSee('Save your current filters to get notified about new jobs.');
});

it('loads more jobs', function () {
    JobListing::factory(15)->create();

    Livewire::test('pages::jobs')
        ->assertViewHas('jobs', function ($jobs) {
            return $jobs->count() === 10;
        })
        ->call('loadMore')
        ->assertViewHas('jobs', function ($jobs) {
            return $jobs->count() === 15;
        });
});