@extends('navia::master')

@section('page_title', __('navia::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')
    <h1 class="page-title">
        <i class="navia-list-add"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
        @can('add',app($dataType->model_name))
            <a href="{{ route('navia.'.$dataType->slug.'.create') }}" class="btn btn-success">
                <i class="navia-plus"></i> {{ __('navia::generic.add_new') }}
            </a>
        @endcan
    </h1>
@stop

@section('content')
    @include('navia::menus.partial.notice')

    <div class="page-content container-fluid">
        @include('navia::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <table id="dataTable" class="table table-hover">
                            <thead>
                            <tr>
                                @foreach($dataType->browseRows as $row)
                                <th>{{ $row->display_name }}</th>
                                @endforeach
                                <th class="actions text-right">{{ __('navia::generic.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($dataTypeContent as $data)
                                <tr>
                                    @foreach($dataType->browseRows as $row)
                                    <td>
                                        @if($row->type == 'image')
                                            <img src="@if( strpos($data->{$row->field}, 'http://') === false && strpos($data->{$row->field}, 'https://') === false){{ Navia::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:100px">
                                        @else
                                            {{ $data->{$row->field} }}
                                        @endif
                                    </td>
                                    @endforeach
                                    <td class="no-sort no-click bread-actions">
                                        @can('delete', $data)
                                            <div class="btn btn-sm btn-danger pull-right delete" data-id="{{ $data->{$data->getKeyName()} }}">
                                                <i class="navia-trash"></i> {{ __('navia::generic.delete') }}
                                            </div>
                                        @endcan
                                        @can('edit', $data)
                                            <a href="{{ route('navia.'.$dataType->slug.'.edit', $data->{$data->getKeyName()}) }}" class="btn btn-sm btn-primary pull-right edit">
                                                <i class="navia-edit"></i> {{ __('navia::generic.edit') }}
                                            </a>
                                        @endcan
                                        @can('edit', $data)
                                            <a href="{{ route('navia.'.$dataType->slug.'.builder', $data->{$data->getKeyName()}) }}" class="btn btn-sm btn-success pull-right">
                                                <i class="navia-list"></i> {{ __('navia::generic.builder') }}
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('navia::generic.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">
                        <i class="navia-trash"></i> {{ __('navia::generic.delete_question') }} {{ $dataType->getTranslatedAttribute('display_name_singular') }}?
                    </h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field("DELETE") }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('navia::generic.delete_this_confirm') }} {{ $dataType->getTranslatedAttribute('display_name_singular') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('navia::generic.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <!-- DataTables -->
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "order": [],
                "language": {!! json_encode(__('navia::datatable'), true) !!},
                "columnDefs": [{"targets": -1, "searchable":  false, "orderable": false}]
                @if(config('dashboard.data_tables.responsive')), responsive: true @endif
            });
        });

        $('td').on('click', '.delete', function (e) {
            $('#delete_form')[0].action = '{{ route('navia.'.$dataType->slug.'.destroy', ['id' => '__menu']) }}'.replace('__menu', $(this).data('id'));

            $('#delete_modal').modal('show');
        });
    </script>
@stop
