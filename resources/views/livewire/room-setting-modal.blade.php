<x-modal id="setting" title="Vote Game Setting">
    <x-form wire:submit="saveSetting">
        <x-input label="Timer" placeholder="Set Seconds" icon="o-clock" hint="Set How many seconds"
            wire:model="timerSecs" />
        <x-checkbox label="Enable Picking Random Participant" wire:model="isRandom"
            hint="Enabled by default, for picking random participant for questions" left />
        <x-slot:actions>

            <x-button label="Save" class="btn-primary" type="submit" spinner="saveSetting" />
        </x-slot:actions>
    </x-form>
</x-modal>