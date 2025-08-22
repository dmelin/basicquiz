<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Question;
use App\Services\NumbersApiService;

class QuestionController extends Controller
{
    private $numbersApi;

    public function __construct(NumbersApiService $numbersApi)
    {
        $this->numbersApi = $numbersApi;
    }

    public function getNextQuestion(Request $request, $user_token)
    {

        $userAnswers = Answer::where('user_token', $user_token)->get();

        $nextQuestion = Question::whereNotIn('id', $userAnswers->pluck('question_id'))
            // ->inRandomOrder()
            ->first();

        if (!$nextQuestion) {
            $nextQuestion = $this->numbersApi->fetchQuestion();

            if (!$nextQuestion) {
                return response()->json([
                    'error' => 'No more questions available.',
                ], 404);
            }

            // Store the new question in the database
            $nextQuestion = Question::create($nextQuestion);
        }

        $playerResults = $this->getGameResults($user_token); // Get game results for the user

        if ($playerResults['current_streak'] === 10) {
            $return =[
                'message' => 'Congratulations! You have reached a streak of 10 correct answers! YOU WIN!',
                'victory' => true,
            ];
        } else {
            $return = [
                'question' => $nextQuestion,
            ];
        }

        $return['results'] = $playerResults; // Include game results in the response
        
        return response()->json($return);
    }

    public function checkAnswer(Request $request, $user_token)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required',
        ]);

        $question = Question::find($validated['question_id']);

        // Ensure user_token is trimmed to avoid issues with leading/trailing spaces
        $user_token = trim($user_token);

        $alreadyAnswered = Answer::where('user_token', $user_token)
            ->where('question_id', $question->id)
            ->exists();

        // One attempt per question!
        if ($alreadyAnswered) {
            return response()->json([
                'error' => 'You have already answered this question.',
            ], 400);
        }


        $isCorrect = $question->answer == $validated['answer'];

        Answer::create([
            'user_token' => $user_token,
            'question_id' => $question->id,
            'correct' => $isCorrect,
            'chosen_answer' => $validated['answer'],
        ]);

        return response()->json([
            'correct' => $isCorrect,
        ]);
    }

    private function getGameResults($user_token)
    {
        $answers = Answer::where('user_token', $user_token)->orderBy('created_at')->get();

        $currentStreak = 0;
        if (!$answers->isEmpty()) {
            foreach ($answers as $answer) {
                if ($answer->correct) {
                    $currentStreak++;
                } else {
                    $currentStreak = 0; // Reset streak on wrong answer
                }
            }
        }

        return [
            'current_streak' => $currentStreak,
            'answers' => $answers->map(function ($answer) {
                return [
                    'question_id' => $answer->question_id,
                    'chosen_answer' => $answer->chosen_answer,
                    'correct_answer' => $answer->question->answer,
                    'question' => $answer->question->content,
                    'correct' => $answer->correct
                ];
            })
        ];
    }
}
