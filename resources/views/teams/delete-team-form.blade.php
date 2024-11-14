<x-action-section>
    <x-slot name="title">
        {{ __('Deletar Equipe') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Exclua permanentemente esta equipe.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('Depois que uma equipe for excluída, todos os seus recursos e dados serão excluídos permanentemente. Antes de excluir esta equipe, baixe quaisquer dados ou informações sobre ela que você deseja manter.') }}
        </div>

        <div class="mt-5">
            <x-danger-button wire:click="$toggle('confirmingTeamDeletion')" wire:loading.attr="disabled">
                {{ __('Deletar Equipe') }}
            </x-danger-button>
        </div>

        <!-- Delete Team Confirmation Modal -->
        <x-confirmation-modal wire:model.live="confirmingTeamDeletion">
            <x-slot name="title">
                {{ __('Deletar Equipe') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Tem certeza de que deseja excluir esta equipe? Depois que uma equipe for excluída, todos os seus recursos e dados serão excluídos permanentemente.') }}
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingTeamDeletion')" wire:loading.attr="disabled">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button class="ms-3" wire:click="deleteTeam" wire:loading.attr="disabled">
                    {{ __('Deletar Equipe') }}
                </x-danger-button>
            </x-slot>
        </x-confirmation-modal>
    </x-slot>
</x-action-section>
