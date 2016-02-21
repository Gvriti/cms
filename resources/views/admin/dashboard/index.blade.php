@extends('admin.app')
@section('content')
<div class="page-title">
    <div class="title-env">
        <h1 class="title">
            <i class="fa fa-dashboard"></i>
            Dashboard
        </h1>
        <p class="description">The main page of the cms</p>
    </div>
    <div class="breadcrumb-env">
        <ol class="breadcrumb bc-1">
            <li class="active">
                <i class="fa fa-dashboard"></i>
                <strong>Dashboard</strong>
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <div class="xe-widget xe-counter xe-counter-red"  data-count=".num" data-from="1" data-to="{{$menusTotal}}" data-duration="2" data-easing="true" data-delay="1">
            <a href="{{cms_route('menus.index')}}" class="xe-icon">
                <i class="{{icon_type('menus')}}"></i>
            </a>
            <div class="xe-label">
                <strong class="num">{{$menusTotal}}</strong>
                <span>Total Menus</span>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="xe-widget xe-counter" data-count=".num" data-count=".numTotal" data-from="1" data-to="{{$pagesTotal}}" data-duration="3">
            <a href="{{is_null($mainPage) ? cms_route('menus.index') : cms_route('pages.index', [$mainPage->id])}}" class="xe-icon">
                <i class="{{icon_type('pages')}}"></i>
            </a>
            <div class="xe-label">
                <strong class="num">{{$pagesMainTotal}}</strong>
                <span>Main Pages</span>
            </div>
            <div class="xe-label">
                <strong class="num numTotal">{{$pagesTotal}}</strong>
                <span>Total Pages</span>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="xe-widget xe-counter xe-counter-info" data-count=".num" data-from="0" data-to="{{$collectionsTotal}}" data-duration="3" data-easing="true">
            <a href="{{cms_route('collections.index')}}" class="xe-icon">
                <i class="{{icon_type('collections')}}"></i>
            </a>
            <div class="xe-label">
                <strong class="num">{{$collectionsTotal}}</strong>
                <span>Total Collections</span>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="xe-widget xe-counter xe-counter-blue" data-count=".num" data-from="1" data-to="{{$usersTotal}}" data-duration="2" data-easing="true">
            <a href="{{cms_route('cmsUsers.index')}}" class="xe-icon">
                <i class="{{icon_type('cms_users')}}"></i>
            </a>
            <div class="xe-label">
                <strong class="num">{{$usersTotal}}</strong>
                <span>Total CMS Users</span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <div class="xe-widget xe-counter-block"  data-count=".num" data-from="0" data-to="{{$catalogTotalDistinct}}" data-duration="3">
            <div class="xe-upper">
                <a href="{{cms_route('collections.index', ['type' => 'catalog'])}}" class="xe-icon">
                    <i class="{{icon_type('catalog')}}"></i>
                </a>
                <div class="xe-label">
                    <strong class="num">{{$catalogTotalDistinct}}</strong>
                    <span>Catalog by Category</span>
                </div>
            </div>
            <div class="xe-lower">
                <div class="border"></div>
                <span>Details</span>
                <strong>{{$catalogTotal}} Total catalog</strong>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="xe-widget xe-counter-block xe-counter-block-blue"  data-count=".num" data-from="0" data-to="{{$articlesTotalDistinct}}" data-duration="3" data-easing="true">
            <div class="xe-upper">
                <a href="{{cms_route('collections.index', ['type' => 'articles'])}}" class="xe-icon">
                    <i class="{{icon_type('articles')}}"></i>
                </a>
                <div class="xe-label">
                    <strong class="num">{{$articlesTotalDistinct}}</strong>
                    <span>Articles by Category</span>
                </div>
            </div>
            <div class="xe-lower">
                <div class="border"></div>
                <span>Details</span>
                <strong>{{$articlesTotal}} Total Article</strong>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="xe-widget xe-counter-block xe-counter-block-danger"  data-count=".num" data-from="0" data-to="{{$photosTotal}}" data-duration="3">
            <div class="xe-upper">
                <a href="{{cms_route('collections.index', ['type' => 'galleries'])}}" class="xe-icon">
                    <i class="{{icon_type('photos')}}"></i>
                </a>
                <div class="xe-label">
                    <strong class="num">{{$photosTotal}}</strong>
                    <span>Total photos</span>
                </div>
            </div>
            <div class="xe-lower">
                <div class="border"></div>
                <span>Details</span>
                <strong>{{$photoAlbumTotal}} Total Photo Albums</strong>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="xe-widget xe-counter-block xe-counter-block-purple"  data-count=".num" data-from="0" data-to="{{$videosTotal}}" data-duration="3">
            <div class="xe-upper">
                <a href="{{cms_route('collections.index', ['type' => 'galleries'])}}" class="xe-icon">
                    <i class="{{icon_type('videos')}}"></i>
                </a>
                <div class="xe-label">
                    <strong class="num">{{$videosTotal}}</strong>
                    <span>Total Videos</span>
                </div>
            </div>
            <div class="xe-lower">
                <div class="border"></div>
                <span>Details</span>
                <strong>{{$videoAlbumTotal}} Total Video Albums</strong>
            </div>
        </div>
    </div>
