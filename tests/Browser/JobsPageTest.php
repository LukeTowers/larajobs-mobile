<?php

use App\Models\JobListing;
use function Pest\Laravel\assertDatabaseHas;

it('can create a job alert for the current search', function () {
    // 1. Visit the home page
    // We use visit('/') instead of Livewire::visit() because we are testing a full page
    // with anonymous components and global assets (Flux, Tailwind) that require the full layout context.
    $browser = visit('/');

    $browser->assertVisible('input[placeholder="Search keywords..."]');

    // 2. Type "Laravel" into the search input
    $browser->type('input[placeholder="Search keywords..."]', 'Laravel');

    // Wait for debounce
    sleep(1);

    // 3. Click the bell button.
    $browser->click('[data-flux-main] button.shrink-0');

    // 4. Wait for modal to appear
    $browser->assertSee('Create Job Alert');
    $browser->assertSee('Save your current filters');

    // 5. Verify "Laravel" is shown in the modal
    $browser->assertSee('Laravel');

    // 6. Click Save Alert
    $browser->click('button:has-text("Save Alert")');

    // 7. Assert success toast/message
    $browser->assertSee('Alert created!');

    // 8. Assert DB
    assertDatabaseHas('job_alerts', [
        'query' => 'Laravel',
    ]);
});

it('loads more jobs when scrolling to the bottom', function () {
    // Arrange: Create jobs distributed across 3 pages (Limit is 10)

    // Page 1: Job 1
    JobListing::factory()->create(['title' => 'JOB_PAGE_1', 'pub_date' => now()]);
    // Page 1: Jobs 2-10
    JobListing::factory()->count(9)->create(['pub_date' => now()->subMinutes(10)]);

    // Page 2: Jobs 11-15
    JobListing::factory()->count(5)->create(['pub_date' => now()->subMinutes(20)]);
    // Page 2: Job 16
    JobListing::factory()->create(['title' => 'JOB_PAGE_2', 'pub_date' => now()->subMinutes(30)]);
    // Page 2: Jobs 17-20
    JobListing::factory()->count(4)->create(['pub_date' => now()->subMinutes(40)]);

    // Page 3: Jobs 21-25
    JobListing::factory()->count(5)->create(['pub_date' => now()->subMinutes(50)]);
    // Page 3: Job 26
    JobListing::factory()->create(['title' => 'JOB_PAGE_3', 'pub_date' => now()->subDays(1)]);

    // Act: Visit the page
    $browser = visit('/');

    // Assert: Check Page 1 is visible
    $browser->assertSee('JOB_PAGE_1');
    $browser->assertDontSee('JOB_PAGE_2');
    $browser->assertDontSee('JOB_PAGE_3');

    // Scroll to the bottom to trigger load of Page 2
    $browser->script('window.scrollTo(0, document.body.scrollHeight)');

    // Wait for Page 2 job to appear
    $browser->assertSee('JOB_PAGE_2', 10);
    $browser->assertDontSee('JOB_PAGE_3');

    // Scroll to the bottom again to trigger load of Page 3
    sleep(1);
    $browser->script('window.scrollTo(0, document.body.scrollHeight)');

    // Wait for Page 3 job to appear
    $browser->assertSee('JOB_PAGE_3', 10);
});
