@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Basic Layout -->
            <div class="colxxl">
                <div class="card">
                    <div class="card-header row">
                        <div class="col-md-6">
                            <h5><span class="mdi mdi-account-plus-outline"></span> แก้ไขข้อมูล</h5>
                        </div>
                        <div class="col-md-6 text-end">

                        </div>

                    </div>

                    <div class="card-body pt-0">
                        <form action="{{ route('HRgroup.updatelist') }}" id="editListHR" method="POST">
                            @csrf
                            <div class="row" id="content-check">
                                <div class="col-md-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <select name="listhr" id="listhr" class="form-select form-select-l">
                                            <option value="{{ $approveStaff->empid }}">{{ $approveStaff->email.' | '.$approveStaff->fullname}}</option>
                                        </select>
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <input type="text" id="head_email" name="head_email">
                                        <input type="text" id="head_name" name="head_name">
                                        <input type="text" id="head_id" name="head_id">
                                        <input type="text" name="groupid" id="groupid" value="{{ $approveStaff->group }}">
                                        <input type="text" name="step" id="step" value="{{ $approveStaff->step }}">
                                    </div>
                                </div>
                                @if($approveStaff->step == 9)
                                <div class="col-md-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <select name="status" id="status" class="form-select form-select-l">
                                            <option value="1" {{ isset($approveStaff) && $approveStaff->status == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ isset($approveStaff) && $approveStaff->status == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                @endif
                                <div class="mt-3 text-center">
                                    <button type="submit"  class="btn btn-primary"><span
                                            class="mdi mdi-check-circle"></span> บันทึกข้อมูล</button>
                                            <a href="{{ route('HRgroup.edit',$approveStaff->group) }}" class="btn btn-danger">กลับ</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('csscustom')
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/select2/select2.css') }}" />
@endsection
@section('jsvendor')
    <script src="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/select2/select2.js') }}"></script>
@endsection
@section('jscustom')
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/hrgroup.js']) }}"></script>
@endsection
