@extends('layouts.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header {{ isset($item) ? 'bg-warning' : 'bg-success' }} text-white">
                    <h5 class="mb-0">
                        <i class="mdi {{ isset($item) ? 'mdi-pencil-circle-outline' : 'mdi-account-plus-outline' }}"></i>
                        {{ isset($item) ? 'แก้ไขรายชื่ออนุมัติพิเศษ' : 'เพิ่มรายชื่ออนุมัติพิเศษ' }}
                    </h5>
                </div>

                <div class="card-body">
                    <form
                        action="{{ isset($item) ? route('SpecialApprove.update', $item->id) : route('SpecialApprove.store') }}"
                        method="POST"
                    >
                        @csrf
                        @if(isset($item)) @method('PUT') @endif

                        <div class="mb-3">
                            <label class="form-label">Emp ID <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="empid"
                                class="form-control"
                                value="{{ old('empid', $item->empid ?? '') }}"
                                required
                            >
                            @error('empid') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="fullname"
                                class="form-control"
                                value="{{ old('fullname', $item->fullname ?? '') }}"
                                required
                            >
                            @error('fullname') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email', $item->email ?? '') }}"
                                required
                            >
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- เฉพาะตอนแก้ไข --}}
                        @if(isset($item))
                        <div class="mb-3">
                            <label class="form-label">สถานะ</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ $item->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $item->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        @endif

                        <div class="text-end">
                            <a href="{{ route('SpecialApprove.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> กลับ
                            </a>
                            <button type="submit" class="btn {{ isset($item) ? 'btn-warning' : 'btn-success' }}">
                                <i class="mdi mdi-content-save-outline"></i>
                                {{ isset($item) ? 'อัปเดต' : 'บันทึก' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
