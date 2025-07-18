@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Basic Layout -->
            <div class="colxxl">
                <div class="card">
                    <div class="card-header row">
                        <div class="col-md-6">
                            <h5>รายชื่อ Plant </h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-sm btn-primary"  onclick="window.location.href='{{ route('HRgroup.addplant', $hrgroupId) }}'"><i class="mdi mdi-plus-circle"></i> เพิ่ม Plant</button>
                        </div>

                    </div>

                    <div class="card-datatable table-responsive pt-0">

                        <table class="datatables-basic table table-bordered" id="hrgroup">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Plant</th>
                                    <th>Status</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($hrplants as $hrplant)
                                <tr>
                                    <td>{{ $hrplant->id }}</td>
                                    <td>{{ $hrplant->plant->plantname }}</td>
                                    <td>
                                        {!! $hrplant->status == 1
                                            ? '<span class="badge rounded-pill bg-success">Active</span>'
                                            : '<span class="badge rounded-pill bg-danger">Inactive</span>' !!}
                                    </td>
                                    {{-- <td>
                                        <button class="btn btn-warning btn-sm btngroupedit"
                                        onclick="window.location.href='{{ route('HRgroup.edit', $hrplant->id) }}'"><i
                                            class="mdi mdi-pencil-circle-outline"></i>
                                        edit</button>
                                        <button type="button" class="btn btn-danger btn-sm deleteuser"
                                        data-id="{{ $hrplant->id }}"><i class="mdi mdi-trash-can"></i></button>
                                    </td> --}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr>
            <div class="colxxl">
                <div class="card">
                    <div class="card-header row">
                        <div class="col-md-6">
                            <h5>รายชื่อ HR ในกลุ่ม </h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-sm btn-primary"  onclick="window.location.href='{{ route('HRgroup.addlist', $hrgroupId) }}'"><i class="mdi mdi-plus-circle"></i> เพิ่ม รายชื่อ</button>
                        </div>

                    </div>

                    <div class="card-datatable table-responsive pt-0">

                        <table class="datatables-basic table table-bordered text-center" id="hrgrouplist">
                            <thead>
                                <tr>
                                    <th>EMPID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($staffapproves as $staffapprove)
                                    <tr>
                                        <td>{{ $staffapprove->empid }}</td>
                                        <td>{{ $staffapprove->fullname }}</td>
                                        <td>
                                            {!! $staffapprove->status == 1
                                                ? '<span class="badge rounded-pill bg-success">Active</span>'
                                                : '<span class="badge rounded-pill bg-danger">Inactive</span>' !!}
                                        </td>
                                        {{-- <td>
                                            <button class="btn btn-warning btn-sm btngroupedit"
                                        onclick="window.location.href='{{ route('HRgroup.edit', $staffapprove->id) }}'"><i
                                            class="mdi mdi-pencil-circle-outline"></i>
                                        edit</button>
                                             <button type="button" class="btn btn-danger btn-sm deleteuser"
                                        data-id="{{ $staffapprove->id }}"><i class="mdi mdi-trash-can"></i></button>
                                        </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('back.hrgroup.modal_list')
@endsection
@section('jscustom')
    @if (session('message'))
        <script>
            Swal.fire({
                title: {!! json_encode(session('message')) !!},
                icon: {!! json_encode(session('class')) !!},
                customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
            });
        </script>
    @endif
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/hrgroup.js']) }}"></script>
@endsection
