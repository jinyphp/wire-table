<div>
    @includeIf($actions['view']['form'])

    <div class="flex justify-between">
        <div></div>
        <div class="text-right">
            <button type="button" class="btn btn-secondary"
                wire:click="cancel">취소</button>
            <button type="button" class="btn btn-primary"
                wire:click="store">저장</button>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('redirect-back', function () {
                console.log("back");
                window.history.back();
            });
        });
    </script>

</div>


