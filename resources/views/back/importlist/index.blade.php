@extends('layouts.template')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><span class="mdi mdi-file-search-outline"></span> ค้นหาข้อมูล</h5>
                    </div>
                    <div class="card-body">

                        <form action="#" method="GET" id="frmSearch">
                            {{-- @csrf --}}
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="groups">Groups
                                        </label>
                                        <div class="col-sm-9">
                                            <select name="groups" id="groups" class="form-control">
                                                <option value="">select</option>
                                                @foreach ($typegroups as $typegroup)
                                                    <option value="{{ $typegroup->id }}">{{ $typegroup->groupname }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" id="btnSearch"
                                        class="btn btn-primary me-sm-3 me-1 waves-effect waves-light"><span
                                            class="mdi mdi-file-search-outline"></span></button>
                                    <a href="{{ route('importlist.index') }}" id="btnReset"
                                        class="btn btn-outline-secondary waves-effect">Reset</a>

                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-6"></div>
                                <div class="col-md-6 text-end">
                                    <a href="{{ route('importlist.import') }}"
                                        class="btn btn-primary waves-effect waves-light"><span
                                            class="mdi mdi-import"></span> Import รายชื่อ</a>
                                    <a href="{{ route('Typegroup.index') }}"
                                        class="btn btn-secondary waves-effect waves-light"><span
                                            class="mdi mdi-square-edit-outline"></span> จัดการประเภทกลุ่ม</a>
                                    {{-- <button type="button" class="btn btn-secondary waves-effect waves-light"><span
                                            class="mdi mdi-file-excel"></span> ตัวอย่างไฟล์</button> --}}

                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="datatables-users table" id="importtable">
                            <thead class="border-top table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>EMPID</th>
                                    <th>Name</th>
                                    <th>Group</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groupspecials as $groupspecial)
                                    <tr>
                                        <td>{{ $groupspecial->id }}</td>
                                        <td>{{ $groupspecial->empid }}</td>
                                        <td>{{ $groupspecial->fullname }}</td>
                                        <td>{{ $groupspecial->Typegroup->groupname }}</td>
                                        <td>
                                            {!! $groupspecial->status == 1
                                                ? '<span class="badge rounded-pill bg-success">Active</span>'
                                                : '<span class="badge rounded-pill bg-danger">Inactive</span>' !!}
                                        </td>
                                        <td>
                                            {{-- <button class="btn btn-warning btn-sm btnedit"
                                                onclick=""><i
                                                    class="mdi mdi-pencil-circle-outline"></i>
                                                edit</button> --}}
                                            <button type="button" class="btn btn-danger btn-sm deletegropspecial"
                                                data-id="{{ $groupspecial->id }}"><i class="mdi mdi-trash-can"></i></button>
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
    @include('back.importlist.modal')
@endsection

@section('jscustom')
<script>
     const ImportDelUrl = "{{ route('importlist.destroy', ':id') }}";
</script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/importlist.js']) }}"></script>
@endsection
