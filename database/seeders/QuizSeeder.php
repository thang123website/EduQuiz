<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Models\QuizPart;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Categories
        $toeicCategory = QuizCategory::create([
            'name' => 'TOEIC',
            'slug' => 'toeic',
            'type' => 'academic',
        ]);

        $ieltsCategory = QuizCategory::create([
            'name' => 'IELTS Academic',
            'slug' => 'ielts-academic',
            'type' => 'academic',
        ]);

        // 2. Create Tags
        $tag2024 = Tag::create(['name' => '2024', 'slug' => '2024']);
        $tagETS = Tag::create(['name' => 'ETS', 'slug' => 'ets']);
        $tagNewEconomy = Tag::create(['name' => 'New Economy', 'slug' => 'new-economy']);

        // 3. Create a TOEIC Quiz
        $quiz = Quiz::create([
            'category_id' => $toeicCategory->id,
            'title' => 'New Economy TOEIC Test 1',
            'description' => 'A standard TOEIC mock test simulating the real exam.',
            'duration' => 120,
            'pass_mark' => 500, // Just a rough number
            'difficulty' => 'medium',
            'status' => 'published',
            'settings' => ['scoring_type' => 'toeic'],
            'is_new' => true,
        ]);

        // Attach Tags
        $quiz->tags()->attach([$tagNewEconomy->id, $tag2024->id]);

        // 4. Create Quiz Parts
        $part1 = QuizPart::create([
            'quiz_id' => $quiz->id,
            'title' => 'Part 1: Photographs',
            'description' => 'Look at the picture and listen to the statements. Choose the statement that best describes the picture.',
            'order_idx' => 1,
        ]);

        $part2 = QuizPart::create([
            'quiz_id' => $quiz->id,
            'title' => 'Part 2: Question-Response',
            'description' => 'Listen to a question or statement and three responses. Choose the best response.',
            'order_idx' => 2,
        ]);

        // 5. Create some Questions for Part 1
        $question1 = Question::create([
            'quiz_id' => $quiz->id,
            'part_id' => $part1->id,
            'type' => 'single_choice',
            'content' => 'What is the man doing?',
            'media_type' => 'image',
            'media_url' => 'https://via.placeholder.com/600x400.png?text=Man+Working',
            'grade' => 5, // Custom TOEIC weight mapping might be needed later, 5 is dummy
            'order_idx' => 1,
            'explanation' => 'The man is clearly typing on his laptop.',
        ]);

        Option::create(['question_id' => $question1->id, 'text' => 'A. He is drinking coffee.', 'is_correct' => false]);
        Option::create(['question_id' => $question1->id, 'text' => 'B. He is typing on a keyboard.', 'is_correct' => true]);
        Option::create(['question_id' => $question1->id, 'text' => 'C. He is reading a book.', 'is_correct' => false]);
        Option::create(['question_id' => $question1->id, 'text' => 'D. He is looking out the window.', 'is_correct' => false]);

        // Create some Questions for Part 2
        $question2 = Question::create([
            'quiz_id' => $quiz->id,
            'part_id' => $part2->id,
            'type' => 'single_choice',
            'content' => '(Audio plays) When does the train leave?',
            'media_type' => 'audio',
            'media_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3', // Dummy audio
            'grade' => 5,
            'order_idx' => 2,
        ]);

        Option::create(['question_id' => $question2->id, 'text' => 'A. From platform 4.', 'is_correct' => false]);
        Option::create(['question_id' => $question2->id, 'text' => 'B. In about 20 minutes.', 'is_correct' => true]);
        Option::create(['question_id' => $question2->id, 'text' => 'C. Yes, it is very fast.', 'is_correct' => false]);
    }
}
