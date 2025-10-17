@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        {{-- <h5 class="mb-0"><span class="mdi mdi-file-search-outline"></span> ค้นหาข้อมูล</h5> --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-check-circle me-1"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- ถ้ามี error จาก validate --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>พบข้อผิดพลาด:</strong>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                    </div>
                    <div class="card-body">

                        <form action="{{ route('MessageAlert.update', $message->id) }}" method="POST"
                            id="frmUpdateMessage">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card mb-4">
                                                <h5 class="card-header">Message Alert</h5>
                                                <div class="card-body">
                                                    <!-- Toolbar -->
                                                    <div id="snow-toolbar">
                                                        <span class="ql-formats">
                                                            <select class="ql-font"></select>
                                                            <select class="ql-size"></select>
                                                        </span>
                                                        <span class="ql-formats">
                                                            <button class="ql-bold"></button>
                                                            <button class="ql-italic"></button>
                                                            <button class="ql-underline"></button>
                                                            <button class="ql-strike"></button>
                                                        </span>
                                                        <span class="ql-formats">
                                                            <select class="ql-color"></select>
                                                            <select class="ql-background"></select>
                                                        </span>
                                                        <span class="ql-formats">
                                                            <button class="ql-script" value="sub"></button>
                                                            <button class="ql-script" value="super"></button>
                                                        </span>
                                                        <span class="ql-formats">
                                                            <button class="ql-header" value="1"></button>
                                                            <button class="ql-header" value="2"></button>
                                                            <button class="ql-blockquote"></button>
                                                            <button class="ql-code-block"></button>
                                                        </span>
                                                    </div>

                                                    <!-- Quill Editor -->
                                                    <div id="message-editor">
                                                        {{-- {{ $message->message }} --}}
                                                    </div>

                                                    <!-- Hidden input for saving -->
                                                    <input type="hidden" name="message" id="message">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <span class="mdi mdi-import"></span> Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('jsvendor')
    <script src="{{ asset('template/assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/quill/quill.js') }}"></script>
@endsection
@section('jscustom')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quill = new Quill('#message-editor', {
                theme: 'snow',
                modules: {
                    toolbar: '#snow-toolbar'
                }
            });

            // ✅ โหลดค่าที่บันทึกไว้จาก Laravel (Base64 หรือ HTML เดิม)
            const savedData = @json($message->message ?? '');

            // ✅ ฟังก์ชันตรวจว่า string เป็น Base64 จริงหรือไม่
            function isBase64(str) {
                if (!str || str.trim() === '') return false;
                // ถ้ามีอักขระที่ไม่ควรอยู่ใน Base64 ก็ return false
                const notBase64 = /[^A-Z0-9+\/=]/i;
                if (notBase64.test(str)) return false;

                try {
                    // ถอดกลับแล้วเข้ารหัสใหม่ต้องได้ค่าเท่ากัน
                    return btoa(atob(str)) === str;
                } catch {
                    return false;
                }
            }

            // ✅ ถ้าเป็น Base64 → decode แล้วใส่ Quill
            if (isBase64(savedData)) {
                try {
                    const decoded = atob(savedData);
                    quill.clipboard.dangerouslyPasteHTML(decoded);
                } catch (err) {
                    console.error('❌ Decode Base64 error:', err);
                }
            } else {
                // ✅ ถ้าไม่ใช่ Base64 → ถือว่าเป็น HTML เดิม
                quill.clipboard.dangerouslyPasteHTML(savedData);
            }

            const form = document.getElementById('frmUpdateMessage');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                let html = quill.root.innerHTML.trim();
                const textOnly = quill.getText().trim();

                // ✅ ถ้าไม่มีข้อความเลย
                if (!textOnly) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'กรุณากรอกข้อความก่อนบันทึก',
                        confirmButtonText: 'ตกลง'
                    });
                    return;
                }

                // ✅ Preview ให้ผู้ใช้ตรวจสอบก่อนบันทึก
                Swal.fire({
                    title: 'ตรวจสอบข้อความก่อนบันทึก',
                    html: `<div style="text-align:left;max-height:400px;overflow:auto;">${html}</div>`,
                    showCancelButton: true,
                    confirmButtonText: 'บันทึก',
                    cancelButtonText: 'ยกเลิก',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ✅ เข้ารหัส Base64 ก่อนส่งกลับ
                        const encoded = btoa(unescape(encodeURIComponent(html)));
                        document.getElementById('message').value = encoded;
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection

@section('csscustom')
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/quill/editor.css') }}" />
@endsection
