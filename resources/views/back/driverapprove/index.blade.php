@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Basic Layout -->

            <div class="colxxl">
                <div class="card">
                    <div class="card-header row">
                        <div class="col-md-6">
                            <h5>สายอนุมัติ พขร.</h5>
                        </div>
                        <div class="col-md-6 text-end">

                        </div>

                    </div>

                    <div class="card-datatable table-responsive pt-0">

                        <table class="datatables-basic table table-bordered text-center" id="hrgrouplist">
                            <thead>
                                <tr>
                                    <th>Step</th>
                                    <th>EMPID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($staffapproves as $staffapprove)

                                        <tr>
                                            <td>{{ $staffapprove->step }}</td>
                                            <td>{{ $staffapprove->empid }}</td>
                                            <td>{{ $staffapprove->fullname }}</td>
                                            <td>
                                                {!! $staffapprove->status == 1
                                                    ? '<span class="badge rounded-pill bg-success">Active</span>'
                                                    : '<span class="badge rounded-pill bg-danger">Inactive</span>' !!}
                                            </td>
                                            <td>
                                            <button class="btn btn-warning btn-sm btngroupedit"
                                        onclick="window.location.href='{{ route('DriverApprove.edit', $staffapprove->id) }}'"><i
                                            class="mdi mdi-pencil-circle-outline"></i>
                                        edit</button>
                                        </td>
                                        </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    <script>
        // const PlantDelUrl = "{{ route('HRgroup.delplant', ':id') }}";
    </script>
    {{-- <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/hrgroup.js']) }}"></script> --}}
@endsection
