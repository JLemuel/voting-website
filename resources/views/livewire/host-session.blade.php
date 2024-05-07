{{-- resources/views/livewire/host-session-form.blade.php --}}

<div>
    <div class="flex justify-center">
        <!-- HEADER -->
        <x-header title="Host a session">

        </x-header>

    </div>

    <x-form wire:submit.prevent="hostSession">
        <x-input label="Room Name" wire:model="roomName" />
        <x-checkbox label="Set Timer" wire:model="setTimer" />

        {{-- Select for Number of Questions --}}
        <x-select label="Number of Questions" :options="$numbers" wire:model="numQuestions" />

        {{-- Select for Question Level --}}
        <x-select label="Question Level" :options="$level" wire:model="questionLevel" />

        <div class="flex justify-center mt-6">
            <x-button label="Host" class="btn-primary w-32" type="submit" spinner="save" />
        </div>
    </x-form>
</div>