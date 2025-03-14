@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-datatable table-responsive pt-0">

                        <table class="datatables-basic table table-bordered" id="FuelPriceTable">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Startrate</th>
                                    <th>Endrate</th>
                                    <th>Bath / km.</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fuelprices as $fuelprice)
                                    <tr>
                                        <td>{{ $fuelprice->id }}</td>
                                        <td>{{ $fuelprice->startrate }}</td>
                                        <td>{{ $fuelprice->endrate }}</td>
                                        <td>{{ $fuelprice->bathperkm }}</td>
                                        <td>{!! $fuelprice->status == 1
                                            ? '<span class="badge rounded-pill bg-success">Active</span>'
                                            : '<span class="badge rounded-pill bg-danger">Inactive</span>' !!}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm btngroupedit" onclick="window.location.href='{{ route('FuelPrice.edit', $fuelprice->id) }}'"><i class="mdi mdi-pencil-circle-outline"></i>
                                                edit</button> <button type="button"
                                                class="btn btn-danger btn-sm deletefuel" data-id="{{ $fuelprice->id }}"><i
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
    {{-- {{ dd(session()->all()) }} --}}
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
        // const FuelpriceStoreUrl = "{{ route('FuelPrice91.store') }}";
        const FuelpriceDelUrl = "{{ route('FuelPrice.destroy', ':id') }}";
    </script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/fuelprice.js']) }}"></script>
@endsection
