@if (! empty($model) || ! empty($modelInput))
<li id="item{{$model->id}}" data-id="{{$model->id}}" data-pos="{{$model->position}}" data-url="{{cms_route('files.edit', [$model->route_name, $model->route_id, $model->id])}}" class="item col-md-2 col-sm-4 col-xs-6">
    <div class="album-image">
        <a href="#" class="thumb" data-modal="edit">
        @if (in_array($ext = pathinfo($modelInput['file'], PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
            <img src="{{$modelInput['file']}}" class="img-responsive" alt="{{$modelInput['title']}}" />
        @elseif( ! empty($ext))
            <img src="{{asset('assets/images/file-ext-icons/'.$ext.'.png')}}" class="img-responsive" alt="{{$modelInput['title']}}" />
        @else
            <img src="{{asset('assets/images/file-ext-icons/www.png')}}" class="img-responsive" alt="{{$modelInput['title']}}" />
        @endif
        </a>
        <a href="#" class="name">
            <span class="title">{{$modelInput['title']}}</span>
            <em>{{$model->created_at->format('d F Y')}}</em>
        </a>
        <div class="image-options">
            <div class="file-checkbox select-item dib">
                <input type="checkbox" data-id="{{$model->id}}" class="cbr" />
            </div>
            <a href="#" data-url="{{cms_route('files.visibility', [$model->id])}}" class="visibility">
                <i class="fa fa-eye{{$model->visible ? '' : '-slash'}}"></i>
            </a>
            <a href="#" data-modal="edit"><i class="fa fa-pencil"></i></a>
            <a href="#" data-delete="this" data-id="{{$model->id}}"><i class="fa fa-trash"></i></a>
        </div>
    </div>
</li>
@endif