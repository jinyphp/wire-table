<div class="d-flex justify-content-between my-2">
    <div class="">
        <h3>
        @if(isset($actions['title']))
            {{$actions['title']}}
        @endif
        </h3>
        <div class="lead text-center" style="font-size: 1rem;">
        @if(isset($actions['subtitle']))
            {{$actions['subtitle']}}
        @endif
        </div>
    </div>
    <div class="flex justify-content-end align-items-end">
        {{-- <a href="#" class="btn btn-light bg-white me-2">도움말</a> --}}
        @if(isset($actions['create']['enable']) && $actions['create']['enable'])
            @livewire('ButtonPopupCreate',['title' => $actions['create']['title']])
        @endif
    </div>
</div>
