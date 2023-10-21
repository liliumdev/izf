<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        return [
            'email' => $this->faker->email,
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'is_public' => true,
        ];
    }
}
