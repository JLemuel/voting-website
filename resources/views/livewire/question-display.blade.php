<div>
    <div class="toast toast-top toast-center" id="toastElement" style="display: none;">
        <div class="alert alert-warning">
            <span>Time is Up!</span>
        </div>
    </div>
    <div class="flex flex-col justify-center">
        @if($displaySummary)
        <div class="flex flex-col items-center justify-center text-center">
            <p class="text-2xl font-bold py-4 text-info">Voting Results:</p>
            <p class="text-5xl font-black px-4 text-center">{{ preg_replace('/^\d+\.\s+/', '', $questionDetail->content
                ?? '')
                }}</p>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-8">
            @foreach($summary as $participantSummary)
            <div class="stats shadow bg-gray-100 rounded-md">
                <div class="stat place-items-center">
                    <p class="stat-title text-lg font-semibold">{{ $participantSummary['participantName'] }}</p>
                    <p
                        class="stat-value {{ isset($participantSummary['isHighest']) ? 'text-primary' : 'text-gray-700' }}">
                        {{ $participantSummary['percentage'] }}%
                    </p>
                </div>
            </div>
            @endforeach

        </div>
        <div class="mt-8 text-center">
            <x-progress class="my-2 progress-primary h-0.5" indeterminate />
            <p class="text-xl font-bold pt-4 text-secondary">waiting for the host to move to next question...</p>
        </div>
        @else
        @if($hasQuestionDetail)
        @if ($chosenParticipant)
        <div class="mt-6 flex flex-col justify-center text-center">
            <p class="text-4xl font-black my-6">You have been selected to write the next question.</p>
            <x-progress class="my-6 progress-primary h-0.5" indeterminate />
            <x-form wire:submit="createQuestion">
                <x-input label="Create a Question" wire:model="generateQuestion" />
                <x-slot name="actions">
                    <div class="mt-4 flex justify-center">
                        <x-button label="Create Question" class="btn-primary" type="submit" spinner="save" />
                    </div>
                </x-slot>
            </x-form>
        </div>
        @else
        <div class="flex flex-col items-center justify-center text-center">
            <p class="text-5xl font-black py-4 text-primary">We have selected a participant to write their next
                question.
            </p>
            <div class="mt-4 text-center">
            </div>
            <x-progress class="my-6 progress-primary h-0.5" indeterminate />
        </div>
        @endif
        @else
        <div class="flex flex-col items-center">
            <h2 class="text-xl font-semibold mb-4 text-info">Question:</h2>
            <p class="text-5xl font-black text-center">{{ preg_replace('/^\d+\.\s*/', '', $questionDetail->content ) }}
            </p>
            @if($isSetTimer)
            <span class="countdown font-mono text-4xl pt-10 text-secondary">
                <span id="counterElement" style="--value:{{ $timerSeconds }};"></span>
            </span>
            @endif
            <div class="mt-8">
                <ul class="grid grid-cols-1 gap-4">
                    @foreach ($participants as $participant)
                    <li>
                        <button id="yourButtonId_{{ $participant->id }}"
                            wire:click="vote({{ $participant->id }}, {{ $questionDetail->id }}, {{ $votedBy }}, {{ $sessionId }})"
                            class="btn btn-wide @if ($participant->voted == true) btn-active btn-warning @else btn-outline @endif ">
                            {{ $participant->name }}
                        </button>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
        @endif
    </div>
</div>



@if($isSetTimer)
@script
<script>
    window.onload = function() {
        let counter = {{ $timerSeconds }};
        const countdownInterval = setInterval(() => {
            if (counter > 0) {
                counter--;
                document.getElementById('counterElement').style.setProperty('--value', counter);
            } else {
                clearInterval(countdownInterval); 
                disableButtons(); 
                showAndHideToast();
            }
        }, 1000);
    };

    function disableButtons() {
        const buttons = document.querySelectorAll('[id^="yourButtonId_"]');
        buttons.forEach(button => {
            button.disabled = true; 
            button.classList.add('btn-disabled'); 
        });
    }

    function showAndHideToast() {
        const toastElement = document.getElementById('toastElement');
        toastElement.style.display = 'block'; 

        setTimeout(() => {
            toastElement.style.display = 'none';
            $wire.dispatchSelf('timer-ended');
        }, 3000);
    }
</script>
@endscript
@endif