<ul class="nav navbar-nav">
    <li>
        <a href="{{site_url()}}">{{home_text()}}</a>
    </li>
@if ($pages = app_instance('pagesTree'))
    @foreach ($pages as $item)
    <li{!!$current->slug == $item->slug ? ' class="active"' : ''!!}>
        <a href="{{site_url($item->slug)}}">{{$item->short_title}}</a>
        @include('site._partials.pages_tree')
    </li>
    @endforeach
@endif
</ul>