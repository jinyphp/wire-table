<ol class="m-0 breadcrumb">
    @php
        $breadcrumb = "/";
    @endphp

    <li class="breadcrumb-item">
        <a href="{{$breadcrumb}}">
            Home
        </a>
    </li>

    @foreach (explode("/",$slot) as $item)
        @php
            $breadcrumb .= $item."/";
        @endphp
        <li class="breadcrumb-item">
            <a href="{{$breadcrumb}}">
                {{$item}}
            </a>
        </li>
    @endforeach
</ol>
