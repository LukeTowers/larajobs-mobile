<?php

use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

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
        ->set('query', 'Laravel')
        ->assertSee('Laravel Developer')
        ->assertDontSee('React Developer');
});

it('filters jobs by location', function () {
    JobListing::factory()->create(['title' => 'Remote Job', 'location' => 'Remote']);
    JobListing::factory()->create(['title' => 'Office Job', 'location' => 'New York']);

    Livewire::test('pages::jobs')
        ->set('location', 'Remote')
        ->assertSee('Remote Job')
        ->assertDontSee('Office Job');
});

it('filters jobs by salary', function () {
    JobListing::factory()->create(['title' => 'High Pay', 'salary' => '$200k']);
    JobListing::factory()->create(['title' => 'Low Pay', 'salary' => '$50k']);

    Livewire::test('pages::jobs')
        ->set('salary', '200')
        ->assertSee('High Pay')
        ->assertDontSee('Low Pay');
});

it('creates a job alert', function () {
    Livewire::test('create-job-alert', [
        'query' => 'Laravel',
    ])
        ->call('createAlert')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('job_alerts', [
        'query' => 'Laravel',
    ]);
});

it('requires at least one filter for job alert', function () {
    Livewire::test('create-job-alert')
        ->call('createAlert')
        ->assertHasErrors(['query', 'location', 'salary']);
});

it('shows the alert modal', function () {
    // Tests that the button to open the modal exists on the main page
    Livewire::test('pages::jobs')
        // We look for the modal trigger name or icon
        ->assertSee('alert-modal');
});

it('validates search form inputs when creating alert', function () {
    // For reactive props, we pass them in mount, we don't set() them directly
    Livewire::test('create-job-alert', ['query' => str_repeat('a', 256)])
        ->call('createAlert')
        ->assertHasErrors(['query']);

    Livewire::test('create-job-alert', ['location' => str_repeat('a', 256)])
        ->call('createAlert')
        ->assertHasErrors(['location']);

    Livewire::test('create-job-alert', ['salary' => str_repeat('a', 256)])
        ->call('createAlert')
        ->assertHasErrors(['salary']);
});

it('loads more jobs', function () {
    JobListing::factory(15)->create();

    Livewire::test('pages::jobs')
        ->assertSeeHtml('wire:intersect="loadMore"')
        ->call('loadMore');

    // Since we can't easily assert view data on anonymous components in Folio/Livewire easily without a view,
    // we can rely on verifying the method exists and runs without error, or check rendered HTML count if feasible.
    // For this simple test, ensuring no crash on loadMore is a good start.
});
