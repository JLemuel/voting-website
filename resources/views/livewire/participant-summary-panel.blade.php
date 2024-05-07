<div>
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
    <div class="mt-8 text-center">
        <x-progress class="my-2 progress-primary h-0.5" indeterminate />
        <p class="text-xl font-bold py-4 text-secondary">waiting for the host to move to next question...</p>
    </div>
</div>