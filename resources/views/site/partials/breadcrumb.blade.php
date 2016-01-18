<nav>
    <ul class="breadcrumb">
	    <li><a href="{{$url = site_url()}}">{{home_text()}}</a></li>
	@if ($breadcrumb = app_instance('breadcrumb'))
	    @foreach ($breadcrumb as $item)
	    <li{!! $breadcrumb->last()->id == $item->id ? ' class="active"' : '' !!}>
            <a href="{{$url . '/' . $item->slug}}">{{$item->short_title :? $item->title}}</a>
        </li>
	    @endforeach
	@endif
    </ul>
</nav>
