<div wire:poll.keep-alive class="flex flex-col justify-center">
    @if ($randomParticipantSelected)
    <div class="flex flex-col items-center justify-center text-center">
        <p class="text-5xl font-black py-4 text-primary">We have selected a participant to write their next question.
        </p>
        <div class="mt-4 text-center">
        </div>
        <x-progress class="my-6 progress-primary h-0.5" indeterminate />
    </div>
    @else
    @if ($showResults)
    <div class="flex flex-col items-center justify-center text-center">
        <p class="text-2xl font-bold py-4 text-info">Voting Results:</p>
        <p class="text-5xl font-black px-4 text-center">{{ preg_replace('/^\d+\.\s+/', '', $question['content'] ?? '')
            }}</p>
    </div>
    <div class="grid grid-cols-2 gap-4 mt-8">
        @foreach($summary as $participant)
        <div class="stats shadow bg-gray-100 rounded-md">
            <div class="stat place-items-center">
                <p class="stat-title text-lg font-semibold">{{ $participant['participant'] }}</p>
                <p class="stat-value {{ isset($participant['isHighest']) ? 'text-primary' : 'text-gray-700' }}">{{
                    $participant['percentage'] }}%</p>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4 text-center">
        <button wire:click="nextQuestion('{{ $roomId }}')"
            class="mt-8 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Next Question</button>
    </div>
    @else
    <p class="text-5xl font-black md:px-0 px-4 text-center">{{ preg_replace('/^\d+\.\s+/', '', $question['content']) }}
    </p>
    <x-progress class="my-6 progress-primary h-0.5" indeterminate />
    <div class="text-center">
        <p class="text-2xl font-bold mb-2">Total Participants</p>
        <p class="text-lg font-semibold text-gray-700">{{ $totalParticipants }}</p>
        @php
        $progressPercentage = ($totalVotes / $totalParticipants) * 100;
        @endphp
        <x-progress value="{{ $progressPercentage }}" max="100" class="progress-secondary h-8 mt-6" />
        <p class="text-gray-700">{{ $totalVotes }} / {{ $totalParticipants }} Voted</p>
    </div>
    @endif
    @endif
</div>