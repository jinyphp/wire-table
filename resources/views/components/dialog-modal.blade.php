@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    @if(isset($title) && $title)
    <div class="px-4 pt-4">
        <div class="text-lg font-medium text-gray-900">
            {{ $title }}
        </div>
    </div>
    @endif

    <div class="px-4 pb-2">
        <div class="mt-4 text-sm text-gray-600">
            {{ $content }}
        </div>
    </div>

    {{-- <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-end"> --}}
    <div class="px-4 py-3 bg-gray-100">
        {{ $footer }}
    </div>
</x-modal>
