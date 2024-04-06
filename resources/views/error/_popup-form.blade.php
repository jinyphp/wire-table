<div>
    <!-- 팝업 데이터 수정창 -->
    @if ($popupForm)
    <x-table-dialog-modal wire:model="popupForm" maxWidth="2xl">
        <x-slot name="content">
            <br/>
            {{$mode}} 사용자 권한이 없습니다.
            <br/>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-between">
                <div></div>
                <div class="text-right">
                    <x-button secondary wire:click="popupFormClose">닫기</x-button>
                </div>
            </div>
        </x-slot>
    </x-table-dialog-modal>
    @endif
</div>
