<?php

namespace App\Services;
use App\Models\Question;
use App\Helpers\QuestionHelper;

class NumbersApiService
{
    public function fetchQuestion()
    {
        $found = false;
        $questionExists = false;
        $maxAttempts = 20; // Max attempts to find a valid question

        $types = [
            'math' => 'math',
            'trivia' => 'trivia',
            'year' => 'year',
        ];
        $randomType = $types[array_rand($types)] ?? 'trivia';

        while (!$found && !$questionExists && $maxAttempts > 0) {
            $response = file_get_contents("http://numbersapi.com/random/{$randomType}?json");
            if ($response === false) {
                return null; // Handle error in fetching data
            }
            
            $response = json_decode($response, true);

            // Check that the number is a numeric value
            if (!isset($response['number']) || !is_numeric($response['number'])) {
                continue; // Skip if the number is not valid
            }
            
            $questionExists = Question::where('content', $response['text'])
                ->where('answer', $response['number'])
                ->exists();

            $found = $response['found'] ?? false;

            $maxAttempts--;
        }

        if (!$found || $questionExists) {
            return null;
        }
        
        $newQuestion = [
            'content' => preg_replace('/\b' . preg_quote($response['number'], '/') . '\b/', '___', $response['text'], 1),
            'answer' => $response['number'],
            'category' => $response['type'],
            'wrongs' => QuestionHelper::generateWrongs($response['number'], 3),
        ];
        return $newQuestion;
    }
}
