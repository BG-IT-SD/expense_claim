@extends('layouts.login.templatelogin')

@section('contentlogin')
  <div class="authentication-inner py-4">
    <div class="card p-2">
      <div class="card-body mt-2">
        <h4 class="mb-3 text-center">ลืมรหัสผ่าน</h4>
        <p class="text-muted text-center mb-4">
          ยืนยันตัวตนด้วยรหัสพนักงานและเลขประจำตัวประชาชน
        </p>

        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        @if (session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form id="formForgot" class="mb-3" method="POST"
              action="{{ route('password.verify-employee') }}" autocomplete="off" novalidate>
          @csrf

          <div class="form-floating form-floating-outline mb-3">
            <input type="text" class="form-control" id="empid" name="empid"
                   placeholder="เช่น 63000593" inputmode="numeric" maxlength="12"
                   value="{{ old('empid') }}" required>
            <label for="empid">รหัสพนักงาน</label>
          </div>

          <div class="form-floating form-floating-outline mb-1">
            <input type="password" class="form-control" id="cid" name="cid"
                   placeholder="13 หลัก" inputmode="numeric" maxlength="13" required>
            <label for="cid">เลขประจำตัวประชาชน</label>
          </div>
          <small class="text-muted d-block mb-3">
            ข้อมูลนี้ใช้เพื่อยืนยันตัวตนเท่านั้น
          </small>

          <button type="submit" class="btn btn-primary d-grid w-100">ดำเนินการต่อ</button>

          <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-primary">กลับไปหน้าเข้าสู่ระบบ</a>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
@section('jslogin')
  <script>
    (function () {
      const form = document.getElementById('formForgot');
      const emp  = document.getElementById('empid');
      const cid  = document.getElementById('cid');

      function validThaiCID(c) {
        if (!/^\d{13}$/.test(c)) return false;
        let sum = 0; for (let i = 0; i < 12; i++) sum += (+c[i]) * (13 - i);
        return ((11 - (sum % 11)) % 10) === +c[12];
      }

      form.addEventListener('submit', function (e) {
        const okEmp = /^\d{4,12}$/.test(emp.value.trim());
        const okCid = validThaiCID(cid.value.trim());
        if (!okEmp || !okCid) {
          e.preventDefault();
          alert('กรุณาตรวจสอบรหัสพนักงานหรือเลขประจำตัวประชาชนให้ถูกต้อง');
        }
      });
    })();
  </script>
@endsection
