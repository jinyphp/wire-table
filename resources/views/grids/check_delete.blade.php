<span id="selected-num">{{$selected_count}}</span>
<span class="px-1">selected</span>

@if ($selected_count)
    <button type="button" class="btn btn-danger btn-sm" id="selected-delete"
        wire:click="popupCheckDelete">선택삭제</button>
@else
    <button type="button" class="btn btn-outline-secondary btn-sm" id="selected-delete"
        disabled>선택삭제</button>
@endif

{{-- 선택삭제 팝업 확인창 --}}
@if(isset($actions['delete']['check']) && $actions['delete']['check'])
    @if ($checkDelete)
    <x-table-dialog-modal wire:model="checkDelete" maxWidth="3xl">
        <x-slot name="title">
            {{ __('선택삭제') }}
        </x-slot>

        <x-slot name="content">
            <p class="p-8">선택한 항목을 삭제하시겠습니까?</p>
        </x-slot>

        <x-slot name="footer">
            <x-flex-between>
                <div>
                    <button type="button" class="btn btn-secondary"
                        wire:click="popupCheckDeleteClose">
                        취소
                    </button>
                </div>
                <div>
                    @if($checkDeleteConfirm)
                    정말로 삭제할까요?
                    <button type="button" class="btn btn-danger"
                        wire:click="checkeDeleteRun">
                        예
                    </button>
                    @else
                    <button type="button" class="btn btn-danger"
                        wire:click="checkeDeleteConfirm">
                        삭제
                    </button>
                    @endif
                </div>
            </x-flex-between>
        </x-slot>
    </x-table-dialog-modal>
    @endif
@endif
