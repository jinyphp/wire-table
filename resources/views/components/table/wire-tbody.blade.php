<tbody>
    @if(!empty($rows))
        @foreach ($rows as $item)
    
        @if (in_array($item['id'], $selected))
        <tr class="row-selected">
        @else
        <tr>
        @endif

            <td width='20'>
                <input type='checkbox' name='ids' value="{{ $item['id'] }}" 
                    class="form-check-input" wire:model="selected">
            </td>

            {{ $slot }}

            <td width="30">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                    <path
                        d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                </svg>
            </td>
        </tr>
        @endforeach
        
    @endif
</tbody>