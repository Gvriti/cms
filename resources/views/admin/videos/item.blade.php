@if (! empty($model) || ! empty($modelInput))
<li id="item{{$model->id}}" data-id="{{$model->id}}" data-pos="{{$model->position}}" data-url="{{cms_route('videos.edit', [$model->gallery_id, $model->id])}}" class="item col-lg-4 col-md-6 col-sm-6 col-xs-12">
    <div class="album-image">
        <a href="#" class="thumb embed-responsive embed-responsive-16by9" data-modal="edit">
            <iframe src="{{getYoutubeEmbed($modelInput['file'])}}" frameborder="0" allowfullscreen class="embed-responsive-item"></iframe>
        </a>
        <a href="#" class="name">
            <span class="title">{{$modelInput['title']}}</span>
            <em>{{$model->created_at->format('d F Y')}}</em>
        </a>
        <div class="image-options">
            <a href="#" data-url="{{cms_route('videos.visibility', [$model->id])}}" class="visibility">
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