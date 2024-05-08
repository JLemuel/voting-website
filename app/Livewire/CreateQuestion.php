<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Question;
use App\Models\RoomQuestion;
use App\Events\CountdownStarted;
use Mary\Traits\Toast;
use Livewire\Attributes\On;

class CreateQuestion extends Component
{
    use Toast;
    public $sessionId;
    public $votedBy;
    public $isChosenParticipant;
    public $generateQuestion;

    public function mount($session, $participant, $chosenParticipant)
    {

        $this->sessionId = $session;
        $this->votedBy = $participant;

        $this->isChosenParticipant = ($chosenParticipant == $this->votedBy);

        // dd($this->isChosenParticipant);
    }

    public function createQuestion()
    {
        $this->validate([
            'generateQuestion' => 'required|string|max:255',
        ]);

        $question = Question::create([
            'room_id' => $this->sessionId,
            'content' => $this->generateQuestion,
        ]);
        $this->generateQuestion = '';

        $this->success('You have created the question.', position: 'toast-top toast-center');

        // $this->startCountdown = true;
        // $this->dispatch('post-created');


        // Update the room_question table with the latest question
        RoomQuestion::updateOrCreate(
            ['room_id' => $this->sessionId],
            ['question_id' => $question->id]
        );
        // CountdownStarted::dispatch();
        event(new CountdownStarted($this->sessionId, $question->id));
    }

    #[On('echo:count-start,CountdownStarted')]
    public function showCountdown($event)
    {
        $roomId = $event['roomId'];
        $questionId = $event['questionId'];

        $route = route('question-panel', ['session' => $roomId, 'participant' => $this->votedBy, 'question' => $questionId]);

        // dd($roomId, $questionId, $route);

        $this->dispatch('post-created', ['route' => $route]);
    }


    public function render()
    {
        return view('livewire.create-question', [
            'chosenParticipant' => $this->isChosenParticipant,
        ]);
    }
}