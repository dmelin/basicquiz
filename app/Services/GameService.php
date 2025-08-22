<?php

namespace App\Services;

use App\Models\Answer;

class GameService
{
    public function getStatus(string $token): array
    {
        $answers = Answer::where('user_token', $token)->latest()->get();

        $streak = $answers->takeWhile(fn($a) => $a->correct)->count();

        $gameOver = $answers->last()?->correct === false || $streak >= 10;

        return [
            'streak' => $streak,
            'game_over' => $gameOver,
            'won' => $streak >= 10,
        ];
    }
}
