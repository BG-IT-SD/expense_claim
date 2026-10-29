@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <h5 class="card-header"><i class="mdi mdi-view-list"></i> รายการกลุ่มอนุมัติ</h5>
                    <div class="table-responsive text-nowrap2">
                        <table class="table" id="grouplist">
                            <thead class="table-dark">
                                <tr>
                                    <th>EXGROUPID</th>
                                    <th>BU</th>
                                    <th>DATE</th>
                                    <th>ยอดรวม</th>
                                    <th>ค่าอาหาร</th>
                                    <th>ค่าน้ำมัน</th>
                                    <th>ค่าใช้จ่ายอื่นๆ</th>
                                    <th>Type Approve</th>
                                    <th>Approve</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach ($exgroups as $exgroup)
                                    <tr>
                                        <td>{{ $exgroup->id }}</td>
                                        <td>{{ $exgroup->plantname }}</td>
                                        <td>{{ \Carbon\Carbon::parse($exgroup->groupdate)->format('d/m/Y') }}</td>
                                        <td>{{ isset($exgroup->nettotal)  ? number_format($exgroup->nettotal, 2) : number_format($exgroup->total, 2) }}</td>
                                        <td>{{ isset($exgroup->nettotal)  ? number_format($exgroup->nettotalfood, 2) : number_format($exgroup->totalfood, 2) }}</td>
                                        <td>{{ isset($exgroup->nettotal)  ? number_format($exgroup->nettotalfuel, 2) : number_format($exgroup->totalfuel, 2)}}</td>
                                        <td>{{ isset($exgroup->nettotal)  ? number_format($exgroup->nettotalother, 2) : number_format($exgroup->totalother, 2) }}</td>
                                        <td>
                                            @if (!is_null($exgroup->typeapprove))
                                                {!! type_approve_text($exgroup->typeapprove, $exgroup->typeapprove) !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if (!is_null($exgroup->statusapprove))
                                                {!! hr_status_approve_badge($exgroup->statusapprove, $exgroup->typeapprove) !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($exgroup->typeapprove == 6 && $exgroup->statusapprove == 1)
                                                <a href="{{ route('HR.export.group.pdf', $exgroup->id) }}"
                                                    class="btn btn-sm btn-danger" target="_blank">
                                                    <span class="mdi mdi-file-pdf-box"></span>
                                                    {{-- PDF --}}
                                                </a>
                                                <a href="{{ route('HR.export.group.excel', $exgroup->id) }}"
                                                    class="btn btn-sm btn-success" target="_blank">
                                                    <span class="mdi mdi-file-excel"></span>
                                                    {{-- Excel --}}
                                                </a>
                                            @endif

                                            @php
                                                $token = optional($exgroup->approve->first())->login_token;
                                            @endphp

                                            <a href="https://ec.bgonlineapp.com/approve/login?token={{ $token }}"
                                            target="_blank"
                                            class="btn btn-sm btn-primary">
                                            <span class="mdi mdi-list-box"></span>
                                            </a>
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

