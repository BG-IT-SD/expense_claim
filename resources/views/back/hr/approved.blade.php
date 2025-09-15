@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12 mb-4">

                <div class="card p-3">
                    <h5 class="card-header"><i class="mdi mdi-view-list"></i> รายการเบิกที่ตรวจสอบแล้ว</h5>
                    <div class="row">
                        <div class="col-md-6 mt-3 mb-3">
                            <input type="hidden" id="selectedPlantName" name="plant_name" value="">
                            <input type="hidden" id="plantID" name="plant_id" value="">
                        </div>
                        <div class="col-md-6 mt-3 mb-3 text-end"> <button type="button" class="btn btn-primary"
                                id="sendSelected">
                                <i class="mdi mdi-content-save"></i> ยืนยันข้อมูลเพื่ออนุมัติ
                            </button></div>
                    </div>

                    {{-- Newtabs --}}
                    <div class="card mb-4">
                        <div class="card-header p-0">
                            <div class="nav-align-top">
                                <ul class="nav nav-tabs" role="tablist">
                                    {{-- Head Tabs active --}}
                                    @foreach ($plantNames as $plant)
                                        <li class="nav-item" role="presentation">
                                            <button type="button"
                                                class="nav-link  {{ $loop->first ? 'active' : '' }} waves-effect"
                                                role="tab" data-bs-toggle="tab"
                                                data-bs-target="#plant_{{ $plant['id'] }}"
                                                aria-controls="{{ $plant['id'] }}" data-plantname="{{ $plant['name'] }}"
                                                data-plantid="{{ $plant['id'] }}"
                                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                {{ $plant['name'] }}
                                            </button>
                                        </li>
                                    @endforeach

                                    {{-- Head Tabs --}}
                                    <span class="tab-slider" style="left: 0px; width: 91.4062px; bottom: 0px;"></span>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content p-0">
                                {{-- content Tabs show active --}}
                                @foreach ($plantNames as $plant)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                        id="plant_{{ $plant['id'] }}" role="tabpanel">

                                        {{-- <input type="text" class="current-plant" name="current_plant[]" value="{{ $plant['id'] }}">
                                        <input type="text" class="current-plant-name" name="current_plant_name[]" value="{{ $plant['name'] }}"> --}}
                                        {{-- Tables --}}

                                        <div class="table-responsive text-nowrap2">
                                            <table class="table appex" id="appex-{{ $plant['id'] }}">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th><input type="checkbox" class="selectAll"
                                                                data-plant="plant_{{ $plant['id'] }}" /></th>
                                                        <th>Expense ID</th>
                                                        <th>Date Time</th>
                                                        <th>Booking ID</th>
                                                        <th>ID | Name </th>
                                                        <th>BU</th>
                                                        <th>Location</th>
                                                        <th>รวมเบิก</th>
                                                        <th>Type Approve</th>
                                                        <th>Approve</th>
                                                        {{-- <th>Approve Name</th> --}}
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table-border-bottom-0">
                                                    @foreach ($expenses as $key => $expense)
                                                        @if (BuEmp($expense->empid) == $plant['name'])
                                                            <tr>
                                                                <td>
                                                                    <input type="checkbox" name="expense_ids[]"
                                                                        class="expense-checkbox"
                                                                        data-plant="plant_{{ $plant['id'] }}"
                                                                        value="{{ $expense->id }}">
                                                                </td>
                                                                <td>{{ $expense->prefix . $expense->id }}</td>
                                                                <td class="text-wrap">
                                                                    {{ \Carbon\Carbon::parse($expense->vbooking->departure_date . ' ' . $expense->vbooking->departure_time)->format(
                                                                        'd/m/Y H:i',
                                                                    ) .
                                                                        ' - ' .
                                                                        \Carbon\Carbon::parse($expense->vbooking->return_date . ' ' . $expense->vbooking->return_time)->format('d/m/Y H:i') }}
                                                                </td>
                                                                <td>{{ $expense->bookid }}</td>
                                                                <td class="text-wrap">
                                                                    @if ($expense->extype == 2 || $expense->extype == 3)
                                                                        {{ $expense->empid . ' | ' . $expense->tech->fullname . ' | ' }}
                                                                    @else
                                                                        {{ $expense->empid . ' | ' . $expense->user->fullname }}
                                                                    @endif

                                                                </td>
                                                                <td class="text-wrap">
                                                                    {{ BuEmp($expense->empid) }}
                                                                </td>
                                                                <td>{{ $expense->vbooking->locationbu }}</td>
                                                                <td>{{ $expense->totalprice ?? 0 }}</td>
                                                                <td>
                                                                    @if (!is_null($expense->latestApprove->typeapprove))
                                                                        {!! type_approve_text($expense->latestApprove->typeapprove, $expense->latestApprove->typeapprove) !!}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if (!is_null($expense->latestApprove->statusapprove))
                                                                        {!! hr_status_approve_badge($expense->latestApprove->statusapprove, $expense->latestApprove->typeapprove) !!}
                                                                        {{-- {{ $expense->latestApprove->statusapprove.'type=>'.$expense->latestApprove->typeapprove }} --}}
                                                                    @endif
                                                                </td>
                                                                {{-- <td >
                                                            @if ($expense->latestApprove->statusapprove >= 3)
                                                                {{ $expense->$latestApprove->approvename }}
                                                            @endif
                                                        </td> --}}
                                                                <td class="text-nowrap">
                                                                    @if ($expense->latestApprove->statusapprove == 2)
                                                                        @if ($expense->extype == 2)
                                                                            <a href="{{ route($page, ['id' => $expense->id, 'type' => 0]) }}"
                                                                                target="_blank" class="btn btn-sm btn-info">
                                                                                <span
                                                                                    class="mdi mdi-eye-arrow-right-outline"></span>
                                                                                View
                                                                            </a>
                                                                        @else
                                                                            <a href="{{ route('HR.view', ['id' => $expense->id, 'type' => '0']) }}"
                                                                                target="_blank"
                                                                                class="btn btn-sm btn-info"><span
                                                                                    class="mdi mdi-eye-arrow-right-outline"></span>
                                                                                View</a>
                                                                        @endif
                                                                    @else
                                                                        @if ($expense->extype == 2)
                                                                            <a href="{{ route($page, ['id' => $expense->id, 'type' => 0]) }}"
                                                                                target="_blank" class="btn btn-sm btn-info">
                                                                                <span
                                                                                    class="mdi mdi-eye-arrow-right-outline"></span>
                                                                                View
                                                                            </a>
                                                                        @else
                                                                            <a href="{{ route('HR.view', ['id' => $expense->id, 'type' => '0']) }}"
                                                                                target="_blank"
                                                                                class="btn btn-sm btn-info"><span
                                                                                    class="mdi mdi-eye-arrow-right-outline"></span>
                                                                                View</a>
                                                                            <a href="{{ route('HR.aftedit', $expense->id) }}"

                                                                                class="btn btn-sm btn-warning"><span
                                                                                    class="mdi mdi-edit"></span> Edit</a>
                                                                        @endif
                                                                    @endif

                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        {{-- Tables --}}
                                    </div>
                                @endforeach
                                {{-- content Tabs --}}

                            </div>
                        </div>
                    </div>
                    {{-- Newtabs --}}


                </div>
            </div>
        </div>
    </div>

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
        const hrNextApproveUrl = "{{ route('HR.hrnextapprove') }}";
    </script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/hr/approve.js']) }}"></script>
@endsection
