@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Basic Layout -->
            <div class="colxxl">
                <div class="card">
                    <div class="card-header row">
                        <div class="col-md-6">
                            <h5><span class="mdi mdi-account-plus-outline"></span> เพิ่ม Plant</h5>
                        </div>
                        <div class="col-md-6 text-end">

                        </div>

                    </div>

                    <div class="card-body pt-0">
                        <form action="{{ route('HRgroup.saveplant') }}" id="addplantHR" method="POST">
                            @csrf
                            <div class="row" id="content-check">
                                <div class="col-md-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <select name="plant" id="plant" class="form-select form-select-l">
                                            <option value="">เลือก plant</option>
                                            @foreach ($plants as $plant)
                                            <option value="{{ $plant->id }}">{{ $plant->plantname }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="groupid" value="{{ $id }}">
                                    </div>


                                </div>
                                <div class="mt-3 text-center">
                                    <button type="submit"  class="btn btn-primary"><span
                                            class="mdi mdi-check-circle"></span> บันทึกข้อมูล</button>
                                            <a href="{{ route('HRgroup.edit',$id) }}" class="btn btn-danger">กลับ</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('jscustom')
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/hrgroup.js']) }}"></script>
@endsection
