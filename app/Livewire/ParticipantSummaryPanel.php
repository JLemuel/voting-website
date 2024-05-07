<?php

namespace App\Livewire;

use App\Models\Vote;
use Livewire\Component;
use App\Models\RoomQuestion;
use App\Models\RoomParticipant;
use Livewire\Attributes\On;

class ParticipantSummaryPanel extends Component
{

    public $roomId;
    public $questionId;
    public $participantId;
    public $participants;
    public $totalParticipants;
    public $totalVotes;
    public $showResults = false;
    public $timeIsUp = false;
    public $summary;

    public function mount($session, $participant, $question)
    {
        $this->roomId = $session;
        $this->questionId = $question;
        $this->participantId = $participant;
    }

    #[On('echo:questions,SetQuestion')]
    public function handleQuestion($questionData)
    {
        // dd($questionData['questionId'], $questionData['roomId'], $this->votedBy);
        $roomId = $questionData['roomId'];
        $questionId = $questionData['questionId'];

        return $this->redirectRoute('question-panel', ['session' => $roomId, 'participant' => $this->participantId, 'question' => $questionId]);
    }

    public function loadData()
    {
        $this->participants = RoomParticipant::where('room_id', $this->roomId)->get();
        $this->totalParticipants = $this->participants->count();
        // dd($this->totalParticipants);
        $this->totalVotes = Vote::where('room_id', $this->roomId)
            ->where('question_id', $this->questionId)
            ->count();

        $this->calculateSummary();
    }

    public function calculateSummary()
    {
        $votes = [];
        foreach ($this->participants as $participant) {
            $votes[$participant->name] = Vote::where('room_id', $this->roomId)
                ->where('participant_id', $participant->id)
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

    public function render()
    {
        $this->loadData();

        $roomId = $this->roomId;

        $questionId = RoomQuestion::where('room_id', $roomId)
            ->where('question_id', $this->questionId)
            ->first();

        $question = optional($questionId)->question;

        return view('livewire.participant-summary-panel', [
            'totalParticipants' => $this->totalParticipants,
            'totalVotes' => $this->totalVotes,
            'showResults' => $this->showResults,
            'timeIsUp' => $this->timeIsUp,
            'summary' => $this->summary,
            'question' => $question,

        ]);
    }
}
