{{-- 체크박스 선택여부 --}}
@if (in_array($item->id, $selected))
<div class="col-3 row-selected">
@else
<div>
@endif

    <div>
        <input type='checkbox' name='ids'
            value="{{ $item->id }}"
            class="form-check-input"
            wire:model.live="selected">
    </div>

    {{ $slot }}


</div>
