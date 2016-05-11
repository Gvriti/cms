<ul class="dropdown-menu">
    @foreach ($item->sub as $item)
    <li{!!$current->slug == $item->slug ? ' class="active"' : ''!!}>
        <a href="{{site_url($item->slug)}}">{{$item->short_title}}</a>
    @if (! empty($item->sub))
        @include('site._partials.pages_tree')
    @endif
    </li>
    @endforeach
</ul>
