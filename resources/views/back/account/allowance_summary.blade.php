@extends('layouts.template')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card p-3">
            <h5 class="card-header">
                <i class="mdi mdi-file-chart-outline"></i> รายงานสรุปเบี้ยเลี้ยงประจำเดือน
            </h5>

            {{--  ฟอร์มค้นหา --}}
            <form id="searchForm" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">ค้นหาจากรหัสพนักงาน</label>
                    <input type="text" name="search_empid" id="search_empid" value="{{ $search_empid ?? '' }}"
                        class="form-control" placeholder="พิมพ์รหัส...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ค้นหาจากรายชื่อผู้เบิกเงิน</label>
                    <input type="text" name="search_name" id="search_name" value="{{ $search_name ?? '' }}"
                        class="form-control" placeholder="พิมพ์ชื่อ...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ค้นหาจากสถานที่ฯ</label>
                    <input type="text" name="search_plant" id="search_plant" value="{{ $search_plant ?? '' }}"
                        class="form-control" placeholder="พิมพ์สถานที่...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ค้นหาจากหน่วยงาน</label>
                    <input type="text" name="search_department" id="search_department"
                        value="{{ $search_department ?? '' }}" class="form-control" placeholder="พิมพ์หน่วยงาน...">
                </div>


                {{-- วันที่ และปุ่ม --}}
                <div class="col-md-4">
                    <label class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $start ?? '' }}"
                        class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $end ?? '' }}" class="form-control">
                </div>

                <div class="col-md-4 text-end">
                    <button type="button" id="searchButton" class="btn btn-primary">
                        <i class="mdi mdi-magnify"></i> ค้นหา
                    </button>
                    <a href="{{ route('Account.AllowanceReport.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-refresh"></i> ล้างค่า
                    </a>

                    <div id="exportButtons" class="d-inline-block" style="display: none!important;">
                        <a href="#" id="exportExcel" target="_blank" class="btn btn-success ms-1"><i
                                class="mdi mdi-file-excel"></i></a>
                        {{-- <a href="#" id="exportPdf" target="_blank" class="btn btn-danger ms-1"><i
                                class="mdi mdi-file-pdf-box"></i></a> --}}
                    </div>
                </div>
            </form>

            <hr>

            {{--  ตาราง  --}}
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered table-hover" id="reportTable">
                    <thead class="table-dark text-center align-middle">
                        <tr>
                            <th>รอบการจ่าย</th>
                            <th>EXID</th>
                            <th>ลำดับที่</th>
                            <th>สถานที่ไป<br>ปฏิบัติงาน</th>
                            <th>รหัสพนักงาน</th>
                            <th>ชื่อ - นามสกุล</th>
                            <th>หน่วยงาน</th>
                            <th>ระดับ</th>
                            <th>จากวันที่</th>
                            <th>ถึงวันที่</th>
                            <th>จำนวนวัน</th>
                            <th>จำนวนเงิน<br>(ค่าเบี้ยเลี้ยง)</th>
                            <th>ค่าทางด่วน</th>
                            <th>จำนวนเงินเดินทาง</th>
                            <th>Total</th>
                            <th>ชื่อบริษัท</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('jscustom')
    <script>
        $(document).ready(function() {

            var table = $('#reportTable').DataTable({
                processing: true,
                serverSide: true,
                paging: true,
                pageLength: 50,
                lengthMenu: [10, 25, 50, 100],
                info: true,
                searching: false,
                deferLoading: 0,

                ajax: {
                    url: "{{ route('Account.AllowanceReport.data') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.search_name = $('#search_name').val();
                        d.search_plant = $('#search_plant').val();
                        d.search_department = $('#search_department').val();
                        d.search_empid = $('#search_empid').val();
                    }
                },


                columns: [{
                        data: 'payment_date_display', // Object { @data: ID, display: TEXT }
                        name: 'id',
                        render: function(data, type, row) {
                            return data.display; //ให้ Datatables แสดงเฉพาะ 'display'
                        }
                    },
                    {
                        data: 'exid',
                        name: 'exid',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'vbooking_location',
                        name: 'vbooking_location',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'empid',
                        name: 'empid'
                    },
                    {
                        data: 'user_fullname',
                        name: 'user_fullname',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_dept',
                        name: 'user_dept',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_level',
                        name: 'user_level',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'departurefrom',
                        name: 'departurefrom'
                    },
                    {
                        data: 'returnfrom',
                        name: 'returnfrom'
                    },
                    {
                        data: 'day_count',
                        name: 'day_count',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'costoffood',
                        name: 'costoffood',
                        className: 'text-end',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'expresswaytoll',
                        name: 'expresswaytoll',
                        className: 'text-end',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'travel_cost',
                        name: 'travel_cost',
                        orderable: false,
                        searchable: false,
                        className: 'text-end'
                    },
                    {
                        data: 'totalprice',
                        name: 'totalprice',
                        className: 'text-end fw-bold',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_company',
                        name: 'user_company',
                        orderable: false,
                        searchable: false
                    }
                ],

                order: [
                    [0, 'desc']
                ],
                drawCallback: function(settings) {
                    var api = this.api();
                    var rows = api.rows({
                        page: 'current'
                    }).nodes();
                    var last = null; // ตัวแปรไว้เช็ค Group ID ล่าสุด
                    var currentClass = 'group-a'; // สีเริ่มต้น

                    // วน Loop ข้อมูลในคอลัมน์ 0 (payment_date_display)
                    api.column(0, {
                        page: 'current'
                    }).data().each(function(groupData, i) {

                        // groupData คือ Object { '@data': ID, 'display': TEXT }
                        var groupId = groupData['@data'];

                        // ถ้า Group ID ไม่เหมือนกับแถวที่แล้ว
                        if (last !== groupId) {
                            // สลับสี Class
                            currentClass = (currentClass === 'group-a' ? 'group-b' : 'group-a');
                            last = groupId; // อัปเดต ID ล่าสุด
                        }

                        // เพิ่ม Class (สี) ให้กับแถว <tr>
                        $(rows).eq(i).removeClass('group-a group-b').addClass(currentClass);
                    });
                }

            });

            $('#searchButton').on('click', function(e) {
                e.preventDefault();
                table.ajax.reload();
            });

            table.on('draw', function() {
                var hasData = table.rows({
                    search: 'applied'
                }).count() > 0;
                if (hasData) {
                    var params = new URLSearchParams($('#searchForm').serialize()).toString();
                    $('#exportExcel').attr('href', '{{ route('Account.AllowanceReport.exportExcel') }}?' +
                        params);
                    $('#exportPdf').attr('href', '{{ route('Account.AllowanceReport.exportPdf') }}?' +
                        params);
                    $('#exportButtons').show();
                } else {
                    $('#exportButtons').hide();
                }
            });

        });
    </script>
@endsection
@section('csscustom')
    {{-- เพิ่ม Style สำหรับสลับสีแถว  --}}
    <style>
        tr.group-a td {
            background-color: #f5f5f5;
            /* สีเทาอ่อน  */
        }

        tr.group-b td {
            background-color: #ffffff;
            /* สีขาว */
        }
    </style>
@endsection
