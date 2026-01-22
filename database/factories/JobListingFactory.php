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
        return [
            'title' => $this->faker->jobTitle,
            'link' => $this->faker->url,
            'description' => $this->faker->paragraph,
            'pub_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'location' => $this->faker->city,
            'salary' => '$' . $this->faker->numberBetween(50, 150) . 'k',
            'company' => $this->faker->company,
            'company_logo' => $this->faker->imageUrl,
            'tags' => $this->faker->words(3, true),
            'job_type' => 'FULL_TIME',
        ];
    }
}