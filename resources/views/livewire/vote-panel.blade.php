<!-- resources/views/livewire/vote-panel.blade.php -->

<div wire:poll.keep-alive class="flex flex-col items-center">
   
    <p class="text-lg text-gray-700">Session ID: <span class="font-semibold">{{ $room->room_name }}</span></p>
    <p class="text-lg text-gray-700">Participant ID: <span class="font-semibold">{{ $participant }}</span></p>
    <p class="text-lg text-gray-700">Code: <span class="font-semibold">{{ $room->code }}</span></p>

    <h2 class="mt-6 text-2xl font-semibold mb-4">Waiting for the session to start.</h2>

</div>
