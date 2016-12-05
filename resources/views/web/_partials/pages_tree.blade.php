@if (! empty($item->subPages))
<ul class="dropdown-menu">
    @foreach ($item->subPages as $item)
    <li{!!$current->slug == $item->slug ? ' class="active"' : ''!!}>
        <a href="{{web_url($item->slug)}}">{{$item->short_title}}</a>
        @include('web._partials.pages_tree')
    </li>
    @endforeach
</ul>
@endif
