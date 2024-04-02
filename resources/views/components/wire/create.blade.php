{{-- 자바스크립트를 이용하여 livewire emit 호출 --}}
<x-btn-primary id="btn-livepopup-create">
    {{$slot}}
</x-btn-primary>

@push('scripts')
<script>
    document.querySelector("#btn-livepopup-create")
    .addEventListener("click",function(e){
        e.preventDefault();
        console.log("livewire emit to create method");
        Livewire.emit('create');
    });
</script>
@endpush
