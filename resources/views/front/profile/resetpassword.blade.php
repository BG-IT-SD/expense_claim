@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Profile /</span><span> Reset Password</span></h4>
        {{-- {{ isset($fuelPrice) ? 'Edit':'Add' }} --}}
        <div class="app-ecommerce">
            <!-- Add Product -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                <div class="d-flex flex-column justify-content-center">
                    {{-- <h4 class="mb-1 mt-3">Profile</h4> --}}
                    {{-- <p></p> --}}
                </div>
                <div class="d-flex align-content-center flex-wrap gap-3">
                    <button type="button" class="btn btn-outline-secondary"
                        onclick="window.location.href='{{ url('Profile') }}';">Discard</button>


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
                            <div class="row" id="content-edregister">
                                <div class="col-md-12 mb-3">
                                    <form method="POST" action="{{ route('profile.updatepassword',['id' => Auth::user()->id]) }}">
                                        @csrf
                                        @method('PUT')
                                        <label>รหัสผ่านปัจจุบัน:</label>
                                        <input type="password" class="form-control mb-3" name="current_password" required>

                                        <label>รหัสผ่านใหม่:</label>
                                        <input type="password" class="form-control mb-3" name="new_password" required>

                                        <label>ยืนยันรหัสผ่านใหม่:</label>
                                        <input type="password" class="form-control mb-3" class="form-control"name="new_password_confirmation" required>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary"><span class="mdi mdi-lock-check"></span> เปลี่ยนรหัสผ่าน</button>
                                        </div>
                                    </form>

                                    <!-- Show success or error messages -->
                                    @if (session('success'))
                                        <p style="color: green;">{{ session('success') }}</p>
                                    @endif

                                    @if ($errors->any())
                                        @foreach ($errors->all() as $error)
                                            <p style="color: red;">{{ $error }}</p>
                                        @endforeach
                                    @endif

                                </div>
                            </div>
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
@endsection
