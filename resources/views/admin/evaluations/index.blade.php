@extends('admin.master')

@section('title', __('admin.Evaluations'))
@section('sub-title', __('admin.Evaluations'))
@section('evaluations-menu-open', 'menu-open')
@section('evaluations-active', 'active')
@section('index-evaluations-active', 'active')

@section('styles')
    <style>
        /* modal  */
        .modal-body {
            height: 150px;
            /* overflow-y: scroll; */
        }
    </style>
@stop

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                @can('add_evaluation')
                <div class="card-header">
                    <div class="d-flex  justify-content-between">

                        <div class="btn-website">
                            <a title="{{ __('admin.Add Evaluation') }}" href="{{ route('admin.evaluations.create') }}" class="btn btn-primary btn-flat"><i
                                    class="fas fa-plus"></i> {{ __('admin.Add Evaluation') }}</a>

                        </div>

                    </div>
                </div>
                @endcan
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped  table-hover ">
                        <thead>
                            <tr style="background-color: #1e272f; color: #fff;">
                                <th>#</th>
                                <th>{{ __('admin.Evaluation Name') }}</th>
                                <th>{{ __('admin.For') }}</th>
                                <th>{{ __('admin.Starts Date') }}</th>
                                <th>{{ __('admin.Ends Date') }}</th>
                               @canAny(['delete_evaluation','edit_evaluation'])
                               <th>{{ __('admin.Actions') }}</th>
                               @endcanAny
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($evaluations as $evaluation)
                                <tr id="row_{{ $evaluation->slug }}">
                                    <td>{{ $evaluation->id }}</td>
                                    <td>{{ $evaluation->name }}</td>
                                    <td>{{ $evaluation->evaluation_type == 'company' ? 'Company' : 'Students' }}</td>
                                    <td>{{ Carbon::parse($evaluation->start_date)->format('Y/m/j')}}</td>
                                    <td>{{ Carbon::parse($evaluation->end_date)->format('Y/m/j') }}</td>
                                    @canAny(['delete_evaluation','edit_evaluation'])

                                    <td>
                                        <div style="display: flex; gap: 5px">
                                            @can('edit_evaluation')
                                            <a title="{{ __('admin.Edit') }}" href="{{ route('admin.evaluations.edit', $evaluation->slug) }}" class="btn btn-primary btn-sm btn-edit btn-flat"> <i class="fas fa-edit"></i> </a>

                                            @endcan
                                            @can('delete_evaluation')
                                            <form class="d-inline delete_form" action="{{ route('admin.evaluations.destroy', $evaluation->slug) }}" method="POST">
                                                @csrf
                                                @method('delete')
                                                <button title="{{ __('admin.Delete') }}" class="btn btn-danger btn-sm btn-delete btn-flat"> <i class="fas fa-trash"></i> </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                    @endcanAny
                                </tr>
                            @empty
                                <td colspan="12" style="text-align: center">
                                    <img src="{{ asset('adminAssets/dist/img/folder.png') }}" alt="" width="300" >
                                    <br>
                                    <h4>{{ __('admin.NO Data Selected') }}</h4>
                                </td>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->

            </div>
            <!-- /.card -->
            <div class="mb-3">
                {{ $evaluations->links() }}
            </div>
        </div>
    </div>


@stop
