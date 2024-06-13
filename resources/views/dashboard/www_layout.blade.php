<x-www-layout>
    <div class="wrapper">
        {{-- left sidemenu --}}

        <aside style="margin-left:50px;width: 250px;">
            @includeIf("jinyerp-hr-home::users.menu_users")
        </aside>

        {{-- main --}}
        <div class="main">

            <section class="p-4">
                @if(isset($actions['view']['title']))
                    @includeIf($actions['view']['title'])
                @else

                @endif
            </section>

            <section class="p-4">
                <main>
                    @if(isset($actions['view']['main']))
                        @if(is_array($actions['view']['main']))
                            @foreach($actions['view']['main'] as $main)
                                @includeIf($main)
                            @endforeach
                        @else
                            @includeIf($actions['view']['main'])
                        @endif
                    @else
                        <p>view main 페이지 목록이 지정되어 있지 않습니다. </p>
                    @endif
                </main>
            </section>

        </div>

    </div>
</x-www-layout>


