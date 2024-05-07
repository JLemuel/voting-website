<div>
    <div class="toast toast-top toast-center" id="toastElement" style="display: none;">
        <div class="alert alert-warning">
            <span>Time is Up!</span>
        </div>
    </div>
    <div class="flex flex-col justify-center">
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
            <h2 class="text-xl font-semibold mb-4">Question:</h2>
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