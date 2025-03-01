@extends('layouts.app')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">FAQ</h4>

                        <div class="page-title-right">
                            @can('create faq')
                            <a href="{{ route('faq.create') }}" class="btn btn-primary">Add New FAQ</a>
                            @endcan
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <x-error-message :message="$errors->first('message')" />
                    <x-success-message :message="session('success')" />

                    <div class="card">
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Question</th>
                                    <th>Answer</th>
                                    @canany(['edit faq', 'delete faq'])
                                    <th>Action</th>
                                    @endcan
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($faqs as $key => $faq)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $faq->question }}</td>
                                    <td>{!! Str::words($faq->answer, 5) !!}</td>
                                    @canany(['edit faq', 'delete faq'])
                                    <td class="action-buttons">
                                        @can('edit faq')
                                        <a href="{{ route('faq.edit', $faq->id)}}" class="btn btn-primary waves-effect waves-light btn-sm edit"><i class="fas fa-pencil-alt"></i></a>
                                        @endcan
                                        @can('delete faq')
                                        <button data-source="Faq" data-endpoint="{{ route('faq.destroy', $faq->id) }}"
                                            class="delete-btn btn btn-danger waves-effect waves-light btn-sm edit">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @endcan
                                    </td>
                                    @endcan
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div> <!-- container-fluid -->
    </div>
    <x-include-plugins :plugins="['dataTable']"></x-include-plugins>
@endsection