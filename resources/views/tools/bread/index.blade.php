@extends('navia::master')

@section('page_title', __('navia::generic.viewing').' '.__('navia::generic.bread'))

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-bread"></i> {{ __('navia::generic.bread') }}
    </h1>
@stop

@section('content')

    <div class="page-content container-fluid">
        @include('navia::alerts')
        <div class="row">
            <div class="col-md-12">

                <table class="table table-striped database-tables">
                    <thead>
                        <tr>
                            <th>{{ __('navia::database.table_name') }}</th>
                            <th style="text-align:right">{{ __('navia::bread.bread_crud_actions') }}</th>
                        </tr>
                    </thead>

                @foreach($tables as $table)
                    @continue(in_array($table->name, config('navia.database.tables.hidden', [])))
                    <tr>
                        <td>
                            <p class="name">
                                <a href="{{ route('navia.database.show', $table->prefix.$table->name) }}"
                                   data-name="{{ $table->prefix.$table->name }}" class="desctable">
                                   {{ $table->name }}
                                </a>
                                <i class="voyager-data"
                                   style="font-size:25px; position:absolute; margin-left:10px; margin-top:-3px;"></i>
                            </p>
                        </td>

                        <td class="actions text-right">
                            @if($table->dataTypeId)
                                <a href="{{ route('navia.' . $table->slug . '.index') }}"
                                   class="btn btn-warning btn-sm browse_bread" style="margin-right: 0;">
                                    <i class="voyager-plus"></i> {{ __('navia::generic.browse') }}
                                </a>
                                <a href="{{ route('navia.bread.edit', $table->name) }}"
                                   class="btn btn-primary btn-sm edit">
                                    <i class="voyager-edit"></i> {{ __('navia::generic.edit') }}
                                </a>
                                <a href="#delete-bread" data-id="{{ $table->dataTypeId }}" data-name="{{ $table->name }}"
                                     class="btn btn-danger btn-sm delete">
                                    <i class="voyager-trash"></i> {{ __('navia::generic.delete') }}
                                </a>
                            @else
                                <a href="{{ route('navia.bread.create', $table->name) }}"
                                   class="_btn btn-default btn-sm pull-right">
                                    <i class="voyager-plus"></i> {{ __('navia::bread.add_bread') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
    </div>
    {{-- Delete BREAD Modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_builder_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('navia::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i>  {!! __('navia::bread.delete_bread_quest', ['table' => '<span id="delete_builder_name"></span>']) !!}</h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_builder_form" method="POST">
                        {{ method_field('DELETE') }}
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="submit" class="btn btn-danger" value="{{ __('navia::bread.delete_bread_conf') }}">
                    </form>
                    <button type="button" class="btn btn-outline pull-right" data-dismiss="modal">{{ __('navia::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal modal-info fade" tabindex="-1" id="table_info" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('navia::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-data"></i> @{{ table.name }}</h4>
                </div>
                <div class="modal-body" style="overflow:scroll">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>{{ __('navia::database.field') }}</th>
                            <th>{{ __('navia::database.type') }}</th>
                            <th>{{ __('navia::database.null') }}</th>
                            <th>{{ __('navia::database.key') }}</th>
                            <th>{{ __('navia::database.default') }}</th>
                            <th>{{ __('navia::database.extra') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="row in table.rows">
                            <td><strong>@{{ row.Field }}</strong></td>
                            <td>@{{ row.Type }}</td>
                            <td>@{{ row.Null }}</td>
                            <td>@{{ row.Key }}</td>
                            <td>@{{ row.Default }}</td>
                            <td>@{{ row.Extra }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-right" data-dismiss="modal">{{ __('navia::generic.close') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@stop

@section('javascript')

    <script>

        var table = {
            name: '',
            rows: []
        };

        new Vue({
            el: '#table_info',
            data: {
                table: table,
            },
        });

        $(function () {

            // Setup Delete BREAD
            //
            $('table .actions').on('click', '.delete', function (e) {
                id = $(this).data('id');
                name = $(this).data('name');

                $('#delete_builder_name').text(name);
                $('#delete_builder_form')[0].action = '{{ route('navia.bread.delete', ['__id']) }}'.replace('__id', id);
                $('#delete_builder_modal').modal('show');
            });

            // Setup Show Table Info
            //
            $('.database-tables').on('click', '.desctable', function (e) {
                e.preventDefault();
                href = $(this).attr('href');
                table.name = $(this).data('name');
                table.rows = [];
                $.get(href, function (data) {
                    $.each(data, function (key, val) {
                        table.rows.push({
                            Field: val.field,
                            Type: val.type,
                            Null: val.null,
                            Key: val.key,
                            Default: val.default,
                            Extra: val.extra
                        });
                        $('#table_info').modal('show');
                    });
                });
            });
        });
    </script>

@stop
