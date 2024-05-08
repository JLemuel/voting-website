<?php

namespace App\Livewire;

use App\Events\DisplaySummary;
use App\Models\Room;
use App\Models\Vote;
use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\Question;
use App\Events\SetQuestion;
use Livewire\Attributes\On;
use App\Models\RoomQuestion;
use App\Models\RoomParticipant;
use App\Events\CountdownStarted;

class QuestionDisplay extends Component
{
    use Toast;

    public $counter = 100; // Initial counter value
    public $sessionId;
    public $votedBy;
    public $otherParticipants;
    public $questionDetail;
    public $isChosenParticipant;
    public $hasQuestionDetail = false;
    public $startCountdown = false;
    public $countdown = 3;
    public $isSetTimer = false;
    public $timerSeconds;
    public $roomQuestionId;
    public $displaySummary;
    public $summary;
    public $summaryParticipants;
    public $totalParticipants;
    public $totalVotes;

    public function mount($session, $participant, $question)
    {
        $roomQuestion = RoomQuestion::where('question_id', $question)
            ->with('question')
            ->first();

        if ($roomQuestion) {
            $this->questionDetail = $roomQuestion->question;
        } else {
            $this->questionDetail = null;
        }

        $room = Room::findOrFail($session);
        if ($room->set_timer) {
            $this->isSetTimer = true;
            $this->timerSeconds = $room->timer_seconds;
        }

        $this->sessionId = $session;
        $this->votedBy = $participant;
        $this->roomQuestionId = $question;

        $this->otherParticipants = RoomParticipant::where('room_id', $session)
            ->where('id', '!=', $participant)
            ->get();

        foreach ($this->otherParticipants as $participant) {
            $participant->voted = false;
        }

        // dd($this->otherParticipants);
    }

    public function vote($participantId, $questionDetailId, $votedBy, $sessionId)
    {
        $voteData = [
            'room_id' => $sessionId,
            'question_id' => $questionDetailId,
            'participant_id' => $participantId,
            'voted_by' => $votedBy,
        ];

        $existingVote = Vote::where('question_id', $questionDetailId)
            ->where('voted_by', $this->votedBy)
            ->first();


        if ($existingVote) {
            $existingVote->update($voteData);
            $this->success('Vote Updated.', position: 'toast-top toast-center');
        } else {
            $previousVote = Vote::where('question_id', $questionDetailId)
                ->where('voted_by', $this->votedBy)
                ->first();

            if ($previousVote) {
                $previousVote->update(['participant_id' => $participantId]);
                $this->success('Vote Changed.', position: 'toast-top toast-center');
            } else {
                Vote::create($voteData);
                $this->success('You Voted', position: 'toast-top toast-center');
            }
        }

        $this->updateParticipantVoteStatus($participantId);
    }

    private function updateParticipantVoteStatus($participantId)
    {
        foreach ($this->otherParticipants as $key => $participant) {
            if ($participant->id == $participantId) {
                $this->otherParticipants[$key]->voted = true;
            }
        }
    }

    #[On('echo:random-participant,RandomQuestionWriterSelected')]
    public function randomParticipantSelected($randomParticipant)
    {
        $roomId = $randomParticipant['roomId'];
        $chosenParticipant = $randomParticipant['selectedParticipantId'];

        $this->info('a random participant is chosen to create a question ', position: 'toast-top toast-center');

        return $this->redirectRoute('create-question', [
            'session' => $this->sessionId,
            'participant' => $this->votedBy,
            'chosenParticipant' => $chosenParticipant // Pass chosenParticipant as a separate parameter
        ]);
    }

    #[On('updateDisplaySummary')]
    public function updateDisplaySummary()
    {
        $this->displaySummary = false;
    }

    #[On('echo:questions,SetQuestion')]
    public function handleQuestion($questionData)
    {
        // dd($questionData['questionId'], $questionData['roomId'], $this->votedBy);
        $roomId = $questionData['roomId'];
        $questionId = $questionData['questionId'];

        $this->displaySummary = null;

        return $this->redirectRoute('question-panel', ['session' => $roomId, 'participant' => $this->votedBy, 'question' => $questionId]);
    }

    #[On('echo:show-summary,DisplaySummary')]
    public function displaySummaryWithoutTimer()
    {
        $this->displaySummary = true;

        $this->summaryParticipants = RoomParticipant::where('room_id', $this->sessionId)->get();
        $this->totalParticipants = $this->summaryParticipants->count();
        // dd($this->totalParticipants);
        $this->totalVotes = Vote::where('room_id', $this->sessionId)
            ->where('question_id', $this->roomQuestionId)
            ->count();

        $this->calculateSummary();
        // return $this->redirectRoute('participant-summary-panel', ['session' => $this->sessionId, 'participant' => $this->votedBy, 'question' => $this->roomQuestionId]);
    }

    #[On('timer-ended')]
    public function timerEnded()
    {
        DisplaySummary::dispatch();

        $this->displaySummary = true;

        $this->summaryParticipants = RoomParticipant::where('room_id', $this->sessionId)->get();
        $this->totalParticipants = $this->summaryParticipants->count();
        // dd($this->totalParticipants);
        $this->totalVotes = Vote::where('room_id', $this->sessionId)
            ->where('question_id', $this->roomQuestionId)
            ->count();

        $this->calculateSummary();
        // return $this->redirectRoute('participant-summary-panel', ['session' => $this->sessionId, 'participant' => $this->votedBy, 'question' => $this->roomQuestionId]);
    }

    public function calculateSummary()
    {
        $votes = [];
        foreach ($this->summaryParticipants as $participant) {
            $votes[$participant->name] = Vote::where('room_id', $this->sessionId)
                ->where('participant_id', $participant->id)
                ->where('question_id', $this->roomQuestionId)
                ->count();
        }

        $total = array_sum($votes);
        $maxPercentage = 0;
        $this->summary = collect($votes)->map(function ($votes, $participantName) use ($total, &$maxPercentage) {
            $percentage = $total ? round(($votes / $total) * 100, 2) : 0;
            $maxPercentage = max($maxPercentage, $percentage);
            return [
                'participantName' => $participantName, // Replace $participant with $participantName
                'percentage' => $percentage,
            ];
        })->sortByDesc('percentage')->values()->toArray();

        foreach ($this->summary as &$participant) {
            if ($participant['percentage'] == $maxPercentage) {
                $participant['isHighest'] = true;
            }
        }
    }

    #[On('echo:show-overall,ShowOverall')]
    public function showOverAll()
    {
        return redirect()->route('overall-panel', ['session' => $this->sessionId]);
    }

    public function render()
    {
        // dd($this->isSetTimer);
        return view('livewire.question-display', [
            'participants' => $this->otherParticipants,
            'questionDetail' => $this->questionDetail,
            'votedBy' => $this->votedBy,
            'sessionId' => $this->sessionId,
            'chosenParticipant' => $this->isChosenParticipant,
            'hasQuestionDetail' => $this->hasQuestionDetail,
            'isSetTimer' => $this->isSetTimer,
            'timerSeconds' => $this->timerSeconds,
            'displaySummary' => $this->displaySummary,
        ]);
    }
}