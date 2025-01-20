@extends('layouts.app')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Fortnight Dates</h4>

                        <div class="page-title-right">
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
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($fortnightsDates as $key => $fortnightsDate)
                                @php
                                    $startDate = \Carbon\Carbon::parse($fortnightsDate->start_date);
                                    $endDate = \Carbon\Carbon::parse($fortnightsDate->end_date);
                            
                                    $isCurrentFortnight = $startDate->isSameDay(\Carbon\Carbon::parse($currentFortnightDays->start_date)) && $endDate->isSameDay(\Carbon\Carbon::parse($currentFortnightDays->end_date));
                                    @endphp
                                <tr class="{{ $isCurrentFortnight ? 'current-fortnight' : '' }}">
                                    <td>{{ ++$key }}</td>
                                    <td>@if($fortnightsDate->start_date){{ \Carbon\Carbon::parse($fortnightsDate->start_date)->format('d-m-Y') }}@else N/A @endif</td>
                                    <td>@if($fortnightsDate->end_date){{ \Carbon\Carbon::parse($fortnightsDate->end_date)->format('d-m-Y') }}@else N/A @endif</td>
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