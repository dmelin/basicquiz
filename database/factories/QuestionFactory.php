<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Question;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        $answer = $this->faker->numberBetween(1, 1000);

        return [
            'content' => "Vad är $answer + 0?", // enkel fråga som vi kan förutsäga
            'answer' => (string) $answer,       // alltid sträng
            'wrongs' => json_encode([
                (string) ($answer + 1),
                (string) ($answer + 2),
                (string) ($answer - 1)
            ]),
            'category' => 'math',
        ];
    }
}
