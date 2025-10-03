@extends('layouts.login.templatelogin')

@section('contentlogin')
  <div class="authentication-inner py-4">
    <div class="card p-2">
      <div class="card-body mt-2">
        <h4 class="mb-3 text-center">ตั้งรหัสผ่านใหม่</h4>
        <p class="text-muted text-center mb-4">
          กรุณากำหนดรหัสผ่านใหม่สำหรับรหัสพนักงาน
          @isset($empid) <strong>{{ $empid }}</strong> @endisset
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
        @if (session('status'))
          <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form id="formReset" class="mb-3" method="POST" action="{{ route('password.reset-employee') }}" novalidate>
          @csrf
          <input type="hidden" name="empid" value="{{ $empid ?? old('empid') }}">
          @isset($token) <input type="hidden" name="token" value="{{ $token }}"> @endisset

          {{-- Password --}}
          <div class="mb-3 form-password-toggle">
            <div class="input-group input-group-merge">
              <div class="form-floating form-floating-outline">
                <input
                  type="password"
                  id="password"
                  name="password"
                  class="form-control"
                  placeholder="New password"
                  minlength="8"
                  pattern="(?=.*[A-Z])(?=.*\d).{8,}"
                  autocomplete="new-password"
                  required
                >
                <label for="password">รหัสผ่านใหม่</label>
              </div>
              <button
                type="button"
                class="input-group-text password-toggle"
                data-target="#password"
                aria-label="แสดงหรือซ่อนรหัสผ่าน"
                aria-pressed="false"
              >
                <i class="mdi mdi-eye-off-outline"></i>
              </button>
            </div>
            <small class="text-muted d-block mt-1">
              อย่างน้อย 8 ตัวอักษร และต้องมี <strong>ตัวพิมพ์ใหญ่</strong> และ <strong>ตัวเลข</strong>
            </small>
            @error('password') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
          </div>

          {{-- Confirm --}}
          <div class="mb-3 form-password-toggle">
            <div class="input-group input-group-merge">
              <div class="form-floating form-floating-outline">
                <input
                  type="password"
                  id="password_confirmation"
                  name="password_confirmation"
                  class="form-control"
                  placeholder="Confirm password"
                  autocomplete="new-password"
                  required
                >
                <label for="password_confirmation">ยืนยันรหัสผ่าน</label>
              </div>
              <button
                type="button"
                class="input-group-text password-toggle"
                data-target="#password_confirmation"
                aria-label="แสดงหรือซ่อนยืนยันรหัสผ่าน"
                aria-pressed="false"
              >
                <i class="mdi mdi-eye-off-outline"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn btn-primary d-grid w-100">ตั้งรหัสผ่าน</button>

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

      const form = document.getElementById('formReset');
      const pwd  = document.getElementById('password');
      const cf   = document.getElementById('password_confirmation');

      function clearValidity(el){ el.setCustomValidity(''); }

      form.addEventListener('submit', function (e) {
        clearValidity(pwd); clearValidity(cf);

        const v = (pwd.value || '').trim();
        const okLen   = v.length >= 8;
        const okUpper = /[A-Z]/.test(v);
        const okNum   = /\d/.test(v);

        if (!(okLen && okUpper && okNum)) {
          e.preventDefault();
          pwd.setCustomValidity('รหัสผ่านต้องยาวอย่างน้อย 8 ตัว และมีตัวพิมพ์ใหญ่พร้อมตัวเลข');
          pwd.reportValidity();
          return;
        }

        if (pwd.value !== cf.value) {
          e.preventDefault();
          cf.setCustomValidity('รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน');
          cf.reportValidity();
        }
      });
    })();
  </script>
@endsection

