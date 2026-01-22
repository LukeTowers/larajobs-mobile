<?php

use App\Services\RssJobFetcher;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

uses(Tests\TestCase::class);

it('fetches and parses rss feed correctly', function () {
    $rssContent = <<<XML
<rss version="2.0" xmlns:job="https://larajobs.com">
<channel>
    <item>
        <title>Software Engineer</title>
        <link>https://larajobs.com/job/software-engineer</link>
        <description>Great job opportunity.</description>
        <pubDate>Fri, 16 Jan 2026 12:00:00 +0000</pubDate>
        <job:location>Remote</job:location>
        <job:salary>$100k</job:salary>
        <job:company>Tech Corp</job:company>
        <job:tags>PHP, Laravel</job:tags>
    </item>
</channel>
</rss>
XML;

    Http::fake([
        'larajobs.com/feed' => Http::response($rssContent, 200),
    ]);

    $fetcher = new RssJobFetcher();
    $jobs = $fetcher->fetch();

    expect($jobs)->toHaveCount(1)
        ->and($jobs[0]['title'])->toBe('Software Engineer')
        ->and($jobs[0]['link'])->toBe('https://larajobs.com/job/software-engineer')
        ->and($jobs[0]['description'])->toBe('Great job opportunity.')
        ->and($jobs[0]['pub_date'])->toBeInstanceOf(Carbon::class)
        ->and($jobs[0]['location'])->toBe('Remote')
        ->and($jobs[0]['salary'])->toBe('$100k')
        ->and($jobs[0]['company'])->toBe('Tech Corp')
        ->and($jobs[0]['tags'])->toBe('PHP, Laravel');
});

it('throws exception on failed request', function () {
    Http::fake([
        'larajobs.com/feed' => Http::response(null, 500),
    ]);

    $fetcher = new RssJobFetcher();
    $fetcher->fetch();
})->throws(\RuntimeException::class);