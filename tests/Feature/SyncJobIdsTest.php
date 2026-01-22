<?php

use App\Contracts\JobFetcher;
use App\Models\JobListing;
use Illuminate\Support\Facades\Artisan;
use Mockery\MockInterface;
use Carbon\Carbon;

it('syncs jobs from the fetcher service', function () {
    $this->mock(JobFetcher::class, function (MockInterface $mock) {
        $mock->shouldReceive('fetch')
            ->once()
            ->andReturn([
                [
                    'title' => 'Test Job 1',
                    'link' => 'https://example.com/job1',
                    'description' => 'Description 1',
                    'pub_date' => Carbon::now(),
                ],
                [
                    'title' => 'Test Job 2',
                    'link' => 'https://example.com/job2',
                    'description' => 'Description 2',
                    'pub_date' => Carbon::now()->subDay(),
                ],
            ]);
    });

    Artisan::call('app:sync-jobs');

    $this->assertDatabaseCount('job_listings', 2);
    $this->assertDatabaseHas('job_listings', [
        'title' => 'Test Job 1',
        'link' => 'https://example.com/job1',
    ]);
});

it('handles errors gracefully', function () {
    $this->mock(JobFetcher::class, function (MockInterface $mock) {
        $mock->shouldReceive('fetch')
            ->once()
            ->andThrow(new \Exception('API Error'));
    });

    Artisan::call('app:sync-jobs');

    // Should verify it logs error or at least doesn't crash the command execution in a way that stops tests awkwardly
    // The command catches the exception and prints error.
    
    // We can verify no jobs were added (assuming empty DB start)
    $this->assertDatabaseCount('job_listings', 0);
});