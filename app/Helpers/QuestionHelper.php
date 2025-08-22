<?php
namespace App\Helpers;

class QuestionHelper
{
    public static function generateWrongs(string $correct, int $count = 3): array
    {
        $correctNum = (int)$correct;
        $length = strlen($correct);
        $wrongs = [];
        
        // Step size based on number length
        $step = pow(10, max($length - 2, 0));
        
        // Generate random offsets to ensure variety
        $usedOffsets = [];
        
        for ($i = 0; $i < $count; $i++) {
            do {
                // Generate random offset between -5 and +5, excluding 0
                $offset = rand(-5, 5);
                if ($offset == 0) $offset = rand(1, 2) * (rand(0, 1) ? 1 : -1);
                
                $wrongNum = $correctNum + $offset * $step;
                
                // Ensure positive number and not a duplicate
            } while ($wrongNum <= 0 || 
                     $wrongNum == $correctNum || 
                     in_array($offset, $usedOffsets));
            
            $usedOffsets[] = $offset;
            $wrongs[] = (string)$wrongNum;
        }
        
        return $wrongs;
    }
}