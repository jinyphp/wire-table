<x-theme theme="admin.sidebar">
    <x-theme-layout>

        {{-- Title --}}
        @if(isset($actions['view']['title']))
            @includeIf($actions['view']['title'])
        @else
            @includeIf("jiny-wire-table::table_popup_forms.title")
        @endif

        {{-- CRUD 테이블 --}}
        <section class="p-2">
            <main>
                @livewire('WireTable-PopupForm', [
                    'actions'=>$actions
                ])
            </main>
        </section>

    </x-theme-layout>
</x-theme>
