@if(!$checkDelete)
    {{-- 선택한 항목 갯수 --}}
    <span id="selected-num">
        {{$selected_count}}
    </span>
    <span class="px-1">selected</span>

    {{-- 버튼 활성화 --}}
    @if ($selected_count)
        <button type="button" class="btn btn-danger btn-sm" id="selected-delete"
            wire:click="popupCheckDelete">선택삭제</button>
    @else
        <button type="button" class="btn btn-outline-secondary btn-sm" id="selected-delete"
            disabled>선택삭제</button>
    @endif

@else
    {{-- 삭제처리 --}}
    @if(!$checkDeleteConfirm)
        <span>
            선택한 항목을 삭제하시겠습니까?
        </span>
        <button type="button" class="btn btn-secondary"
            wire:click="popupCheckDeleteClose">
            취소
        </button>

        <button type="button" class="btn btn-danger"
            wire:click="checkeDeleteConfirm">
            삭제
        </button>

    @else
        <span class="text-red-700">
            정말로 삭제할까요?
        </span>

        <button type="button" class="btn btn-secondary"
            wire:click="popupCheckDeleteClose">
            취소
        </button>
        <button type="button" class="btn btn-danger"
            wire:click="checkeDeleteRun">
            예
        </button>
    @endif
@endif
