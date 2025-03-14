@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Basic Layout -->
            {{-- <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><span class="mdi mdi-file-search-outline"></span> ค้นหาข้อมูล</h5>
                    </div>
                    <div class="card-body">

                        <form action="#" method="POST" id="frmSearchGroupprice">
                            @csrf
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="scrgroups">Groups
                                        </label>
                                        <div class="col-sm-9">
                                            <div class="form-floating form-floating-outline">
                                                <select id="scrgroups" class="select2 form-select" name="scrgroups"
                                                    data-allow-clear="true">
                                                    <option value="">Select</option>
                                                    @foreach ($groupprices as $groupprice)
                                                        <option value="{{ $groupprice->id }}">
                                                            {{ $groupprice->groupname . '  ' . $groupprice->level->levelname }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <button type="button" class="btn btn-info rounded-pill" data-bs-toggle="modal"
                                        data-bs-target="#GroupsModal"><span class="mdi mdi-pencil-circle"></span> Manage
                                        Groups</button>
                                </div>

                            </div>
                            <div class="row mt-4">
                                <div class="col-md-6"></div>
                                <div class="col-md-6 justify-content-end d-flex">

                                    <button type="button" id="btnSearch"
                                        class="btn btn-primary me-sm-3 me-1 waves-effect waves-light"><span
                                            class="mdi mdi-file-search-outline"></span></button>
                                    <a href="{{ route('Pricepermeal.index') }}" id="btnReset"
                                        class="btn btn-outline-secondary waves-effect">Reset</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div> --}}
            <div class="colxxl">
                <div class="card">
                    <div class="card-header row">
                        <div class="col-md-6">
                            {{-- <h5 class="mb-0"><span class="mdi mdi-list-box"></span> รายการราคาต่อมื้อ แบ่งตามกลุ่ม</h5> --}}
                        </div>
                        <div class="col-md-6 text-end">
                            {{-- <a href="{{ route('Pricepermeal.create') }}" class="btn btn-primary rounded-pill"><span
                                    class="mdi mdi-plus-circle"></span> Add</a> --}}
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#GroupsModal"><span class="mdi mdi-pencil-circle"></span> Manage
                                    Groups</button>
                        </div>

                    </div>

                    <div class="card-datatable table-responsive pt-0">

                        <table class="datatables-basic table table-bordered" id="PricePerMealTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Groups</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal --}}
    @include('back.Pricepermeal.modal')
    {{-- Modal --}}
@endsection
@section('jscustom')
@if (session('message'))
<script>
    Swal.fire({
        title: {!! json_encode(session('message')) !!}, // ✅ ป้องกัน Error ใน JavaScript
        icon: {!! json_encode(session('class')) !!},
        customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light'
        },
        buttonsStyling: false
    });
</script>
@endif
<script>
    const GroupListUrl = "{{ route('Groupprice.index') }}";
    const GroupStoreUrl = "{{ route('Groupprice.store') }}";
    const GroupUpdateUrl = "{{ route('Groupprice.update', ':id') }}";
    const GroupDelUrl = "{{ route('Groupprice.destroy', ':id') }}";
    const ListUrl = "{{ route('Pricepermeal.list') }}";
    const DelMealUrl ="{{ route('Pricepermeal.destroy', ':id') }}";
</script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/pricepermeal.js']) }}"></script>
@endsection
@section('csscustom')
    <style>
        .hidden {
            display: none !important;
        }
    </style>
@endsection
