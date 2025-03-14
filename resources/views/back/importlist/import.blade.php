@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Groups Special/</span><span> Import Data</span></h4>
        <div class="app-ecommerce">
            <!-- Add Type Groups -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                <div class="d-flex flex-column justify-content-center">
                    <h4 class="mb-1 mt-3">Import Data</h4>
                </div>
                <div class="d-flex align-content-center flex-wrap gap-3">
                    <button type="button" class="btn btn-outline-secondary"
                        onclick="window.location.href='{{ url('ImportList') }}';">Discard</button>
                </div>

            </div>

            <div class="row">
                <!-- Main form-->
                <div class="col-12 col-lg-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <td>รหัสสำหรับนำเข้า</td>
                                        <td>ประเภท</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($typegroups as $typegroup)
                                        <tr>
                                            <td>{{ $typegroup->id }}</td>
                                            <td>{{ $typegroup->groupname }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-12">
                    <!-- Type Groups Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                        </div>
                        <div class="card-body">

                            @if (session('success'))
                             <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                            @endif
                            {{-- Display Validation Errors --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            {{-- Upload Form --}}
                            <form action="{{ route('importlist.excel') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Upload Excel File</label>
                                    <input type="file" name="file"
                                        class="form-control @error('file') is-invalid @enderror" required>
                                    @error('file')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mt-3 text-end">
                                    <a href="{{ route('download.sample') }}" class="btn btn-success"><span class="mdi mdi-file-excel"></span> ตัวอย่างไฟล์</a>
                                    <button type="submit" class="btn btn-primary"><span class="mdi mdi-import"></span> Import</button>

                                </div>

                            </form>

                            @if (session('importResults'))
                                <div class="alert alert-info mt-3">
                                    <h4>Import Results:</h4>
                                    <table class="table table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Typeid</th>
                                                <th>EmpID</th>
                                                <th>Name</th>
                                                <th>Position</th>
                                                <th>Status</th>
                                                <th>Message</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (session('importResults') as $index => $result)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $result['row']['typeid'] ?? 'N/A' }}</td>
                                                    <td>{{ $result['row']['empid'] ?? 'N/A' }}</td>
                                                    <td>{{ $result['row']['fullname'] ?? 'N/A' }}</td>
                                                    <td>{{ $result['row']['position'] ?? 'N/A' }}</td>
                                                    <td>
                                                        @if ($result['status'] == 'success')
                                                            <span class="badge bg-success">Success</span>
                                                        @else
                                                            <span class="badge bg-danger">Error</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($result['status'] == 'error')
                                                            {{ $result['message'] }}
                                                        @else
                                                            User imported successfully
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- /Type Groups Information -->
                </div>
                <!-- End Main form-->
            </div>
        </div>
    </div>
@endsection
