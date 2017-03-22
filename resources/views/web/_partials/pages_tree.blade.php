@if ($item->subItems instanceof \Illuminate\Support\Collection && $item->subItems->isNotEmpty())
<ul class="dropdown-menu">
    @foreach ($item->subItems as $item)
    <li{!!$current->slug == $item->slug ? ' class="active"' : ''!!}>
        <a href="{{web_url($item->slug)}}">{{$item->short_title}}</a>
        @include('web._partials.pages_tree')
    </li>
    @endforeach
</ul>
@endif
