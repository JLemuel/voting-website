<div wire:poll.keep-alive class="flex flex-col justify-center">
    <div class="flex flex-col items-center justify-center text-center">
  
        <p class="text-5xl font-black py-4 text-center">Voting Results</p>
    </div>
    <hr />
  
    <div class="grid grid-cols-1 gap-4 mt-8">
        @php
    
            $highestParticipant = collect($participantPercentages)->sortDesc()->keys()->first();
        @endphp

        @foreach($participantPercentages as $participantId => $percentage)
            <div class="bg-white shadow-md rounded-lg p-4">
                <p class="text-3xl font-bold mb-2">{{ $participantId }}</p>
                <div class="flex items-center">
                    <x-progress :value="round($percentage)" :max="100" class="h-6 {{ $participantId == $highestParticipant ? 'progress-secondary' : 'progress-info' }}" />
                    {{-- <p class="text-lg ml-4">{{ $percentage }}%</p> --}}
                    <p class="pl-8 stat-value {{ $highestParticipant ? 'text-primary' : 'text-gray-700' }}">{{
                        round($percentage) }}%</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 text-center">
        <button wire:click="endVoting" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            End Voting
        </button>
    </div>
</div>
