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
                            <form action="{{ route('User.update', $users->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row" id="content-edregister">
                                    <div class="col-md-12 mb-3">
                                        <ul class="list-unstyled my-3 py-1">
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="mdi mdi-identifier mdi-24px"></i><span
                                                    class="fw-medium mx-2">รหัสพนักงาน:</span>
                                                <span id="text-empid">{{ $users->empid }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="mdi mdi-account-outline mdi-24px"></i><span
                                                    class="fw-medium mx-2">ชื่อ - นามสกุล:</span> <span
                                                    id="text-fullname">{{ $users->fullname }}</span>
                                            </li>

                                            <li class="d-flex align-items-center mb-3">
                                                <i class="mdi mdi-star-outline mdi-24px"></i><span
                                                    class="fw-medium mx-2">แผนก:</span>
                                                <span id="text-dept">{{ $users->dept }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="mdi mdi-email-check-outline mdi-24px"></i><span
                                                    class="fw-medium mx-2">E-Mail:</span>
                                                <span id="text-email">{{ $users->email }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-1">
                                                <i class="mdi mdi-domain mdi-24px"></i><span
                                                    class="fw-medium mx-2">Bu:</span>
                                                <span id="text-bu">{{ $users->bu }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-1">
                                                <i class="mdi mdi-lock-reset mdi-24px"></i>
                                                <button type="button" id="resetpassword"
                                                    class="btn btn-sm btn-danger">Reset Password</button>
                                            </li>
                                            {{-- <li class="d-flex align-items-center mb-1">
                                                <i class="mdi mdi-draw mdi-24px"></i>
                                                <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#signatureModal">
                                                    อัปโหลดลายเซ็น
                                                </button>
                                            </li> --}}
                                        </ul>
                                    </div>
                                    <div class="col-md-12 mb-3 mt-3">
                                        <h6>ลายเซ็น</h6>
                                        @if ($users->sigfile && $users->sigfile->path)
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="card border shadow-sm p-3">
                                                        {{-- <h6 class="text-muted">ลายเซ็นเดิม</h6> --}}
                                                        <img src="{{ asset('storage/' . $users->sigfile->path) }}"
                                                            class="img-thumbnail" style="max-height:150px;">
                                                    </div>
                                                </div>
                                                <div class="col-md-6"></div>
                                            </div>
                                        @else
                                            <p class="text-danger">ยังไม่มีลายเซ็น</p>
                                        @endif

                                        <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal"
                                            data-bs-target="#signatureModal">
                                            อัปโหลดลายเซ็น
                                        </button>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating form-floating-outline mb-4">
                                            <select class="form-select" id="status" name="status"
                                                aria-label="Default select example">
                                                <option value="1"
                                                    {{ isset($users) && $users->status == 1 ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="0"
                                                    {{ isset($users) && $users->status == 0 ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                            <label for="status">status</label>
                                            <input type="hidden" name="userid" id="userid"
                                                value="{{ $users->id }}">
                                            <input type="hidden" name="empid" id="empid"
                                                value="{{ $users->empid }}">
                                        </div>
                                        @error('status')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror

                                    </div>
                                    <hr>
                                    <button type="submit" id="edsaveButton" class="btn btn-primary"><span
                                            class="mdi mdi-content-save"></span>
                                        &nbsp;Save</button>

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

    <!-- Modal -->
    <div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="signatureForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="empid" value="{{ $users->empid }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="signatureModalLabel">อัปโหลดลายเซ็น</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="sigfile" class="form-label">เลือกลายเซ็น</label>
                            <input type="file" class="form-control" name="sigfile" id="sigfile"
                                accept="image/*,application/pdf" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('jscustom')
    <script>
        const UserResetUrl = "{{ route('User.reset', ':id') }}";
        const UserSigUrl = "{{ route('User.uploadSignature') }}";
    </script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/usercreate.js']) }}"></script>
@endsection
