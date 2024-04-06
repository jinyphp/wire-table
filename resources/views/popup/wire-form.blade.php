<div>
    <x-loading-indicator/>

    <!-- 팝업 데이터 수정창 -->
    @if ($popupForm)
    <x-wire-dialog-modal wire:model="popupForm" maxWidth="3xl">

        <x-slot name="title">
            @if (isset($actions['id']))
                {{ __('자료 수정') }}
            @else
                {{ __('신규 입력') }}
            @endif
        </x-slot>

        <x-slot name="content">
            @includeIf($actions['view']['form'])
        </x-slot>


        <x-slot name="footer">
            @if($message)
            <div class="alert alert-danger" role="alert">
                {{$message}}
            </div>
            @endif


            @if (isset($actions['id']))
            {{-- 수정폼--}}
            <div class="flex justify-between">
                <div> {{-- 2단계 삭제 --}}
                    @if($popupDelete)
                    <span class="text-red-600">정말로 삭제를 진행할까요?</span>
                    <button type="button" class="btn btn-danger btn-sm" wire:click="deleteConfirm">삭제</button>
                    @else
                    <button type="button" class="btn btn-danger" wire:click="delete">삭제</button>
                    @endif
                </div>
                <div> {{-- right --}}
                    <button type="button" class="btn btn-secondary"
                        wire:click="cancel">취소</button>
                    <button type="button" class="btn btn-info"
                        wire:click="update">수정</button>
                </div>
            </div>
            @else
            {{-- 생성폼 --}}
            <div class="flex justify-between">
                <div></div>
                <div class="text-right">
                    <button type="button" class="btn btn-secondary"
                        wire:click="cancel">취소</button>
                    <button type="button" class="btn btn-primary"
                        wire:click="store">저장</button>
                </div>
            </div>
            @endif
        </x-slot>
    </x-wire-dialog-modal>
    @endif

    {{-- 2단계 삭제 확인 컨펌 창 --}}
    {{-- @if($popupDelete)
        <x-wire-dialog-modal wire:model="popupDelete" maxWidth="2xl" opacity="opacity-30">
            <x-slot name="title">
                {{ __('레코드 삭제') }}
            </x-slot>

            <x-slot name="content">
                <div class="flex w-full p-5 space-x-5 lg:p-6 grow">
                    <div class="flex items-center justify-center flex-none w-16 h-16 bg-red-100 rounded-full">
                        <svg class="inline-block w-8 h-8 text-red-500 hi-solid hi-shield-exclamation"
                            fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="mb-1 text-xl font-semibold">
                            정말로 삭제를 진행할까요?
                        </h4>
                        <p class="text-gray-600">
                            삭제된 후에는 되돌리수 없습니다.
                        </p>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-btn-danger-text wire:click="deleteCancel">
                    취소
                </x-btn-danger-text>
                <x-btn-danger wire:click="deleteConfirm">
                    예, 삭제를 진행합니다.
                </x-btn-danger>
            </x-slot>
        </x-wire-dialog-modal>
    @endif --}}

</div>
