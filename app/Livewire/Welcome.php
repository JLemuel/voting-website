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
             $participant = $room->participants()->create([
                'name' => $this->name,
            ]);
            
            return $this->redirectRoute('vote-panel', [
                'session' => $room->id,
                'participant' => $participant->id,
                'code' => $this->code
            ]);
            
        } else {

            $this->warning("Room not found. Please check the room code and try again.", position: 'toast-top toast-center');
        }
    }
}
