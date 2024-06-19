
<x-flex-between>
    <button class="btn btn-primary" type="button"
        data-bs-toggle="collapse"
        data-bs-target="#collapseFilter"
        aria-expanded="false"
        aria-controls="collapseFilter">

        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
            <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z"/>
        </svg>

        조건필터
    </button>

    <div>
        @if(isset($actions['create']['enable']) && $actions['create']['enable'])
        {{-- WireTable내의 create 메소드를 호출합니다. --}}
        <button class="btn btn-primary" wire:click="create">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
            </svg>
            @if(isset($actions['create']['title']))
            {{$actions['create']['title']}}
            @endif
        </button>
        @endif
    </div>
</x-flex-between>


<div class="collapse bg-white p-2 y-2" id="collapseFilter">
    <x-flex class="pt-2">
        {{-- 페이징: 한페이지에 보여주는 데이터의 갯수 --}}
        {{-- <div class="d-flex gap-2 align-items-center">
            <label for="table-pasing-number" class="form-label">
                페이징
            </label>
            <select class="form-select flex-grow-1"
                id="table-pasing-number"  style="width: unset;"
                wire:model.live="paging">
                <option value='5'>5</option>
                <option value='10'>10</option>
                <option value='20'>20</option>
                <option value='50'>50</option>
                <option value='100'>100</option>
            </select>
        </div> --}}
        <div style="width:150px;">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-6 text-sm-end">페이징</label>
                <div class="col-sm-6">
                    <select class="form-select"
                        wire:model.live="paging">
                        <option value='5'>5</option>
                        <option value='10'>10</option>
                        <option value='20'>20</option>
                        <option value='50'>50</option>
                        <option value='100'>100</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex-grow-1 border-l">
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

        <div style="width:150px;">
        </div>
    </x-flex-between>
</div>

