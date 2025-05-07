@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">PricePerMeal /</span><span> {{ isset($pricepermeal) ? 'Edit':'Add'}} Data</span></h4>

        <div class="app-ecommerce">
            <!-- Add Product -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                <div class="d-flex flex-column justify-content-center">
                    <h4 class="mb-1 mt-3">{{ isset($pricepermeal) ? 'แก้ไข':'เพิ่ม'}}ข้อมูลราคาต่อมื้อ</h4>
                    <p>ราคาแยกตามกลุ่ม</p>
                </div>
                <div class="d-flex align-content-center flex-wrap gap-3">
                    <button type="button" class="btn btn-outline-secondary"
                        onclick="window.location.href='{{ url('Pricepermeal') }}';">Discard</button>
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
                        <div class="card-body row">
                            <form
                                action="{{ isset($pricepermeal) ? route('Pricepermeal.update', $pricepermeal->id) : route('Pricepermeal.store') }}"
                                method="POST" id="frmPricepermeal">
                                @csrf
                                @if (isset($pricepermeal))
                                    @method('PUT')
                                @endif
                                <div class="col-md-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <select id="groupprice" name="groupprice" class="select2 form-select"
                                            data-placeholder="Option" data-allow-clear="true">
                                            <option value="">เลือกกลุ่ม</option>
                                            @foreach ($groupprices as $groupprice)
                                                <option value="{{ $groupprice->id }}"
                                                    data-level="{{ $groupprice->levelid }}"
                                                    {{ isset($pricepermeal) && $pricepermeal->groupid == $groupprice->id ? 'selected' : '' }}>
                                                    {{ $groupprice->groupname . ' ' . $groupprice->level->levelname }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="levelid" name="levelid">
                                        <label for="groupprice">กลุ่ม</label>
                                    </div>
                                    @error('groupprice')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                    <div id="groupprice-error" class="text-danger small"></div>
                                </div>
                                <hr>
                                <div class="col-md-12 mb-3">
                                    {{-- Level 1 - 7 --}}
                                    <div class="card mb-3" id="cardlevel1">
                                        <div class="card-header bg-primary">
                                            <h5 class="card-tile mb-0 text-white"><span
                                                    class="mdi mdi-account-group-outline"></span> ราคาเบิกอาหารแต่ละมื้อ
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive text-nowrap">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>
                                                                <h5><span class="badge rounded-pill bg-label-primary"><span
                                                                            class="mdi mdi-clock-time-eight"></span>
                                                                        มื้อเช้า (บาท)</span></h5>

                                                            </th>
                                                            <th>
                                                                <h5><span class="badge rounded-pill bg-label-success"><span
                                                                            class="mdi mdi-clock-time-eleven"></span>
                                                                        มื้อกลางวัน (บาท)</span></h5>
                                                            </th>
                                                            <th>
                                                                <h5><span class="badge rounded-pill bg-label-danger"><span
                                                                            class="mdi mdi-clock-time-five"></span> มื้อเย็น
                                                                        (บาท)</span></h5>
                                                            </th>
                                                            <th>
                                                                <h5><span class="badge rounded-pill bg-label-info"><span
                                                                            class="mdi mdi-clock-time-nine"></span> มื้อดึก
                                                                        (บาท)</span></h5>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input type="number" class="form-control" id="meal1"
                                                                    name="meal1" min="0" step="0.01"
                                                                    value="{{ old('meal1', $pricepermeal->meal1 ?? 0) }}" >
                                                                <div id="meal1-error" class="text-danger small"></div>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control" id="meal2"
                                                                    name="meal2" min="0" step="0.01"
                                                                    value="{{ old('meal2', $pricepermeal->meal2 ?? 0) }}" >
                                                                <div id="meal2-error" class="text-danger small"></div>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control" id="meal3"
                                                                    name="meal3" min="0" step="0.01"
                                                                    value="{{ old('meal3', $pricepermeal->meal3 ?? 0) }}" >
                                                                <div id="meal3-error" class="text-danger small"></div>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control" id="meal4"
                                                                    name="meal4" min="0" step="0.01"
                                                                    value="{{ old('meal4', $pricepermeal->meal4 ?? 0) }}" >

                                                                {{-- <div id="meal4-error" class="text-danger small"></div> --}}

                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- End Level 1 - 7 --}}
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="card mb-3" id="cardlevel1">
                                        <div class="card-header bg-dark">
                                            <h5 class="card-tile mb-0 text-white"><span
                                                    class="mdi mdi-office-building-marker-outline"></span> BU ที่ร่วมใช้งาน
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @php $selected = is_array($selectedPlants ?? []) ? $selectedPlants : []; @endphp
                                            @foreach ($plants as $plant)
                                                <div class="form-check col-md-4">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="plant_{{ $plant->id }}"
                                                        name="plants[]"
                                                        value="{{ $plant->id }}"
                                                        {{ in_array($plant->id, $selected) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="plant_{{ $plant->id }}">{{ $plant->plantname }}</label>
                                                </div>
                                            @endforeach
                                            </div>
                                            @error('plants')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                            <div id="plants-error" class="text-danger small"></div>
                                        </div>
                                    </div>
                                </div>
                                @if (isset($pricepermeal))
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select" name="status" id="status" required>
                                        <option value="1" {{ old('status', $pricepermeal->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $pricepermeal->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">สถานะ</label>
                                </div>
                                @endif

                                {{-- <div class="mt-3 float-end">

                                </div> --}}

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
        const MealStoreUrl = "{{ route('Pricepermeal.store') }}";
    </script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/pricepermeal_create.js']) }}"></script>
@endsection
