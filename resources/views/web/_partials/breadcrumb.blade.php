<nav>
    <ul class="breadcrumb">
        <li><a href="{{$url = web_url('/')}}">{{trans('general.home')}}</a></li>
        @if ($breadcrumb = app_instance('breadcrumb'))
            @foreach ($breadcrumb as $item)
                <li{!! $loop->last ? ' class="active"' : '' !!}>
                    @if (! $loop->last)
                        <a href="{{$url . '/' . $item->slug ?? $item->id}}">
                            @endif
                            {{$item->short_title ?: $item->title}}
                            @if (! $loop->last)
                        </a>
                    @endif
                </li>
            @endforeach
        @endif
    </ul>
</nav>
