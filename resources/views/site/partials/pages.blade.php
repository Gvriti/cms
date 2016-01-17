@if ($pages = app_instance('pagesTree'))
<ul class="nav navbar-nav">
    @foreach ($pages as $item)
    <li{!!$current->slug == $item->slug ? ' class="active"' : ''!!}>
        <a href="{{site_url($item->slug)}}">{{$item->short_title}}</a>
    @if (! empty($item->sub))
        @include('site.partials.pages_tree')
    @endif
    </li>
    @endforeach
</ul>
@endif