<div wire:poll.keep-alive class="flex flex-col justify-center">
    <div class="flex flex-col items-center justify-center text-center">
        <p class="text-5xl font-black py-4 text-center">Voting Results</p>
    </div>
    <hr />
    <div class="grid grid-cols-1 gap-4 mt-8">
        @foreach($mostVotedParticipants as $question => $participant)
        <div class="bg-base-100 shadow-md rounded-lg p-4 flex justify-between items-center">
            <p class="text-2xl text-primary font-black mb-2 w-9/12">{{ preg_replace('/^\d+\.\s+/', '', $question )}}
            </p>
            <p class="text-3xl text-secondary font-black px-4">{{ $participant }}</p>
        </div>
        @endforeach
    </div>

    <div class="mt-8 text-center">
        <button wire:click="endVoting" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            End Voting
        </button>
    </div>
</div>