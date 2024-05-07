<div wire:poll.keep-alive class="flex justify-center items-center w-full">
    @if ($rooms)
    <div class="flex flex-col justify-center m-auto items-center text-center">
        <!-- HEADER -->
        <x-header :title="$rooms->room_name" />

        <p class="text-gray-600 mc- -mt-8">ID: <span class="font-semibold">{{ $rooms->code }}</span></p>

        <div class="mt-8 text-center">
            @if ($rooms->participants->isEmpty())
            <p>No participants have joined yet.</p>
            @else
            <div class="grid grid-cols-4 gap-4">
                @foreach ($rooms->participants as $participant)
                <div class="flex items-center">
                    <span>{{ $participant->name }}</span>
                    <button wire:click="removeParticipant('{{ $rooms->id }}', '{{ $participant->id }}')"
                        class="text-red-500 hover:text-red-700 -ml-1 -mt-2">X</button>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Add "Start Now" button -->
        @if ($rooms->participants->count() == 3)
        <div class="mt-4">
            <button wire:click="startNow('{{ $rooms->id }}')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Start Now
            </button>
        </div>
        @endif

    </div>

    @else
    <div class="text-center text-gray-400">No rooms available.</div>
    @endif
</div>