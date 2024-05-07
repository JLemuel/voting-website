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

        // Count votes for each participant
        foreach ($this->roomVotes as $vote) {
            $participantId = $vote->participant_id;
            if (!isset($participantVotes[$participantId])) {
                $participantVotes[$participantId] = 0;
            }
            $participantVotes[$participantId]++;
        }

        $participantPercentages = [];

        // Calculate percentage of votes for each participant
        foreach ($participantVotes as $participantId => $votes) {
            $percentage = ($votes / $totalVotes) * 100;
            $participantPercentages[RoomParticipant::find($participantId)->name] = $percentage;
        }

        // Find the participant with the highest percentage of votes
        $highestParticipant = collect($participantPercentages)->sortDesc()->keys()->first();

        // Initialize an array to store the most voted participants for each question
        $mostVotedParticipants = [];

        // Get all the questions and their most voted participants
        foreach ($this->roomVotes as $vote) {
            $questionContent = $vote->question->content;
            $participantName = RoomParticipant::find($vote->participant_id)->name;
            if (!isset($mostVotedParticipants[$questionContent])) {
                $mostVotedParticipants[$questionContent] = $participantName;
            } else {
                // Update if the new participant has more votes than the current most voted participant
                if ($participantVotes[$vote->participant_id] > $participantVotes[RoomParticipant::where('name', $mostVotedParticipants[$questionContent])->first()->id]) {
                    $mostVotedParticipants[$questionContent] = $participantName;
                }
            }
        }

        // dd($mostVotedParticipants);

        return view('livewire.overall-panel', [
            'highestParticipant' => $highestParticipant,
            'participantPercentages' => $participantPercentages,
            'mostVotedParticipants' => $mostVotedParticipants
        ]);
    }
}
