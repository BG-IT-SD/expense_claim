@extends('layouts.template')

@section('csscustom')
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('content')
@php
    $activeTab = old('form_type') === 'assignment' ? 'assignment' : 'approvers';
    $groupData = $groups->mapWithKeys(function ($group) {
        return [(string) $group['group'] => [
            'step1' => $group['step1'] ? [
                'id' => (string) $group['step1']->empid,
                'text' => $group['step1']->empid.' | '.$group['step1']->fullname.' | '.$group['step1']->email,
            ] : null,
            'step2' => $group['step2'] ? [
                'id' => (string) $group['step2']->empid,
                'text' => $group['step2']->empid.' | '.$group['step2']->fullname.' | '.$group['step2']->email,
            ] : null,
        ]];
    });
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="mb-1"><i class="mdi mdi-account-cog-outline me-1"></i> กลุ่มอนุมัติช่าง</h5>
            <p class="text-muted mb-0">จัดการผู้อนุมัติและกำหนดกลุ่มให้พนักงานประเภทช่าง</p>
        </div>

        <div class="card-body pt-3">
            @if (session('success'))
                <div class="alert alert-success" role="alert">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <div class="fw-medium mb-1">กรุณาตรวจสอบข้อมูล</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'approvers' ? 'active' : '' }}" data-bs-toggle="tab"
                        data-bs-target="#approvers-tab" type="button" role="tab">
                        <i class="mdi mdi-account-supervisor-outline me-1"></i> สายอนุมัติ
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'assignment' ? 'active' : '' }}" data-bs-toggle="tab"
                        data-bs-target="#assignment-tab" type="button" role="tab">
                        <i class="mdi mdi-account-multiple-check-outline me-1"></i> พนักงานช่าง
                        @if ($unassignedCount > 0)
                            <span class="badge bg-danger ms-1">{{ $unassignedCount }}</span>
                        @endif
                    </button>
                </li>
            </ul>

            <div class="tab-content px-0 pb-0">
                <div class="tab-pane fade {{ $activeTab === 'approvers' ? 'show active' : '' }}" id="approvers-tab" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="mb-0">สายอนุมัติที่ใช้งานอยู่</h6>
                                <span class="text-muted small">{{ $groups->count() }} กลุ่ม</span>
                            </div>
                            <div class="table-responsive border rounded">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>กลุ่ม</th>
                                            <th>Step 1 ผู้จัดการส่วน</th>
                                            <th>Step 2 ผู้จัดการฝ่าย</th>
                                            <th class="text-center">พนักงาน</th>
                                            <th class="text-center">สถานะ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($groups as $group)
                                            <tr class="approval-group-row" data-group="{{ $group['group'] }}" role="button">
                                                <td class="fw-medium">กลุ่ม {{ $group['group'] }}</td>
                                                <td>
                                                    @if ($group['step1'])
                                                        <div>{{ $group['step1']->fullname }}</div>
                                                        <small class="text-muted">{{ $group['step1']->empid }}</small>
                                                    @else
                                                        <span class="text-danger">ยังไม่กำหนด</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($group['step2'])
                                                        <div>{{ $group['step2']->fullname }}</div>
                                                        <small class="text-muted">{{ $group['step2']->empid }}</small>
                                                    @else
                                                        <span class="text-danger">ยังไม่กำหนด</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $assignedCounts->get($group['group'], 0) }}</td>
                                                <td class="text-center">
                                                    @if ($group['step1'] && $group['step2'])
                                                        <span class="badge bg-success">พร้อมใช้งาน</span>
                                                    @else
                                                        <span class="badge bg-warning">ข้อมูลไม่ครบ</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center text-muted py-4">ยังไม่มีสายอนุมัติช่าง</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-5 border-start-lg">
                            <h6 class="mb-3">เพิ่มหรือแก้ไขสายอนุมัติ</h6>
                            <form method="POST" action="{{ route('TechApprove.approvers.save') }}">
                                @csrf
                                <input type="hidden" name="form_type" value="approvers">

                                <div class="mb-3">
                                    <label class="form-label" for="approval-group-select">กลุ่ม</label>
                                    <select class="form-select" id="approval-group-select" name="group">
                                        <option value="">สร้างกลุ่มใหม่อัตโนมัติ</option>
                                        @foreach ($groups as $group)
                                            <option value="{{ $group['group'] }}" @selected((string) old('group') === (string) $group['group'])>
                                                กลุ่ม {{ $group['group'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="step1-employee">Step 1 ผู้จัดการส่วน</label>
                                    <select class="form-select employee-select" id="step1-employee" name="step1_empid" required></select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="step2-employee">Step 2 ผู้จัดการฝ่าย</label>
                                    <select class="form-select employee-select" id="step2-employee" name="step2_empid" required></select>
                                </div>

                                <div class="text-end">
                                    <button type="button" class="btn btn-outline-secondary" id="reset-approval-form">
                                        <i class="mdi mdi-plus me-1"></i> กลุ่มใหม่
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save me-1"></i> บันทึกสายอนุมัติ
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade {{ $activeTab === 'assignment' ? 'show active' : '' }}" id="assignment-tab" role="tabpanel">
                    <form method="POST" action="{{ route('TechApprove.technicians.assign') }}">
                        @csrf
                        <input type="hidden" name="form_type" value="assignment">

                        <div class="row g-3 align-items-end mb-3">
                            <div class="col-md-5">
                                <label class="form-label" for="technician-search">ค้นหาพนักงานช่าง</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                    <input type="search" id="technician-search" class="form-control" placeholder="รหัส ชื่อ BU หรือแผนก">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="assignment-filter">สถานะ</label>
                                <select id="assignment-filter" class="form-select">
                                    <option value="all">ทั้งหมด</option>
                                    <option value="unassigned">ยังไม่กำหนดกลุ่ม</option>
                                    <option value="assigned">กำหนดแล้ว</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="assignment-group">กำหนดเป็นกลุ่ม</label>
                                <select id="assignment-group" name="group" class="form-select" required>
                                    <option value="">เลือกกลุ่มอนุมัติ</option>
                                    @foreach ($completeGroups as $group)
                                        <option value="{{ $group['group'] }}" @selected((string) old('group') === (string) $group['group'])>
                                            กลุ่ม {{ $group['group'] }} — {{ $group['step1']->fullname }} / {{ $group['step2']->fullname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive border rounded">
                            <table class="table table-hover align-middle mb-0" id="technician-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 48px;" class="text-center">
                                            <input class="form-check-input" type="checkbox" id="select-all-technicians" aria-label="เลือกพนักงานทั้งหมด">
                                        </th>
                                        <th>รหัสพนักงาน</th>
                                        <th>ชื่อพนักงาน</th>
                                        <th>BU</th>
                                        <th>แผนก/ตำแหน่ง</th>
                                        <th>กลุ่มปัจจุบัน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($technicians as $technician)
                                        @php $isAssigned = !empty($technician->groupapprove); @endphp
                                        <tr data-assignment="{{ $isAssigned ? 'assigned' : 'unassigned' }}"
                                            data-search="{{ strtolower($technician->empid.' '.$technician->fullname.' '.$technician->bu.' '.$technician->dept.' '.$technician->position) }}">
                                            <td class="text-center">
                                                <input class="form-check-input technician-checkbox" type="checkbox"
                                                    name="technician_ids[]" value="{{ $technician->id }}"
                                                    @checked(in_array($technician->id, old('technician_ids', [])))>
                                            </td>
                                            <td>{{ $technician->empid }}</td>
                                            <td class="fw-medium">{{ $technician->fullname }}</td>
                                            <td>{{ $technician->bu ?: '-' }}</td>
                                            <td>
                                                <div>{{ $technician->dept ?: '-' }}</div>
                                                <small class="text-muted">{{ $technician->position ?: '-' }}</small>
                                            </td>
                                            <td>
                                                @if ($isAssigned)
                                                    <span class="badge bg-label-primary">กลุ่ม {{ $technician->groupapprove }}</span>
                                                @else
                                                    <span class="badge bg-label-danger">ยังไม่กำหนด</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-4">ไม่พบพนักงานประเภทช่าง</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <span class="text-muted"><span id="selected-count">0</span> คนที่เลือก</span>
                            <button type="submit" class="btn btn-primary" @disabled($completeGroups->isEmpty())>
                                <i class="mdi mdi-account-multiple-check-outline me-1"></i> บันทึกกลุ่มพนักงาน
                            </button>
                        </div>
                        @if ($completeGroups->isEmpty())
                            <div class="alert alert-warning mt-3 mb-0">กรุณาสร้างสายอนุมัติที่มี Step 1 และ Step 2 ครบก่อนกำหนดกลุ่มพนักงาน</div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('jsvendor')
    <script src="{{ asset('template/assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('jscustom')
<script>
    $(function () {
        const groupData = @json($groupData);
        const employeeSearchUrl = @json(route('TechApprove.employees.search'));
        const oldStep1 = @json(old('step1_empid'));
        const oldStep2 = @json(old('step2_empid'));

        $('.employee-select').each(function () {
            $(this).select2({
                width: '100%',
                placeholder: 'ค้นหาจากรหัส ชื่อ หรืออีเมล',
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    url: employeeSearchUrl,
                    dataType: 'json',
                    delay: 300,
                    data: params => ({ q: params.term || '', page: params.page || 1 }),
                    processResults: data => data
                }
            });
        });

        function setEmployee($select, employee) {
            $select.empty();
            if (employee) {
                $select.append(new Option(employee.text, employee.id, true, true));
            }
            $select.trigger('change');
        }

        function loadGroup(group) {
            const data = groupData[String(group)] || {};
            setEmployee($('#step1-employee'), data.step1 || null);
            setEmployee($('#step2-employee'), data.step2 || null);
        }

        $('#approval-group-select').on('change', function () {
            loadGroup($(this).val());
        });

        $('.approval-group-row').on('click', function () {
            const group = String($(this).data('group'));
            $('#approval-group-select').val(group).trigger('change');
        });

        $('#reset-approval-form').on('click', function () {
            $('#approval-group-select').val('').trigger('change');
        });

        if ($('#approval-group-select').val()) {
            loadGroup($('#approval-group-select').val());
        }

        function restoreOldEmployee($select, value) {
            if (!value) return;
            $.getJSON(employeeSearchUrl, { q: value, page: 1 }).done(function (data) {
                const employee = (data.results || []).find(item => String(item.id) === String(value));
                if (employee) setEmployee($select, employee);
            });
        }

        restoreOldEmployee($('#step1-employee'), oldStep1);
        restoreOldEmployee($('#step2-employee'), oldStep2);

        function filterTechnicians() {
            const keyword = $('#technician-search').val().toLowerCase().trim();
            const assignment = $('#assignment-filter').val();

            $('#technician-table tbody tr[data-assignment]').each(function () {
                const matchesKeyword = !keyword || String($(this).data('search')).includes(keyword);
                const matchesAssignment = assignment === 'all' || $(this).data('assignment') === assignment;
                $(this).toggle(matchesKeyword && matchesAssignment);
            });
        }

        function updateSelectedCount() {
            $('#selected-count').text($('.technician-checkbox:checked').length);
        }

        $('#technician-search').on('input', filterTechnicians);
        $('#assignment-filter').on('change', filterTechnicians);
        $('#select-all-technicians').on('change', function () {
            const checked = this.checked;
            $('#technician-table tbody tr:visible .technician-checkbox').prop('checked', checked);
            updateSelectedCount();
        });
        $('.technician-checkbox').on('change', updateSelectedCount);
        updateSelectedCount();
    });
</script>
@endsection
