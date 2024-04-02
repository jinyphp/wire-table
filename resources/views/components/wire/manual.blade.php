<x-btn-primary id="btn-livepopup-manual">
    {{$slot}}
</x-btn-primary>

@push('scripts')
<script>
    document.querySelector("#btn-livepopup-manual")
    .addEventListener("click",function(e){
        e.preventDefault();
        console.log("livewire emit to manual method");
        Livewire.emit('manual');
    });
</script>
@endpush
