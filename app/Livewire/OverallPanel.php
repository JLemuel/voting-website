<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\Vote;
use Livewire\Component;
use App\Models\RoomParticipant;

class OverallPanel extends Component
{
    public $roomVotes;
    public $roomId;

    public function mount($session)
    {
        $this->roomId = $session;

        $this->roomVotes = Vote::where('room_id', $this->roomId)->get();
    }

    public function endVoting()
    {

        $room = Room::findOrFail($this->roomId);
        $room->active = false;
        $room->save();

        return redirect()->route('home');
    }

    public function render()
    {

        $this->roomVotes->load('question', 'participant');

        $totalVotes = $this->roomVotes->count();

        $participantVotes = [];
        $mostVotedParticipants = [];

        foreach ($this->roomVotes as $vote) {
            $participantId = $vote->participant_id;
            $questionContent = $vote->question->content;
            $participantName = $vote->participant->name;

            if (!isset($participantVotes[$participantId])) {
                $participantVotes[$participantId] = 0;
            }
            $participantVotes[$participantId]++;

            if (
                !isset($mostVotedParticipants[$questionContent]) ||
                $participantVotes[$participantId] > $participantVotes[$mostVotedParticipants[$questionContent]['participant_id']]
            ) {
                $mostVotedParticipants[$questionContent] = [
                    'participant_id' => $participantId,
                    'participant_name' => $participantName
                ];
            }
        }

        $participantPercentages = [];

        foreach ($participantVotes as $participantId => $votes) {
            $percentage = ($votes / $totalVotes) * 100;
            $participantName = RoomParticipant::find($participantId)->name;
            $participantPercentages[$participantName] = $percentage;
        }

        $highestParticipant = collect($participantPercentages)->sortDesc()->keys()->first();

        $mostVotedParticipants = array_map(function ($item) {
            return $item['participant_name'];
        }, $mostVotedParticipants);

        return view('livewire.overall-panel', [
            'highestParticipant' => $highestParticipant,
            'participantPercentages' => $participantPercentages,
            'mostVotedParticipants' => $mostVotedParticipants
        ]);
    }
}