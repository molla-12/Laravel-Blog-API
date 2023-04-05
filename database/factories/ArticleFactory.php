<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition()
    {
        $category = Category::pluck('id')->toArray();
        $user = User::pluck('id')->toArray();

        return [
            'category_id' => $this->randomElement($category),
            'user_id' => $this->randomElement($user),
            'title' => $this->faker->text(),
            'details' => $this->faker->sentence(),
        ];
    }
}