@if ($item->sub)
<ul>
@foreach ((array) $item->sub as $item)
    <li id="item{{ $item->id }}" class="item{{$item->collapse ? ' uk-collapsed' : ''}}" data-id="{{ $item->id }}" data-parent="1">
        <div class="uk-nestable-item">
            <div class="uk-nestable-handle"></div>
            <div data-nestable-action="toggle"></div>
            <div class="list-label"><a href="{{ cms_route('pages.edit', [$item->menu_id, $item->id]) }}">{{ $item->short_title }}</a></div>
            <div class="btn-action togglable pull-right">
                <div class="btn btn-gray item-id disabled">#{{$item->id}}</div>
                <a href="{{$newUrl = $url . '/' . $item->slug}}" class="link btn btn-white" title="Site link" data-slug="{{$item->slug}}" target="_blank">
                    <span class="fa fa-link"></span>
                </a>
                <a href="#" class="movable btn btn-white" title="Move to menu" data-id="{{$item->id}}">
                    <span class="{{icon_type('menus')}}"></span>
                </a>
                {!! Form::open(['method' => 'post', 'url' => cms_route('pages.visibility', [$item->id]), 'class' => 'visibility', 'id']) !!}
                <button type="submit" class="btn btn-{{$item->visible ? 'white' : 'gray'}}" title="{{trans('general.visibility')}}">
                    <span class="fa fa-eye{{$item->visible ? '' : '-slash'}}"></span>
                </button>
                {!! Form::close() !!}
                <a href="{{ cms_route('files.index', ['pages', $item->id]) }}" class="btn btn-{{$item->files_id ? 'turquoise' : 'white'}}" title="{{trans('general.files')}}">
                    <span class="{{icon_type('files')}}"></span>
                </a>
                <a href="{{($isAttached = in_array($item->type, cms_pages('attached'))) ? $item->collection_id ? cms_route($item->collection_type . '.index', [$item->collection_id]) : cms_route($item->type . '.index') : '#' }}" class="btn btn-{{$isAttached ? 'info' : 'white disabled'}}" title="{{$item->collection_title ?: ucfirst($item->type)}}">
                    <span class="{{$isAttached ? icon_type($item->collection_type ?: $item->type) : 'fa fa-file-text-o'}}"></span>
                </a>
                <a href="{{ cms_route('pages.create', [$item->menu_id, 'id' => $item->id]) }}" class="btn btn-secondary" title="{{trans('general.create')}}">
                    <span class="fa fa-plus"></span>
                </a>
                <a href="{{ cms_route('pages.edit', [$item->menu_id, $item->id]) }}" class="btn btn-orange" title="{{trans('general.edit')}}">
                    <span class="fa fa-edit"></span>
                </a>
                {!! Form::open(['method' => 'delete', 'url' => cms_route('pages.destroy', [$menu->id, $item->id]), 'class' => 'form-delete', 'data-id' => $item->id]) !!}
                    <button type="submit" class="btn btn-danger" title="{{trans('general.delete')}}"{{$item->sub || $item->files_id ? ' disabled' : ''}}>
                        <span class="fa fa-trash"></span>
                    </button>
                {!! Form::close() !!}
            </div>
            <a href="#" class="btn btn-primary btn-toggle pull-right visible-xs">
                <span class="fa fa-toggle-left"></span>
            </a>
        </div>
        @if (! empty($item->sub))
            @include('admin.pages.pages_tree', ['url' => $newUrl])
        @endif
    </li>
@endforeach
</ul>
@endif
