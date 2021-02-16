<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Entry;
use App\Models\User;

class EntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Entry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'username' => $this->faker->userName,
            'password' => $this->faker->word,
            'url' => $this->faker->url,
            'comment' => $this->faker->text,
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
        ];
    }
}
