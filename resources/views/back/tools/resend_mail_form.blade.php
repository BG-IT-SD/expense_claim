@extends('layouts.template')

@section('csscustom')
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-1">
                <i class="mdi mdi-email-sync-outline me-1"></i> ส่งอีเมลอนุมัติซ้ำ
            </h5>
            <p class="text-muted mb-0">เลือกรายการที่อยู่ระหว่างรออนุมัติ ข้อมูลผู้รับและลิงก์จะอ้างอิงจากระบบ</p>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success" role="alert">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('tools.resendMail.send') }}">
                @csrf

                <div class="mb-4">
                    <label for="approve_id" class="form-label">รายการรออนุมัติ</label>
                    <select id="approve_id" name="approve_id"
                        class="form-select @error('approve_id') is-invalid @enderror" required>
                        <option value="">เลือกเลขที่เอกสาร ผู้ขอ หรือผู้อนุมัติ</option>
                        @foreach ($approvals as $approval)
                            <option value="{{ $approval['id'] }}"
                                data-document="{{ $approval['document'] }}"
                                data-requester="{{ $approval['requester'] }}"
                                data-approver="{{ $approval['approver'] }}"
                                data-email="{{ $approval['email'] }}"
                                data-preview-url="{{ $approval['preview_url'] }}"
                                @selected((string) old('approve_id') === (string) $approval['id'])>
                                {{ $approval['document'] }} | ผู้ขอ: {{ $approval['requester'] }} | ผู้อนุมัติ: {{ $approval['approver'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="approval-detail" class="border rounded p-3 mb-4 d-none">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block">เลขที่เอกสาร</small>
                            <span id="detail-document" class="fw-medium"></span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">ผู้ขอ</small>
                            <span id="detail-requester" class="fw-medium"></span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">ส่งถึง</small>
                            <span id="detail-recipient" class="fw-medium"></span>
                        </div>
                    </div>
                </div>

                <div id="email-preview" class="mb-4 d-none">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label mb-0">ตัวอย่างอีเมล</label>
                        <span class="text-muted small">ตัวอย่างจากข้อมูลปัจจุบันในระบบ</span>
                    </div>
                    <iframe id="email-preview-frame" class="w-100 border rounded"
                        style="height: 560px; pointer-events: none;" sandbox
                        title="ตัวอย่างอีเมลอนุมัติ"></iframe>
                </div>

                @if ($approvals->isEmpty())
                    <div class="alert alert-info mb-0" role="alert">ไม่พบรายการที่อยู่ระหว่างรออนุมัติ</div>
                @else
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-send me-1"></i> ส่งอีเมลซ้ำ
                        </button>
                    </div>
                @endif
            </form>
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
        const $select = $('#approve_id');

        $select.select2({
            placeholder: 'เลือกเลขที่เอกสาร ผู้ขอ หรือผู้อนุมัติ',
            allowClear: true,
            width: '100%'
        });

        function updateDetail() {
            const option = $select.find(':selected');
            const hasSelection = Boolean(option.val());

            $('#approval-detail').toggleClass('d-none', !hasSelection);
            $('#detail-document').text(option.data('document') || '-');
            $('#detail-requester').text(option.data('requester') || '-');
            $('#detail-recipient').text(hasSelection
                ? (option.data('approver') + ' (' + option.data('email') + ')')
                : '-');

            $('#email-preview').toggleClass('d-none', !hasSelection);
            $('#email-preview-frame').attr('src', hasSelection ? option.data('preview-url') : 'about:blank');
        }

        $select.on('change', updateDetail);
        updateDetail();
    });
</script>
@endsection
