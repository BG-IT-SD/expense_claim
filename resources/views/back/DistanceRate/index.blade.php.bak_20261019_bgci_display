@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <table class="table text-center">
                    <thead>
                        <tr>
                            <th class="table-primary"></th>
                            @foreach($groupedPlants as $colIndex => $colPlant)
                                <th class="table-primary">{{ $colPlant->plantname ?? $colPlant->plantname }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedPlants as $rowPlant)
                        <tr>
                            <th class="table-primary">{{ $rowPlant->plantname }}</th>
                            @foreach($groupedPlants as $colPlant)
                                @php
                                    $value = null;

                                    if ($rowPlant->id != $colPlant->id && isset($matrixGrouped[$rowPlant->id][$colPlant->id])) {
                                        $value = $matrixGrouped[$rowPlant->id][$colPlant->id];
                                    } elseif ($rowPlant->id != $colPlant->id && isset($matrixGrouped[$colPlant->id][$rowPlant->id])) {
                                        $value = $matrixGrouped[$colPlant->id][$rowPlant->id];
                                    }
                                @endphp
                                <td>
                                    {{ $value !== null ? number_format($value, 0) : '' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="datatables-users table" id="DistanceRateTable">
                            <thead class="border-top table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>StartPlant</th>
                                    <th>EndPlant</th>
                                    <th>KM.</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rates as $rate)
                                    <tr>
                                        <td>{{ $rate->id }}</td>
                                        <td>{{ $rate->Startplant->plantname }}</td>
                                        <td>{{ $rate->Endplant->plantname }}</td>
                                        <td>{{ $rate->kilometer }}</td>
                                        <td>{!! $rate->status == 1
                                            ? '<span class="badge rounded-pill bg-success">Active</span>'
                                            : '<span class="badge rounded-pill bg-danger">Inactive</span>' !!}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm btngroupedit"
                                                onclick="window.location.href='{{ route('DistanceRate.edit', $rate->id) }}'"><i
                                                    class="mdi mdi-pencil-circle-outline"></i>
                                                edit</button> <button type="button"
                                                class="btn btn-danger btn-sm deleterate" data-id="{{ $rate->id }}"><i
                                                    class="mdi mdi-trash-can"></i></button>
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
         const RateDelUrl = "{{ route('DistanceRate.destroy', ':id') }}";
    </script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/distancerate.js']) }}"></script>
@endsection

@section('csscustom')

@endsection
