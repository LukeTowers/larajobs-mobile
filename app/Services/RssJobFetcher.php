<?php

namespace App\Services;

use App\Contracts\JobFetcher;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class RssJobFetcher implements JobFetcher
{
    public function fetch(): array
    {
        $response = Http::get('https://larajobs.com/feed');
        
        if ($response->failed()) {
            throw new \RuntimeException('Failed to fetch RSS feed.');
        }

        $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
        $items = [];
        $ns = 'https://larajobs.com';

        foreach ($xml->channel->item as $item) {
            $jobData = $item->children($ns);
            
            $items[] = [
                'title' => (string) $item->title,
                'link' => (string) $item->link,
                'description' => (string) $item->description,
                'pub_date' => Carbon::parse((string) $item->pubDate),
                'location' => (string) $jobData->location,
                'salary' => (string) $jobData->salary,
                'company' => (string) $jobData->company,
                'company_logo' => (string) $jobData->company_logo,
                'tags' => (string) $jobData->tags,
                'job_type' => (string) $jobData->job_type,
            ];
        }

        return $items;
    }
}