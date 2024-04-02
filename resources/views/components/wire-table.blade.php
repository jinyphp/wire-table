<style>
    .row-selected {
        background-color: #fcf7c2;
    }
</style>

<table {{$attributes->merge(['class' => 'table table-striped table-centered mb-0'])}}>
    {{$slot}}
</table>



