{{-- 목록을 출력하기 위한 템플릿 --}}
<x-theme theme="admin.sidebar">
    <x-theme-layout>
        <!-- start page title -->
        @if (isset($actions['view']['title']) && !empty($actions['view']['title']))
            @includeIf($actions['view']['title'])
        @else
            @include("jinytable::title")
        @endif
        <!-- end page title -->

        <div class="alert alert-danger" role="alert">
            사용 권한이 없습니다.
        </div>

        {{-- SuperAdmin Actions Setting --}}
        @if(Module::has('Actions'))
            @livewire('setActionRule', ['actions'=>$actions])
        @endif

    </x-theme-layout>
</x-theme>
