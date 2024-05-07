<div>
    <p class="text-lg font-bold">You have been selected to write the next question.</p>
    <x-form wire:submit="createQuestion">
        <x-input label="Create a Question" wire:model="generateQuestion" />
       
        <x-slot:actions>
            <x-button label="Click me!" class="btn-primary" type="submit" spinner="save" />
        </x-slot:actions>
    </x-form>
</div>
