@extends('navia::master')

@section('page_title', __('navia::generic.media'))

@section('content')
    <div class="page-content container-fluid">
        @include('navia::alerts')
        <div class="row">
            <div class="col-md-12">

                <div class="admin-section-title">
                    <h3><i class="navia-images"></i> {{ __('navia::generic.media') }}</h3>
                </div>
                <div class="clear"></div>
                <div id="filemanager">
                    <media-manager
                        base-path="{{ config('navia.media.path', '/') }}"
                        :show-folders="{{ config('navia.media.show_folders', true) ? 'true' : 'false' }}"
                        :allow-upload="{{ config('navia.media.allow_upload', true) ? 'true' : 'false' }}"
                        :allow-move="{{ config('navia.media.allow_move', true) ? 'true' : 'false' }}"
                        :allow-delete="{{ config('navia.media.allow_delete', true) ? 'true' : 'false' }}"
                        :allow-create-folder="{{ config('navia.media.allow_create_folder', true) ? 'true' : 'false' }}"
                        :allow-rename="{{ config('navia.media.allow_rename', true) ? 'true' : 'false' }}"
                        :allow-crop="{{ config('navia.media.allow_crop', true) ? 'true' : 'false' }}"
                        :details="{{ json_encode(['thumbnails' => config('navia.media.thumbnails', []), 'watermark' => config('navia.media.watermark', (object)[])]) }}"
                        ></media-manager>
                </div>
            </div><!-- .row -->
        </div><!-- .col-md-12 -->
    </div><!-- .page-content container-fluid -->
@stop

@section('javascript')
<script>
new Vue({
    el: '#filemanager'
});
</script>
@endsection
