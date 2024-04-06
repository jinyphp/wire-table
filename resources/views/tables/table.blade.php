<div>
    <x-loading-indicator/>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-2 border-r">
                    {{-- 페이징: 한페이지에 보여주는 데이터의 갯수 --}}
                    <div class="mb-3">
                        <label for="simpleinput" class="form-label">페이징</label>
                        <select class="form-select" style="width=200px;"
                            id="table-pasing-number"
                            wire:model="paging">
                            <option value='5'>5</option>
                            <option value='10'>10</option>
                            <option value='20'>20</option>
                            <option value='50'>50</option>
                            <option value='100'>100</option>
                        </select>
                    </div>
                </div>
                <div class="col-10">
                    {{-- 필터를 적용시 filter.blade.php 를 읽어 옵니다. --}}
                    @if (isset($actions['view']['filter']))
                        <div class="row justify-content-center">
                            @includeIf($actions['view']['filter'])
                        </div>
                        <div class="d-flex justify-content-center justify-content-center gap-2">
                            <button class="btn btn-primary" wire:click="filter_search">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline-block" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                </svg>
                                검색
                            </button>
                            <button class="btn btn-secondary" wire:click="filter_reset">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline-block" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                </svg>
                                취소
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    {{-- 메시지를 출력합니다. --}}
    @if (session()->has('message'))
        <div class="alert alert-success">{{session('message')}}</div>
    @endif


    <div class="card">
        @if (isset($actions['table_title']))
        <div class="card-header">
            <h4 class="header-title">{{$actions['table_title']}}</h4>
            <p class="text-muted font-14">

            </p>
        </div>
        @endif

        <div class="card-body">
            @if (isset($actions['view']['list']))
                @includeIf($actions['view']['list'])
            @endif

            @if(empty($rows))
            <div>
                목록이 없습니다.
            </div>
            @endif
        </div>

        <div class="card-footer">
            @if (isset($rows) && is_object($rows))
                @if(method_exists($rows, "links"))
                {{ $rows->links() }}
                @endif
            @endif

            {{-- 선택갯수 표시--}}
            <span id="selected-num">{{count($selected)}}</span>
            <span class="px-2">selected</span>

            @if (count($selected))
            <button type="button" class="btn btn-danger btn-sm" id="selected-delete"
                wire:click="popupDeleteOpen">선택삭제</button>
            @else
            <button type="button" class="btn btn-danger btn-sm" id="selected-delete"
                wire:click="popupDeleteOpen" disabled>선택삭제</button>
            @endif
        </div>
    </div>


    {{-- 선택삭제 --}}
    @include("jiny-wire-table::popup.delete")

    {{-- 퍼미션 알람--}}
    @include("jiny-wire-table::error.popup.permit")

</div>
