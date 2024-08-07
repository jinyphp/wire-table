<x-www-app>
    <div class="container">
        <div class="alert alert-danger">
            {{$message}}
        </div>

        @livewire('setActionRule', [
                'actions'=>$actions
        ])

        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('refeshTable', (event) => {
                    console.log("refeshTable");
                    window.location.reload();
                });
            });
        </script>
    </div>

</x-www-app>
