<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;
use Illuminate\Support\Facades\Route;
use Mary\Traits\Toast;

class RoomSettingModal extends Component
{
    use Toast;
    public $timerSecs;
    public $isRandom;

    public $roomId;

    public function mount()
    {
        $this->roomId = Route::current()->parameter('session');
        // dd($this->roomId);
    }

    public function render()
    {
        return view('livewire.room-setting-modal');
    }

    public function saveSetting()
    {
        $room = Room::find($this->roomId); // Replace 1 with the ID of the room you want to update

        $room->set_timer = true; // Assuming you want to enable the timer
        $room->timer_seconds = $this->timerSecs;
        $room->pick_random_participant = $this->isRandom;
        $room->save();

        $this->success('Room Setting Saved', position: 'toast-top toast-center');
    }
}
