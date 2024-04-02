{{-- 테일윈드 & AlpinJS 필요 --}}
@props(['id' => null, 'maxWidth' => null])

<x-wire-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>

    {{-- 입력받는 title값을 바이패스 --}}
    @if (isset($title))
    <x-slot name="title">
        {{ $title }}
    </x-slot>
    @endif

    @if (isset($content))
    <div class="px-2 py-1">
        {{ $content }}
    </div>
    @endif

    @if (isset($footer))
    <div class="px-4 py-3 bg-white">
        {{ $footer }}
    </div>
    @endif

</x-wire-modal>
