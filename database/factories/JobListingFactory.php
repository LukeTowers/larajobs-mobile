<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobListing>
 */
class JobListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $knownTitles = ['PHP Developer', 'Laravel Developer', 'Designer', 'Manager', 'Tester'];
        $locations = ['Remote', 'New York', 'London', 'San Francisco', 'Berlin'];
        $salaries = ['$50k - $70k', '$80k - $100k', '$100k - $120k', '$120k - $150k', '$150k+'];

        return [
            'title' => $this->faker->randomElement($knownTitles),
            'link' => $this->faker->url,
            'description' => $this->faker->paragraph,
            'pub_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'location' => $this->faker->randomElement($locations),
            'salary' => $this->faker->randomElement($salaries),
            'company' => $this->faker->company,
            'company_logo' => 'https://avatars.laravel.cloud/'.$this->faker->url,
            'tags' => implode(',', $this->faker->words(3)),
            'job_type' => 'FULL_TIME',
        ];
    }
}
