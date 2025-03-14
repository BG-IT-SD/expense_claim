@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="datatables-users table" id="Grouptypetable">
                            <thead class="border-top table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Typename</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($typegroups as $typegroup)
                                    <tr>
                                        <td>{{ $typegroup->id}}</td>
                                        <td>{{ $typegroup->groupname}}</td>
                                        <td>
                                            {!! $typegroup->status == 1
                                                ? '<span class="badge rounded-pill bg-success">Active</span>'
                                                : '<span class="badge rounded-pill bg-danger">Inactive</span>' !!}
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm btnedit"
                                                onclick="window.location.href='{{ route('Typegroup.edit', $typegroup->id) }}'"><i
                                                    class="mdi mdi-pencil-circle-outline"></i>
                                                edit</button>
                                                {{-- <button type="button"
                                                class="btn btn-danger btn-sm deletetype" data-id="{{ $typegroup->id }}"><i
                                                    class="mdi mdi-trash-can"></i></button> --}}
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
        const TypegroupDelUrl ="{{ route('Typegroup.destroy', ':id') }}";

    </script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/grouptype.js']) }}"></script>
@endsection
