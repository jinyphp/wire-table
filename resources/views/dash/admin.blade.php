<x-admin>
    {{-- Title --}}
    @if (isset($actions['view']['title']))
        @includeIf($actions['view']['title'])
    @else
        <div class="d-flex justify-content-between">
            <div class="page-title-box">
                <x-flex class="align-items-center gap-2">
                    <h1 class="align-middle h3 d-inline">
                        @if (isset($actions['title']))
                            {{ $actions['title'] }}
                        @endif
                    </h1>
                    {{-- <x-badge-info>Admin</x-badge-info> --}}
                </x-flex>
                <p>
                    @if (isset($actions['subtitle']))
                        {{ $actions['subtitle'] }}
                    @endif
                </p>
            </div>

            <div class="page-title-box">

                <x-breadcrumb-item>
                    {{ $actions['route']['uri'] }}
                </x-breadcrumb-item>

                <div class="mt-2 d-flex justify-content-end gap-2">
                    <x-btn-video>
                        Video
                    </x-btn-video>

                    <x-btn-manual>
                        Manual
                    </x-btn-manual>
                </div>
            </div>

        </div>

    @endif

    {{-- CRUD 테이블 --}}
    <section>


            @if (isset($actions['view']['main']))
                @includeIf($actions['view']['main'])
            @else
                <div class="alert alert-danger" role="alert">
                    컨트롤러에서 출력할 main 화면이 지정되어 있지 않습니다.
                </div>
            @endif

    </section>

</x-admin>
{{-- <x-theme name="admin.sidebar">
    <x-theme-layout>

        @includeIf("jiny-wire-table::dash.layout")

    </x-theme-layout>
</x-theme> --}}
