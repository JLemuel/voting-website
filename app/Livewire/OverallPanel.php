<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Vote;
use App\Models\RoomParticipant;

class OverallPanel extends Component
{
    public $roomVotes;

    public function mount($session)
    {

        $this->roomVotes = Vote::where('room_id', $session)->get();
    }

    public function endVoting()
    {

        return redirect()->route('home');
    }


    public function render()
    {

        $totalVotes = $this->roomVotes->count();

        $participantVotes = [];

        foreach ($this->roomVotes as $vote) {
            $participantId = $vote->participant_id;
            if (!isset($participantVotes[$participantId])) {
                $participantVotes[$participantId] = 0;
            }
            $participantVotes[$participantId]++;
        }

        $participantPercentages = [];
        foreach ($participantVotes as $participantId => $votes) {
            $percentage = ($votes / $totalVotes) * 100;
            $participantPercentages[RoomParticipant::find($participantId)->name] = $percentage;
        }

        arsort($participantPercentages);

        return view('livewire.overall-panel', [
            'participantPercentages' => $participantPercentages
        ]);
    }

}
