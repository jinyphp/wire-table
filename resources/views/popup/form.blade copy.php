<div>
    <x-table-loading-indicator />

    <!-- 팝업 데이터 수정창 -->
    @if ($popupForm)
        <x-boot-dialog-modal wire:model="popupForm" maxWidth="4xl">
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
                {{-- 수정버튼 --}}
                @if (isset($actions['id']))
                    {{-- left --}}
                    <div>
                        <button type="button" class="btn btn-danger" wire:click="delete">
                            삭제
                        </button>
                    </div>
                    {{-- right --}}
                    <div>
                        <button type="button" wire:click="popupFormClose"
                        class="btn btn-secondary">
                            취소
                        </button>
                        <button type="button" wire:click="update"
                        class="btn btn-info">
                            수정
                        </button>
                    </div>

                @else
                    <div class="flex justify-between">
                        <div></div>
                        <div class="text-right">

                            <!-- Secondary Button (extra small) -->
                            <button type="button" wire:click="popupFormClose"
                                class="inline-flex justify-center items-center space-x-2 rounded border font-semibold focus:outline-none px-2 py-1 leading-5 text-sm border-gray-200 bg-gray-200 text-gray-700 hover:text-gray-700 hover:bg-gray-300 hover:border-gray-300 focus:ring focus:ring-gray-500 focus:ring-opacity-50 active:bg-gray-200 active:border-gray-200">
                                취소1
                            </button>
                            <!-- END Secondary Button (extra small) -->
                            <!-- Secondary Button (extra small) -->
    <button type="button"
    wire:click="store"
    class="inline-flex justify-center items-center space-x-2 rounded border font-semibold focus:outline-none px-2 py-1 leading-5 text-sm border-blue-200 bg-blue-200 text-blue-700 hover:text-blue-700 hover:bg-blue-300 hover:border-blue-300 focus:ring focus:ring-blue-500 focus:ring-opacity-50 active:bg-blue-200 active:border-blue-200">
        저장
      </button>
      <!-- END Secondary Button (extra small) -->



                        </div>
                    </div>
                @endif
            </x-slot>
        </x-boot-dialog-modal>
    @endif


    @if ($popupForm)
        <x-boot-dialog-modal wire:model="popupDelete" maxWidth="2xl" opacity="opacity-30">
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
        </x-boot-dialog-modal>
    @endif


    @if (isset($error) && $error)
        <x-boot-dialog-modal wire:model="error" maxWidth="2xl" opacity="opacity-30">
            <x-slot name="title">
                {{ __('오류') }}
            </x-slot>

            <x-slot name="content">
                {{ $message }}
            </x-slot>

            <x-slot name="footer">
                <x-btn-second wire:click="closeError">
                    닫기
                </x-btn-second>
            </x-slot>
        </x-boot-dialog-modal>
    @endif


    {{-- 퍼미션 알람 --}}
    {{-- @include('jinytable::error.popup.permit') --}}

</div>
