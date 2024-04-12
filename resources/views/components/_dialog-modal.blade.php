{{-- 테일윈드 & AlpinJS 필요 --}}
@props(['id' => null, 'maxWidth' => null])
와이어 모달
<x-wire-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    -- 모달
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
