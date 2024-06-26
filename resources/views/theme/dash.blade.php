{{-- 테마의 레이아웃을 적용합니다--}}
<x-theme>
    <x-theme-layout>

        {{-- Title --}}
        <section>
        @if(isset($actions['view']['title']))
            @includeIf($actions['view']['title'])
        @else
            @includeIf("jiny-wire-table::dash.title")
        @endif
        </section>



        {{-- dashboard 메인 컨덴츠 출력 --}}
        <section>
            @if(isset($actions['view']['main']))
                @includeIf($actions['view']['main'])
            @else
                <div class="alert alert-danger" role="alert">
                    컨트롤러에서 출력할 main 화면이 지정되어 있지 않습니다.
                </div>
            @endif
        </section>

    </x-theme-layout>
</x-theme>
