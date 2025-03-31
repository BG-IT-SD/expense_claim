@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">DistanceRate /</span><span>
                {{ isset($DistanceRate) ? 'Edit' : 'Add' }} Data</span></h4>

        <div class="app-ecommerce">
            <!-- Add Product -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                <div class="d-flex flex-column justify-content-center">
                    <h4 class="mb-1 mt-3">เพิ่มข้อมูลระยะทางจาก Plant to Plant</h4>
                    {{-- <p></p> --}}
                </div>
                <div class="d-flex align-content-center flex-wrap gap-3">
                    <button type="button" class="btn btn-outline-secondary"
                        onclick="window.location.href='{{ url('DistanceRate') }}';">Discard</button>
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
                            <form
                                action="{{ isset($DistanceRate) ? route('DistanceRate.update', $DistanceRate->id) : route('DistanceRate.store') }}"
                                method="POST" id="frmDistanceRate">
                                @csrf
                                @if (isset($DistanceRate))
                                    @method('PUT') <!-- ใช้ PUT method เมื่อเป็นการแก้ไข -->
                                @endif
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <div class="form-floating form-floating-outline">
                                            <select id="startplant" name="startplant" class="form-control w-100 text-dark"
                                                data-style="btn-default">
                                                <option value="">เลือก Plant</option>
                                                @foreach ($plants as $plant)
                                                    <option value="{{ $plant->id }}">
                                                        {{ $plant->plantname }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="startplant">Plant เริ่มต้น</label>
                                        </div>
                                        @error('startplant')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror

                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-floating form-floating-outline">
                                            <select id="endplant" name="endplant" class="form-control w-100 text-dark"
                                                data-style="btn-default">
                                                <option value="">เลือก Plant</option>
                                                @foreach ($plants as $plant)
                                                    <option value="{{ $plant->id }}">
                                                        {{ $plant->plantname }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="endplant">Plant สิ้นสุด</label>
                                        </div>
                                        @error('endplant')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" class="form-control" id="kilometer" name="kilometer"
                                                value="{{ old('kilometer', $DistanceRate->kilometer ?? '') }}"
                                                min="0" step="0.01">
                                            <label for="kilometer">จำนวนกิโลเมตร</label>
                                        </div>
                                        @error('kilometer')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-floating form-floating-outline mb-4">
                                            <select class="form-select" id="status" name="status"
                                                aria-label="Default select example">
                                                <option value="1"
                                                    {{ isset($DistanceRate) && $DistanceRate->status == 1 ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="0"
                                                    {{ isset($DistanceRate) && $DistanceRate->status == 0 ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                            <label for="status">status</label>
                                        </div>
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
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/distancerate.js']) }}"></script>
@endsection