@extends('layouts.app')

@section('content')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">User</h4>

                    <div class="page-title-right">
                        <a href="{{ route('users.index') }}" class="btn btn-primary">Back</a>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <form class="form-horizontal" action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <x-form-input name="first_name" value="{{ old('first_name') }}" label="First Name" placeholder="Enter your First Name" required="true"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <x-form-input name="last_name" value="{{ old('last_name') }}" label="Last Name" placeholder="Enter your Last Name" required="true"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <x-form-input name="email" value="{{ old('email') }}" label="Email" placeholder="Enter your Email" required="true"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <x-form-input type="number" label="Phone Number" name="phone_no" class="form-control" value="{{ old('phone_no')}}" placeholder="Enter your phone number" required="true"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="role">Role<span class="text-danger">*</span></label>
                                    <select name="role" id="role" class="form-control{{ $errors->has('role') ? ' is-invalid' : '' }}">
                                        <option value="" selected disabled>Select Role</option>
                                        @foreach ($roles as $key => $role)
                                            <option value="{{$role->name}}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <x-form-input name="password" type="password" label="Password" placeholder="Enter your password" required="true"/><br>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary w-md">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->
            </div>
        </div>
        <!-- end row -->
    </div>
</div>
@endsection