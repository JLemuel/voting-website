<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;
use Illuminate\Support\Str;

class HostSession extends Component
{
    public $code;
    public $roomName;
    public $setTimer = false;
    public $numQuestions = '10';
    public $questionLevel = '1';
    public $active = true;

    protected $rules = [
        'roomName' => 'required|string',
        'setTimer' => 'boolean',
        'numQuestions' => 'required|integer|min:1',
        'questionLevel' => 'required|integer|min:1',
    ];

    public function render()
    {

        $numbers = collect(['10', '25', '50'])->map(function ($item) {
            return ['id' => $item, 'name' => $item];
        });

        $level = collect(['1', '2'])->map(function ($item) {
            return ['id' => $item, 'name' => 'Level ' . $item];
        });

        return view('livewire.host-session', compact('numbers', 'level'));
    }

    public function hostSession()
    {
        $this->validate();

        $this->code = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

        $room = Room::create([
            'code' => $this->code,
            'room_name' => $this->roomName,
            'set_timer' => $this->setTimer,
            'num_questions' => $this->numQuestions,
            'question_level' => $this->questionLevel,
            'active' => $this->active,
        ]);

        $room_id = $room->id;

        return redirect()->route('host-panel', ['session' => $room_id]);
    }
}
