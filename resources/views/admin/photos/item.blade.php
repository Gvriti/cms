@if (! empty($model) || ! empty($modelInput))
<li id="item{{$model->id}}" data-id="{{$model->id}}" data-pos="{{$model->position}}" data-url="{{cms_route('photos.edit', [$model->gallery_id, $model->id])}}" class="item col-md-3 col-sm-4 col-xs-6">
    <div class="album-image">
        <a href="#" class="thumb" data-modal="edit">
            <img src="{{$model->file ?: $model->file_default}}" class="img-responsive" alt="{{$modelInput['title']}}" />
        </a>
        <a href="#" class="name">
            <span class="title">{{$modelInput['title']}}</span>
            <em>{{$model->created_at->format('d F Y')}}</em>
        </a>
        <div class="image-options">
            <a href="#" data-url="{{cms_route('photos.visibility', [$model->id])}}" class="visibility">
                <i class="fa fa-eye{{$model->visible ? '' : '-slash'}}"></i>
            </a>
            <a href="#" data-modal="edit"><i class="fa fa-pencil"></i></a>
            <a href="#" data-delete="this" data-id="{{$model->id}}"><i class="fa fa-trash"></i></a>
        </div>
        <div class="image-checkbox select-item">
            <input type="checkbox" data-id="{{$model->id}}" class="cbr" />
        </div>
    </div>
</li>
@endif