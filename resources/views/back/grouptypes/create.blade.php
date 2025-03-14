@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Type Groups /</span><span> Add Data</span></h4>
        <div class="app-ecommerce">
            <!-- Add Type Groups -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                <div class="d-flex flex-column justify-content-center">
                    <h4 class="mb-1 mt-3">เพิ่มข้อมูล</h4>
                </div>
                <div class="d-flex align-content-center flex-wrap gap-3">
                    <button type="button" class="btn btn-outline-secondary"
                        onclick="window.location.href='{{ url('Typegroup') }}';">Discard</button>
                    <button type="button" id="saveButton" class="btn btn-primary"><span
                            class="mdi mdi-content-save"></span>
                        &nbsp;Save</button>
                </div>

            </div>

            <div class="row">
                <!-- Main form-->
                <div class="col-12 col-lg-12">
                    <!-- Type Groups Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                        </div>
                        <div class="card-body">
                            <form
                                action="{{ isset($typegroups) ? route('Typegroup.update', $typegroups->id) : route('Typegroup.store') }}"
                                method="POST" id="frmTypegroups">
                                @csrf
                                @if (isset($typegroups))
                                    @method('PUT') <!-- ใช้ PUT method เมื่อเป็นการแก้ไข -->
                                @endif
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="groupname" name="groupname"
                                                value="{{ old('groupname', $typegroups->groupname ?? '') }}">
                                            <label for="groupname">ชื่อประเภทกลุ่ม</label>
                                        </div>
                                        @error('groupname')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror

                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating form-floating-outline mb-4">
                                            <select class="form-select" id="status" name="status"
                                                aria-label="Default select example">
                                                <option value="1"
                                                    {{ isset($typegroups) && $typegroups->status == 1 ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="0"
                                                    {{ isset($typegroups) && $typegroups->status == 0 ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                            <label for="status">status</label>
                                        </div>
                                    </div>
                                </div>


                            </form>

                        </div>
                    </div>
                    <!-- /Type Groups Information -->
                </div>
                <!-- End Main form-->
            </div>
        </div>
    </div>
@endsection
@section('jscustom')
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/grouptype.js']) }}"></script>
@endsection
