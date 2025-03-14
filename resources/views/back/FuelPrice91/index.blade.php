@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Basic Layout -->
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><span class="mdi mdi-file-search-outline"></span> ค้นหาข้อมูล</h5>
                    </div>
                    <div class="card-body">

                        <form action="{{ route('FuelPrice91.index') }}" method="GET" id="frmSearch">
                            {{-- @csrf --}}
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="StartDate">StartDate
                                        </label>
                                        <div class="col-sm-9">
                                            <input type="text" id="StartDate" name="startdate"
                                                value="{{ request('startdate') }}"
                                                class="form-control dob-picker flatpickr-input" placeholder="YYYY-MM-DD">
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="EndDate">EndDate
                                        </label>
                                        <div class="col-sm-9">
                                            <input type="text" id="EndDate" name="enddate"
                                                value="{{ request('enddate') }}"
                                                class="form-control dob-picker flatpickr-input" placeholder="YYYY-MM-DD">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row mt-4">
                                <div class="col-md-6"></div>
                                <div class="col-md-6 justify-content-end d-flex">

                                    <button type="submit" id="btnSearch"
                                        class="btn btn-primary me-sm-3 me-1 waves-effect waves-light"><span
                                            class="mdi mdi-file-search-outline"></span></button>
                                    <a href="{{ route('FuelPrice91.index') }}" id="btnReset"
                                        class="btn btn-outline-secondary waves-effect">Reset</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    {{-- <div class="card-header row"> --}}
                        {{-- <div class="col-md-6">
                            <h5 class="mb-0"><span class="mdi mdi-list-box"></span> รายการราคาน้ำมัน 91</h5>
                        </div> --}}
                        {{-- <div class="col-md-6 text-end">
                            <button class="btn btn-primary rounded-pill addprice" data-bs-toggle="modal"
                                data-bs-target="#AddPriceModal"><span class="mdi mdi-plus-circle"></span> Add</button>
                        </div> --}}

                    {{-- </div> --}}

                    <div class="card-datatable table-responsive pt-0">

                        <table class="datatables-basic table table-bordered" id="FuelPrice91Table">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Date</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fuelprice91s as $fuelprice91)
                                    <tr>
                                        <td>{{ $fuelprice91->id }}</td>
                                        <td>{{ $fuelprice91->dateprice }}</td>
                                        <td>{{ $fuelprice91->price }}</td>
                                        <td>
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#AddPriceModal"
                                                data-bs-id="{{ $fuelprice91->id }}"
                                                class="btn btn-sm btn-warning editprice"><span
                                                    class="mdi mdi-pencil-circle-outline"></span> Edit</button>
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#DelPriceModal"
                                                data-bs-id="{{ $fuelprice91->id }}"
                                                class="btn btn-sm btn-danger delprice"><span
                                                    class="mdi mdi-trash-can"></span></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <?php
                        // echo '<pre>';
                        // print_r($oilLists);
                        // echo '</pre>';
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal --}}
    @include('back.FuelPrice91.modal')
    {{-- End Modal --}}
@endsection
@section('jscustom')
    <script>
        const FuelpriceStoreUrl = "{{ route('FuelPrice91.store') }}";
        const FuelpriceUpdateUrl = "{{ route('FuelPrice91.update', ':id') }}";
        const FuelpriceDelUrl = "{{ route('FuelPrice91.destroy', ':id') }}";
    </script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/fuelprice91.js']) }}"></script>
@endsection