</div>
<div class="row">
@if ($notes)
    <div class="col-sm-3">
        <div class="xe-widget xe-todo-list">
            <div class="xe-header">
                <a href="{{cms_route('notes.index')}}" class="xe-icon">
                    <i class="fa fa-file-text-o"></i>
                </a>
                <div class="xe-label">
                    <span>Last 5 note</span>
                    <strong>Notes</strong>
                </div>
            </div>
            <div class="xe-body">
                <ul class="list-unstyled">
                @foreach ($notes as $item)
                    <li>
                        <label>
                            <span>{{$item->title}}</span>
                        </label>
                    </li>
                @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
    <div class="col-sm-3">
        <div class="xe-widget xe-counter-block xe-counter-block-orange"  data-count=".num" data-from="0" data-to="{{$calendarTotal}}" data-duration="3">
            <div class="xe-upper">
                <a href="{{cms_route('calendar.index')}}" class="xe-icon">
                    <i class="fa fa-calendar"></i>
                </a>
                <div class="xe-label">
                    <strong class="num">{{$calendarTotal}}</strong>
                    <span>Total Calendar Events</span>
                </div>
            </div>
            <div class="xe-lower">
                <div class="border"></div>
                <span>Details</span>
                <strong>{{count($calendarEvents)}} Events between 1 week</strong>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="xe-widget xe-counter-block xe-counter-block-turquoise"  data-count=".num" data-from="0" data-to="{{$filesTotal}}" data-duration="3">
            <div class="xe-upper">
                <div class="xe-icon">
                    <i class="{{icon_type('files')}}"></i>
                </div>
                <div class="xe-label">
                    <strong class="num">{{$filesTotal}}</strong>
                    <span>Total attached files</span>
                </div>
            </div>
            <div class="xe-lower">
                <div class="border"></div>
                <span>Details</span>
                <strong>Files used in {{$filesTotalDistinct}} category</strong>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="xe-widget xe-counter-block xe-counter-block-info"  data-count=".num" data-from="0" data-to="{{$galleriesTotal}}" data-duration="3">
            <div class="xe-upper">
                <div class="xe-icon">
                    <i class="{{icon_type('galleries')}}"></i>
                </div>
                <div class="xe-label">
                    <strong class="num">{{$galleriesTotal}}</strong>
                    <span>Total galleries</span>
                </div>
            </div>
            <div class="xe-lower">
                <div class="border"></div>
                <span>Details</span>
                <strong>{{$photoAlbumTotal + $videoAlbumTotal}} total gallery items</strong>
            </div>
        </div>
    </div>
</div>
<!-- Imported scripts on this page -->
<script src="{{asset('assets/js/xenon-widgets.js')}}"></script>
@endsection
