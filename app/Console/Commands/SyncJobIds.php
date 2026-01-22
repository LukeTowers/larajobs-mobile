<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobListing;
use App\Contracts\JobFetcher;

class SyncJobIds extends Command
{
    protected $signature = 'app:sync-jobs';
    protected $description = 'Fetch and sync jobs from LaraJobs RSS feed';

    public function handle(JobFetcher $jobFetcher)
    {
        $this->info('Fetching jobs from LaraJobs...');

        try {
            $items = $jobFetcher->fetch();
            $count = 0;

            foreach ($items as $item) {
                JobListing::updateOrCreate(
                    ['link' => $item['link']],
                    [
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'pub_date' => $item['pub_date'],
                        'location' => $item['location'] ?? null,
                        'salary' => $item['salary'] ?? null,
                        'company' => $item['company'] ?? null,
                        'company_logo' => $item['company_logo'] ?? null,
                        'tags' => $item['tags'] ?? null,
                        'job_type' => $item['job_type'] ?? null,
                    ]
                );
                $count++;
            }

            $this->info("Successfully synced {$count} jobs.");

        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}