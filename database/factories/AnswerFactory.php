<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Answer;
use App\Models\Question;

class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition()
    {
        return [
            'user_token' => $this->faker->regexify('[A-Z]{5}'),
            'question_id' => Question::factory(),
            'chosen_answer' => '42',
            'correct' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
