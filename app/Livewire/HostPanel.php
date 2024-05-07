<?php

namespace App\Livewire;

use App\Events\SetQuestion;
use Livewire\Attributes\On; 
use App\Models\Room;
use App\Models\Question;
use App\Models\RoomQuestion;
use App\Models\Vote;
use Livewire\Component;
use Mary\Traits\Toast;

class HostPanel extends Component
{
    use Toast;

    public $rooms;
    public $roomId;
    public string $message;

    public function mount($session)
    {
    
        $this->rooms = Room::with('participants')->where('id', $session)->first();

        if (!$this->rooms) {
   
            $this->message = "Room not found with ID: $session";
        }
    }

    public function render()
    {

        $this->readTextFile();
        return view('livewire.host-panel', ['rooms' => $this->rooms]);
    }

    public function readTextFile()
    {
   
        if ($this->rooms) {
  
            $existingQuestionsCount = Question::where('room_id', $this->rooms->id)->count();
          
            if ($existingQuestionsCount > 0) {
                return;
            }
    
            $content = ($this->rooms->question_level == 1) ? file_get_contents(public_path('basic.txt')) : file_get_contents(public_path('extreme.txt'));
    
            $questions = explode("\n", $content);
            $numQuestions = min($this->rooms->num_questions, count($questions));
            $selectedQuestions = $this->selectRandomQuestions($questions, $numQuestions);
           
            foreach ($selectedQuestions as $questionContent) {
                $question = new Question();
                $question->room_id = $this->rooms->id;
                $question->content = $questionContent;
                $question->save();
            }
        }
    }    

    private function selectRandomQuestions($questions, $numQuestions)
    {
        shuffle($questions);
        return array_slice($questions, 0, $numQuestions);
    }

    public function removeParticipant($roomId, $participantId)
    {
 
        $room = Room::findOrFail($roomId);

        $participant = $room->participants()->findOrFail($participantId);
 
         $participant->delete();

         $this->info("Participant {$participant->name} has been removed from the room.", position: 'toast-top toast-center');
    }

    public function startNow($roomId)
    {
        try {

            $questionId = $this->getRandomQuestionId();
            
            $questionAlreadyVoted = Vote::where('question_id', $questionId)->exists();
            
            while ($questionAlreadyVoted) {
                $questionId = $this->getRandomQuestionId();
                $questionAlreadyVoted = Vote::where('question_id', $questionId)->exists();
            }

            $roomQuestion = RoomQuestion::updateOrCreate([
                'room_id' => $roomId,
                'question_id' => $questionId
            ]);


            event(new SetQuestion($roomId, $questionId));

            $this->success('Question added to room.', position: 'toast-top toast-center');

            return redirect()->route('summary-panel', ['session' => $roomId, 'question' => $questionId]);
        } catch (\Exception $e) {
            $this->error('Failed to add question to room: ' . $e->getMessage());
            return back();
        }
    }


    private function getRandomQuestionId()
    {

        $randomQuestion = Question::inRandomOrder()->first();
        return $randomQuestion->id;
    }
  
}
