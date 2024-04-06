{{-- 선택삭제 팝업 확인창 --}}
@if ($popupDelete)
    <x-table-dialog-modal wire:model="popupDelete" maxWidth="xl">
        <x-slot name="title">
            {{ __('선택삭제') }}
        </x-slot>

        <x-slot name="content">
            <p class="p-8">정말로 삭제할까요?</p>
            {{--
            @foreach ($selected as $item)
                {{$item}}
            @endforeach
            --}}
        </x-slot>

        <x-slot name="footer">
            <button type="button" class="btn btn-secondary" wire:click="popupDeleteClose">취소</button>
            <button type="button" class="btn btn-danger" wire:click="checkeDelete">삭제</button>
        </x-slot>
    </x-table-dialog-modal>
@endif
