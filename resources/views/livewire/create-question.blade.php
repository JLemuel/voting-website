<div>
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
</div>