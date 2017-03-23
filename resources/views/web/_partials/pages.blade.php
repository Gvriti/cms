<ul class="nav navbar-nav">
    <li>
        <a href="{{web_url()}}">{{home_text()}}</a>
    </li>
@if (($pages = app_instance('pagesTree')) instanceof \Illuminate\Support\Collection)
    @foreach ($pages as $item)
    <li{!!$current->slug == $item->slug ? ' class="active"' : ''!!}>
        <a href="{{web_url($item->slug)}}">{{$item->short_title}}</a>
        @include('web._partials.pages_tree')
    </li>
    @endforeach
@endif
</ul>