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
                        onclick="window.location.href='{{ url('Role') }}';">Discard</button>
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
                            <form action="{{ isset($roles) ? route('Role.update', $roles->id) : route('Role.store') }}"
                                method="POST" id="frmPermission">
                                @csrf
                                @if (isset($roles))
                                    @method('PUT') <!-- ใช้ PUT method เมื่อเป็นการแก้ไข -->
                                @endif
                                @if ($type == 1)
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control" id="modulename" name="modulename"
                                                    value="{{ old('modulename', $roles->modulename ?? '') }}">
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <label for="modulename">ชื่อหน้าจอการทำงาน</label>
                                            </div>
                                            @error('modulename')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror

                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-floating form-floating-outline mb-4">
                                                <select class="form-select" id="status" name="status"
                                                    aria-label="Default select example">
                                                    <option value="1"
                                                        {{ isset($roles) && $roles->status == 1 ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option value="0"
                                                        {{ isset($roles) && $roles->status == 0 ? 'selected' : '' }}>
                                                        Inactive</option>
                                                </select>
                                                <label for="status">status</label>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($type == 2)
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control" id="rolename" name="rolename"
                                                    value="{{ old('rolename', $roles->rolename ?? '') }}">
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <label for="rolename">ชื่อสิทธิ</label>
                                            </div>
                                            @error('rolename')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror

                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-floating form-floating-outline mb-4">
                                                <select class="form-select" id="status" name="status"
                                                    aria-label="Default select example">
                                                    <option value="1"
                                                        {{ isset($roles) && $roles->status == 1 ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option value="0"
                                                        {{ isset($roles) && $roles->status == 0 ? 'selected' : '' }}>
                                                        Inactive</option>
                                                </select>
                                                <label for="status">status</label>
                                            </div>
                                        </div>
                                    </div>
                                @endif


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
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/role.js']) }}"></script>
@endsection
