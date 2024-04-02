<x-wire-table>
    <x-wire-thead>
        {{-- 테이블 제목 --}}

    </x-wire-thead>
    <tbody>
        @if(!empty($rows))
            @foreach ($rows as $item)
            <x-wire-tbody-item :selected="$selected" :item="$item">
                {{-- 테이블 리스트 --}}
                
            </x-wire-tbody-item>
            @endforeach
        @endif
    </tbody>
</x-wire-table>