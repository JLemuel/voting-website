<div>
    <div class="flex justify-center">
        <!-- HEADER -->
        <x-header title="Join a session">

        </x-header>

    </div>

    <x-form wire:submit.prevent="joinRoom">
        <x-input label="Your Name" wire:model="name" />
        <x-input label="Code" wire:model="code" />

        <div class="flex justify-center">
            <x-button label="Join" class="btn-primary w-32" type="submit" spinner="save" />
        </div>
    </x-form>
    <div class="mt-6">
        <hr />
    </div>
    <div class="mt-6 flex justify-center">
        <a href="/host-session" wire:navigate class="text-primary hover:underline">Host a Session</a>
    </div>

</div>