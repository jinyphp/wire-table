{{-- Title --}}
@if(isset($actions['view']['title']))
    @includeIf($actions['view']['title'])
@else
    @includeIf("jiny-wire-table::show.title")
@endif

{{-- CRUD 테이블 --}}
<section class="p-2">
    <main>
        @if(isset($actions['view']['main']))
            @includeIf($actions['view']['main'],[
                'actions'=>$actions,
                'row'=>$row
            ])
        @else
            <p>
                출력할 main 화면이 지정되어 있지 않습니다.
            </p>
        @endif
    </main>
</section>
