@extends('layouts.app')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">NST Deduction</h4>

                        <div class="page-title-right">
                            <a href="#" id="exportBtn" class="btn btn-primary"><i class="bx bx-export"></i> Deduction Export</a>
                            {{-- <a href="{{ route('export.deductions') }}" class="btn btn-primary"><i class="bx bx-export"></i> Deduction Export</a> --}}
                            <a href="{{ route('deductions.create') }}" class="btn btn-primary">Add New Deduction</a>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <form id="filterForm" method="GET">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" name="search_name" class="form-control" placeholder="Search by Name" value="{{ request('search_name') }}" id="search_name">
                            </div>
                            <div class="col-md-3">
                                <select name="search_type" id="search_type" class="form-control{{ $errors->has('type') ? ' is-invalid' : '' }}">
                                    <option value="" disabled selected>Select Type</option>
                                    @php
                                        $types = ['Staff Loan', 'Salary Advance', 'Medical Ins', 'PSRA', 'Garnishment', 'Missing Goods', 'Damaged Goods', 'Bank Loan', 'Approved Pension'];    
                                    @endphp
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" @selected(isset($deduction->type) && $deduction->type == $type)>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="search_document_date" class="form-control date_of_separation" placeholder="Search by Document Date" value="{{ request('search_document_date') }}" id="search_document_date">
                            </div>

                            <div class="col-md-2">
                                <input type="text" name="search_period_date" class="form-control date_of_separation" placeholder="Search by Period Date" value="{{ request('search_period_date') }}" id="search_period_date">
                            </div>
                            
                            <div class="col-md-2">
                                <button type="button" id="searchBtn" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <x-error-message :message="$errors->first('message')" />
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                    <x-success-message :message="session('success')" />

                    <div class="card">
                        <div class="card-body">
                            <table id="deduction-list" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee No</th>
                                        <th>Employee Name</th>
                                        <th>Non Stat Deduction</th>
                                        <th>Amount</th>
                                        <th>No Of Deduction</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                    </tr>
                                </thead>

                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-include-plugins :plugins="['dataTable', 'datePicker']"></x-include-plugins>
    <script>
        $(document).ready(function() {
           let deductionTable = $('#deduction-list').DataTable({
               processing: true,
               serverSide: true,
               ajax: {
                   url: "{{ route('get-deductions-list') }}",
                   type: "POST",
                   data: function(d) {
                       d._token = "{{ csrf_token() }}";
                       d.search_name = $('#search_name').val();
                       d.search_type = $('#search_type').val();
                       d.search_document_date = $('#search_document_date').val();
                       d.search_period_date = $('#search_period_date').val();
                       return d;
                   },
                   dataSrc: function(json) {
                       return json.data || [];
                   }
               },
               columns: [
                   { 
                       data: null, 
                       render: function(data, type, row, meta) {
                           return meta.row + 1 + meta.settings._iDisplayStart;
                       }
                   },
                   { data: 'user.user_code' },
                   { data: 'user.first_name' },
                   { data: 'type' },
                   { data: 'amount' }, 
                   { data: 'no_of_payroll' }, 
                   { data: 'start_date', name: 'start_date', render: function(data) {
                        return data ? moment(data).format('DD-MM-YYYY') : 'N/A';
                    }},
                    { data: 'end_date', name: 'end_date', render: function(data) {
                        return data ? moment(data).format('DD-MM-YYYY') : 'N/A';
                    }},
               ],
               paging: true,
               pageLength: 10,
               lengthMenu: [10, 25, 50, 100],
               order: [[0, 'asc']]
           });

           $('#exportBtn').on('click', function(e) {
            let searchName = $('#search_name').val();
            let searchType = $('#search_type').val() ? $('#search_type').val() : ''; 
            let searchDocumentDate = $('#search_document_date').val();
            let searchPeriodDate = $('#search_period_date').val();

            let exportUrl = "{{ route('export.deductions') }}";
            exportUrl += `?search_name=${encodeURIComponent(searchName)}`;
            exportUrl += `&search_type=${encodeURIComponent(searchType)}`;
            exportUrl += `&search_document_date=${encodeURIComponent(searchDocumentDate)}`;
            exportUrl += `&search_period_date=${encodeURIComponent(searchPeriodDate)}`;

            window.location.href = exportUrl;
        });

           $('#searchBtn').on('click', function() {
                deductionTable.ajax.reload();
            });
       });  
   </script>
@endsection