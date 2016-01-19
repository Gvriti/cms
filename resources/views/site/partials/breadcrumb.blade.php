<nav>
    <ul class="breadcrumb">
	    <li><a href="{{$url = site_url()}}">{{home_text()}}</a></li>
	@if ($breadcrumb = app_instance('breadcrumb'))
	    @foreach ($breadcrumb as $item)
	    <li{!! ($isLast = $breadcrumb->last()->slug == $item->slug) ? ' class="active"' : '' !!}>
        @if (! $isLast)
            <a href="{{$url . '/' . $item->slug}}">
        @endif
                {{$item->short_title ?: $item->title}}
        @if (! $isLast)
            </a>
        @endif
        </li>
	    @endforeach
	@endif
    </ul>
</nav>
