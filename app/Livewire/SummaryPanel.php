<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\Vote;
use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\Question;
use App\Events\SetQuestion;
use Livewire\Attributes\On;
use App\Models\RoomQuestion;
use App\Events\DisplaySummary;
use App\Models\RoomParticipant;
use App\Events\RandomQuestionWriterSelected;
use App\Events\ShowOverall;

class SummaryPanel extends Component
{
    use Toast;

    public $roomId;
    public $questionId;
    public $participants;
    public $totalParticipants;
    public $totalVotes;
    public $showResults;
    public $summary;
    public $randomParticipantSelected = false;
    public $isSetTimer = false;
    public $questionTimer;

    public $timeIsUp;

    public function mount($session, $question)
    {
        $this->roomId = $session;
        $this->questionId = $question;
    }

    public function loadData()
    {
        $this->participants = RoomParticipant::where('room_id', $this->roomId)->get();
        $this->totalParticipants = $this->participants->count();
        // dd($this->totalParticipants);
        $this->totalVotes = Vote::where('room_id', $this->roomId)
            ->where('question_id', $this->questionId)
            ->count();

        // $this->isSetTimer();

        // dd($this->isSetTimer());

        $this->showResults = $this->totalParticipants === $this->totalVotes;
        // dd($this->showResults);

        if ($this->showResults) {
            $this->calculateSummary();
            DisplaySummary::dispatch();
        }

        if ($this->timeIsUp) {
            $this->calculateSummary();
        }
    }

    #[On('echo:show-summary,DisplaySummary')]
    public function isSetTimer()
    {
        $this->timeIsUp = true;
    }

    public function calculateSummary()
    {
        $votes = [];
        foreach ($this->participants as $participant) {
            $votes[$participant->name] = Vote::where('room_id', $this->roomId)
                ->where('participant_id', $participant->id)
                ->where('question_id', $this->questionId)
                ->count();
        }

        $total = array_sum($votes);
        $maxPercentage = 0;
        $this->summary = collect($votes)->map(function ($votes, $participant) use ($total, &$maxPercentage) {
            $percentage = $total ? round(($votes / $total) * 100, 2) : 0;
            $maxPercentage = max($maxPercentage, $percentage);
            return [
                'participant' => $participant,
                'percentage' => $percentage,
            ];
        })->sortByDesc('percentage')->values()->toArray();

        foreach ($this->summary as &$participant) {
            if ($participant['percentage'] == $maxPercentage) {
                $participant['isHighest'] = true;
            }
        }
    }

    public function nextQuestion($roomId)
    {
        $distinctQuestionCount = Vote::where('room_id', $roomId)->distinct('question_id')->count();

        if ($distinctQuestionCount % 5 == 0) {
            $this->selectRandomQuestionWriter($roomId);
            $this->randomParticipantSelected = true;

            // RoomQuestion::where('room_id', $roomId)->delete();

        } else {
            $questionId = $this->getRandomQuestionId();

            $distinctQuestionIds = Question::where('room_id', $roomId)->distinct()->pluck('id');
            $allQuestionsVoted = $distinctQuestionIds->every(function ($questionId) {
                return Vote::where('question_id', $questionId)->exists();
            });

            if ($allQuestionsVoted) {
                ShowOverall::dispatch();

                return redirect()->route('overall-panel', ['session' => $roomId]);
            }

            $questionId = $this->getUniqueQuestionId($roomId);

            try {
                $roomQuestion = RoomQuestion::updateOrCreate(
                    ['room_id' => $roomId],
                    ['question_id' => $questionId]
                );

                event(new SetQuestion($roomId, $questionId));

                $this->success('Question added to room.', position: 'toast-top toast-center');

                return redirect()->route('summary-panel', ['session' => $roomId, 'question' => $questionId]);
            } catch (\Exception $e) {
                $this->error('Failed to add question to room.');
                return back();
            }
        }
    }

    private function getUniqueQuestionId($roomId)
    {
        $questionId = $this->getRandomQuestionId();
        $questionAlreadyVoted = Vote::where('question_id', $questionId)->exists();

        while ($questionAlreadyVoted) {
            $questionId = $this->getRandomQuestionId();
            $questionAlreadyVoted = Vote::where('question_id', $questionId)->exists();
        }

        return $questionId;
    }

    private function selectRandomQuestionWriter($roomId)
    {
        $participants = RoomParticipant::where('room_id', $roomId)->pluck('id')->toArray();
        $selectedParticipant = $participants[array_rand($participants)];
        // dd($roomId, $selectedParticipant);

        event(new RandomQuestionWriterSelected($roomId, $selectedParticipant));

        $this->success('random participant' . ' ' . $selectedParticipant, position: 'toast-top toast-center');
    }

    private function getRandomQuestionId()
    {

        $randomQuestion = Question::inRandomOrder()->first();
        return $randomQuestion->id;
    }

    #[On('echo:count-start,CountdownStarted')]
    public function showCountdown($event)
    {
        $roomId = $event['roomId'];
        $questionId = $event['questionId'];

        $route = route('summary-panel', ['session' => $roomId, 'question' => $questionId]);

        // dd($roomId, $questionId, $route);

        $this->dispatch('post-created', ['route' => $route]);
    }

    public function render()
    {
        $this->loadData();

        $roomId = $this->roomId;

        $questionId = RoomQuestion::where('room_id', $roomId)
            ->where('question_id', $this->questionId)
            ->first();

        $question = optional($questionId)->question;

        // dd($question);

        return view('livewire.summary-panel', [
            'totalParticipants' => $this->totalParticipants,
            'totalVotes' => $this->totalVotes,
            'showResults' => $this->showResults,
            'timeIsUp' => $this->timeIsUp,
            'summary' => $this->summary,
            'question' => $question,
            'roomId' => $roomId,
            'isSetTimer' => $this->isSetTimer,
            'questionTimer' => $this->questionTimer,
        ]);
    }
}