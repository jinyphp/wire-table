<x-theme>
    <x-theme-layout>

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
                <a href="#" class="btn btn-light bg-white me-2">도움말</a>
                @livewire('ButtonPopupCreate',['title' => "추가"])
            </div>
        </div>

        @livewire('WireTable', ['actions'=>$actions])

        @livewire('WirePopupForm', ['actions'=>$actions])


        {{-- @livewire('Popup-LiveManual') --}}

        {{-- SuperAdmin Actions Setting --}}
        @if(Module::has('Actions'))
            @livewire('setActionRule', ['actions'=>$actions])
        @endif

        {{-- popup UI Design mode --}}
        {{-- @livewire('DesignForm') --}}

    </x-theme-layout>
</x-theme>
