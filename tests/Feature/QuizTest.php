<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_it_can_fetch_next_question_with_game_results()
    {
        $token = 'ABCDE';

        // Skapa en dummy-fråga i databasen
        $question = Question::factory()->create([
            'answer' => '42',
            'wrongs' => ['41', '43', '40'],
        ]);

        // Anropa endpointen
        $response = $this->getJson("/api/next-question/{$token}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'question' => ['id', 'content', 'options'],
                'results' => ['current_streak', 'answers'],
            ]);

        // Kontrollera att rätt svar finns bland alternativen
        $this->assertContains('42', $response->json('question.options'));
    }

    public function test_it_can_create_token()
    {
        $response = $this->getJson('/api/generate-token');

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);

        $this->assertNotEmpty($response->json('token'));
    }

    public function test_it_can_check_correct_answer()
    {
        $token = 'ABCDE';
        $question = Question::factory()->create([
            'answer' => '42',
            'wrongs' => ['41', '43', '40'],
        ]);

        $question_id = $question->id;

        // Anropa endpointen med rätt svar
        $response = $this->getJson("/api/check-answer/{$token}?answer=42&question_id={$question_id}");

        $response->assertStatus(200)
            ->assertJson(['correct' => true]);
    }

    public function test_it_can_check_wrong_answer()
    {
        $token = 'ABCDE';
        $question = Question::factory()->create([
            'answer' => '42',
            'wrongs' => ['41', '43', '40'],
        ]);

        $question_id = $question->id;

        // Anropa endpointen med fel svar
        $response = $this->getJson("/api/check-answer/{$token}?answer=41&question_id={$question_id}");

        $response->assertStatus(200)
            ->assertJson(['correct' => false]);
    }

    public function test_streak_count() {
        $token = 'ABCDE';

        $correctStreak = 0;

        $answersGiven = "";

        for ($i = 0; $i < 20; $i++) {
            $question = Question::factory()->create([
                'answer' => $i,
                'wrongs' => [$i - 1, $i + 1, $i + 2],
            ]);

            $question_id = $question->id;

            if (rand(0, 4)) {
                $response = $this->getJson("/api/check-answer/{$token}?answer={$i}&question_id={$question_id}");
                $correctStreak++;
                $answersGiven .= "T";
            } else {
                $response = $this->getJson("/api/check-answer/{$token}?answer=" . ($i + 1) . "&question_id={$question_id}");
                $correctStreak = 0; // Reset streak on wrong answer
                $answersGiven .= "F";
            }
        }

        $response = $this->getJson("/api/next-question/{$token}");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'results' => ['current_streak', 'answers'],
            ]);
        
        $currentStreak = $response->json('results.current_streak');

        $this->assertEquals($currentStreak, $correctStreak, "Current streak {$currentStreak} should match the number of correct answers {$correctStreak} in a row. {$answersGiven}");
    }
}
