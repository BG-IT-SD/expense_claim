@extends('layouts.template')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
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

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('jscustom')
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/distancerate.js']) }}"></script>
@endsection