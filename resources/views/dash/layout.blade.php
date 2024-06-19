{{-- Title --}}
@if(isset($actions['view']['title']))
    @includeIf($actions['view']['title'])
@else
    @includeIf("jiny-wire-table::dash.title")
@endif

{{-- CRUD 테이블 --}}
<section>
    <main>

        @if(isset($actions['view']['main']))

            @includeIf($actions['view']['main'])
        @else
        <div class="alert alert-danger" role="alert">
            컨트롤러에서 출력할 main 화면이 지정되어 있지 않습니다.
        </div>
        @endif
    </main>
</section>
