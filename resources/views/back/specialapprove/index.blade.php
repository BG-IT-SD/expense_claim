@extends('layouts.template')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-datatable table-responsive pt-0">
                        <div class="p-3">
                            <a href="{{ route('SpecialApprove.create') }}" class="btn btn-success mb-3">
                                <i class="mdi mdi-plus-circle-outline"></i> เพิ่มรายชื่ออนุมัติพิเศษ
                            </a>
                        </div>

                        <table class="datatables-basic table table-bordered" id="ApproveSpecialTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Emp ID</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->empid }}</td>
                                        <td>{{ $item->fullname }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>
                                            {!! $item->status == 1
                                                ? '<span class="badge rounded-pill bg-success">Active</span>'
                                                : '<span class="badge rounded-pill bg-danger">Inactive</span>' !!}
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm btngroupedit"
                                                onclick="window.location.href='{{ route('SpecialApprove.edit', $item->id) }}'">
                                                <i class="mdi mdi-pencil-circle-outline"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm deleteapprove"
                                                data-id="{{ $item->id }}">
                                                <i class="mdi mdi-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> {{-- end card-datatable --}}
                </div>
            </div>
        </div>
    </div>

@section('jscustom')
    @if (session('message'))
        <script>
            Swal.fire({
                title: {!! json_encode(session('message')) !!},
                icon: {!! json_encode(session('class')) !!}, // success, error, info, warning
                confirmButtonText: 'ตกลง',
                customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
            });
        </script>
    @endif

    {{-- SweetAlert Delete Confirm --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.deleteapprove').on('click', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: "คุณต้องการลบรายชื่อนี้หรือไม่",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/SpecialApprove/' + id,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                Swal.fire('สำเร็จ!', 'ข้อมูลถูกลบแล้ว', 'success')
                                    .then(() => location.reload());
                            },
                            error: function(xhr) {
                                let msg = 'ไม่สามารถลบข้อมูลได้';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                Swal.fire('ผิดพลาด', msg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection

@endsection
