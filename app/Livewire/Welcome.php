<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;
use Mary\Traits\Toast;
use App\Models\Room;
use App\Models\RoomParticipant;
use Illuminate\Support\Facades\Redirect;

class Welcome extends Component
{
    use Toast;

    public $name;
    public $code;

    protected $rules = [
        'name' => 'required|string',
        'code' => 'required|string',
    ];

    public function render()
    {
        return view('livewire.welcome');
    }

    public function joinRoom()
    {
        $this->validate();

        $room = Room::where('code', $this->code)->first();

        if ($room) {
            // Check if the room is active
            if ($room->active) {
                $participant = $room->participants()->create([
                    'name' => $this->name,
                ]);

                return $this->redirectRoute('vote-panel', [
                    'session' => $room->id,
                    'participant' => $participant->id,
                    'code' => $this->code
                ]);
            } else {
                // Room is not active, display a warning
                $this->warning("The room is not active. Please try joining another room.", position: 'toast-top toast-center');
            }
        } else {
            // Room not found, display a warning
            $this->warning("Room not found. Please check the room code and try again.", position: 'toast-top toast-center');
        }
    }
}