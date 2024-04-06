{{-- 수정폼을 출력하기 위한 템플릿 --}}
<x-theme theme="admin.sidebar">
    <x-theme-layout>

        <!-- start page title -->
        @if (isset($actions['view']['title']))
            @includeIf($actions['view']['title'])
        @endif
        <!-- end page title -->

        @livewire('WireForm', ['actions'=>$actions])

    </x-theme-layout>
</x-theme>

