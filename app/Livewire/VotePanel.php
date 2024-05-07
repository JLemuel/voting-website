<?php

namespace App\Livewire;

use App\Events\SetQuestion;
use App\Models\Room;
use App\Models\Question;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Mary\Traits\Toast;
use Livewire\Attributes\On;

class VotePanel extends Component
{
    use Toast;

    public $session;
    public $participant;
    public $code;
    public $room;
    public $question;

    public function mount($session, $participant, $code)
    {
        $this->session = $session;
        $this->participant = $participant;
        $this->code = $code;

        $this->room = Room::with('participants')->findOrFail($session);

        $this->success("You have joined the room.", position: 'toast-top toast-center');

        $this->checkParticipant();
    }

    private function checkParticipant()
    {

        $participantExists = $this->room->participants->contains('id', $this->participant);
        if (!$participantExists) {
            $this->warning("You have been removed from the room.", position: 'toast-top toast-center');
            sleep(2.1);
            return redirect()->route('home');
        }
    }

    #[On('echo:questions,SetQuestion')]
    public function handleQuestion($questionData)
    {
        $roomId = $questionData['roomId'];
        $questionId = $questionData['questionId'];

        return redirect()->route('question-panel', ['session' => $this->session, 'participant' => $this->participant, 'question' => $questionId]);
    }


    public function render()
    {
        $this->checkParticipant();

        return view('livewire.vote-panel', ['question' => $this->question]);
    }
}