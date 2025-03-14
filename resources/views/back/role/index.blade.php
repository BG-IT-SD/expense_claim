@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-3 ">
            <div class="card-header bg-dark">
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-module table" id="datatables-module">
                    <thead class="border-top table-light">
                        <tr>
                            <th>ID</th>
                            <th>Module</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($modules as $module)
                            <tr>
                                <td>{{ $module->id }}</td>
                                <td>{{ $module->modulename }}</td>
                                <td>
                                    {!! $module->status == 1
                                        ? '<span class="badge rounded-pill bg-success">Active</span>'
                                        : '<span class="badge rounded-pill bg-danger">Inactive</span>' !!}
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm editmodule"
                                        onclick="window.location.href='/Role/{{ $module->id }}/edit/1'"><i
                                            class="mdi mdi-pencil-circle-outline"></i>
                                        edit</button>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="deleteRole({{ $module->id }}, 1);">
                                            <i class="mdi mdi-trash-can"></i>
                                        </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mb-3 ">
            <div class="card-header bg-primary">
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-role table" id="datatables-role">
                    <thead class="border-top table-light">
                        <tr>
                            <th>ID</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->rolename }}</td>
                                <td>
                                    {!! $role->status == 1
                                        ? '<span class="badge rounded-pill bg-success">Active</span>'
                                        : '<span class="badge rounded-pill bg-danger">Inactive</span>' !!}
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm editmodule"
                                        onclick="window.location.href='/Role/{{ $role->id }}/edit/2'"><i
                                            class="mdi mdi-pencil-circle-outline"></i>
                                        edit</button>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="deleteRole({{ $role->id }}, 2);">
                                            <i class="mdi mdi-trash-can"></i>
                                        </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('csscustom')
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
       const ModuleDelUrl = "{{ route('Role.destroy', [':id', ':type']) }}";
    </script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/role.js']) }}"></script>
@endsection
