@extends('layouts.login.templatelogin')
@section('contentlogin')
    <div class="authentication-inner py-4">
        <!-- Login -->
        <div class="card p-2">
            <div class="card-body mt-2">
                <h4 class="mb-3 text-center">Expense Claim</h4>
                {{-- <p class="mb-4"></p> --}}
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
                <form id="formAuthentication" class="mb-3" action="{{ url('/login') }}" method="POST">
                    @csrf
                    <div class="form-floating form-floating-outline mb-3">
                        <input type="text" class="form-control" id="empid" name="empid"
                            placeholder="Enter your Employee ID" autofocus />
                        <label for="email">Employee ID</label>
                    </div>
                    <div class="mb-3">
                        <div class="form-password-toggle">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <label for="password">Password</label>
                                </div>
                                <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                    </div>
                    <div class="divider my-4">
                        <div class="divider-text">กรณีเข้าระบบครั้งแรก</div>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-label-primary waves-effect d-grid w-100" type="button"
                            onclick="window.location.href = '{{ route('register') }}'">Create Password</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- /Login -->
@endsection
