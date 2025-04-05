@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                {{-- <h5 class="card-title">Search Filter</h5>
            <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0">
                <div class="col-md-4 user_role"></div>
                <div class="col-md-4 user_plan"></div>
                <div class="col-md-4 user_status"></div>
            </div> --}}
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table" id="datatables-users">
                    <thead class="border-top table-light">
                        <tr>
                            <th>ID</th>
                            <th>EMPID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            {{-- <th>BU</th> --}}
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->empid }}</td>
                                <td>{{ $user->fullname }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info rolemodal" data-id="{{ $user->id }}"
                                        data-empid="{{ $user->empid }}" data-fullname="{{ $user->fullname }}"><span
                                            class="mdi mdi-account-key"></span> &nbsp;Role</button>
                                </td>
                                <td>
                                    {!! $user->status == 1
                                        ? '<span class="badge rounded-pill bg-success">Active</span>'
                                        : '<span class="badge rounded-pill bg-danger">Inactive</span>' !!}
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm btnedit"
                                        onclick="window.location.href='{{ route('User.edit', $user->id) }}'"><i
                                            class="mdi mdi-pencil-circle-outline"></i>
                                        edit</button>
                                        {{-- <button type="button" class="btn btn-danger btn-sm deleteuser"
                                        data-id="{{ $user->id }}"><i class="mdi mdi-trash-can"></i></button> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('back.user.modal')
@endsection

@section('csscustom')
    {{-- <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/select2/select2.css') }}" /> --}}
    <style>
        .hidden {
            display: none !important;
        }
    </style>
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
        const UserRoleListUrl = "{{ route('UserRole.index') }}";
        const RoleStoreUrl = "{{ route('UserRole.store') }}";
        const RoleUpdateUrl = "{{ route('UserRole.update', ':id') }}";
        const RoleDelUrl = "{{ route('UserRole.destroy', ':id') }}";
        const UserDelUrl = "{{ route('User.destroy', ':id') }}";
    </script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/user.js']) }}"></script>
@endsection

@section('jsvendor')
    {{-- <script src="{{ asset('template/assets/vendor/libs/select2/select2.js') }}"></script> --}}
@endsection
