@extends('layouts.template')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Role /</span><span> Add Data</span></h4>
    {{-- {{ isset($fuelPrice) ? 'Edit':'Add' }} --}}
    <div class="app-ecommerce">
        <!-- Add Product -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
            <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-1 mt-3">เพิ่มข้อมูล</h4>
                {{-- <p></p> --}}
            </div>
            <div class="d-flex align-content-center flex-wrap gap-3">
                <button type="button" class="btn btn-outline-secondary"
                    onclick="window.location.href='{{ url('User') }}';">Discard</button>
                {{-- <button class="btn btn-outline-primary">Save draft</button> --}}
                <button type="button" id="saveButton" class="btn btn-primary"><span
                        class="mdi mdi-content-save"></span>
                    &nbsp;Save</button>
            </div>

        </div>

        <div class="row">
            <!-- Main form-->
            <div class="col-12 col-lg-12">
                <!-- Product Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        {{-- <h5 class="card-tile mb-0">From New Data</h5> --}}
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="empid" name="empid">
                                        <label for="empid">Employee ID</label>
                                    </div>
                                    <div id="empid-error" class="text-danger small"></div>

                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="idcard" name="idcard">
                                        <label for="idcard">เลขบัตรประจำตัวประชาชน</label>
                                    </div>
                                    <div id="idcard-error" class="text-danger small"></div>

                                </div>
                                <div class="mt-3 text-center">
                                    <button type="button" id="checkempid" class="btn btn-primary"><span class="mdi mdi-check-circle"></span> ตรวจสอบข้อมูล</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">

                                </div>
                            </div>
                        </form>

                    </div>
                </div>
                <!-- /Product Information -->
            </div>
            <!-- End Main form-->
        </div>
    </div>
</div>
@endsection
@section('jscustom')
<script>
    const CheckEmpURL =  "{{ route('CheckEmpID') }}";
</script>
<script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/usercreate.js']) }}"></script>
@endsection