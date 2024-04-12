@push('css')
<style>
    .row-selected {
        background-color: #fcf7c2;
    }

    table th {
        padding: 12px 8px;
    }

    table tr {
        border-bottom: 1px solid #dddddd;
    }

    table tr:last-child {
        /*border-bottom: none;*/ /* 마지막 tr에 대한 밑줄을 없애는 스타일 */
    }

    table td {
        padding: 12px 8px;
    }
</style>
@endpush
{{-- table-striped table-centered mb-0 --}}
<table {{$attributes->merge(['class' => ''])}}>
    {{$slot}}
</table>



