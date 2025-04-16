@extends('layouts.template')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-body">

                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <h4 class="alert-heading d-flex align-items-center">
                          <i class="mdi mdi-alert-circle-outline mdi-24px me-2"></i>Warning!!
                        </h4>
                        <p>
                            {{ $message }}
                        </p>
                        {{-- <hr> --}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection