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

            // โหลดค่าที่บันทึกไว้
            const saved = @json($message->message ?? '');
            if (saved) {
                // วิธีที่ง่ายสุดและตรงจุด
                quill.clipboard.dangerouslyPasteHTML(saved);

                // หรือทางเลือกที่ปลอดภัยกว่า (แปลงเป็น Delta ก่อน)
                // quill.setContents(quill.clipboard.convert(saved));
            }

            // ก่อน submit: ดึง HTML จาก Quill ลง hidden input
            document.getElementById('frmUpdateMessage').addEventListener('submit', function() {
                // ถ้าใช้ Quill v2 มี getSemanticHTML() จะสวยกว่า
                // document.getElementById('message').value = quill.getSemanticHTML();
                document.getElementById('message').value = quill.root.innerHTML;
            });
        });
    </script>
@endsection

@section('csscustom')
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/quill/editor.css') }}" />
@endsection
