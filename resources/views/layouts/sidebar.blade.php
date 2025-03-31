<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <span class="app-brand-logo demo me-1">
                <span style="color: var(--bs-primary)">

                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-semibold ms-2">Expense CLaim</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="mdi menu-toggle-icon d-xl-block align-middle mdi-20px"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-header fw-medium mt-4">
            <span class="menu-header-text" data-i18n="Normal">Normal</span>
        </li>
        <!-- Normal -->
        <li class="menu-item @if (Route::is('Expense.index') || Route::is('Expense.create')) active open @endif ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                <div data-i18n="เบิกค่าใช้จ่าย">เบิกค่าใช้จ่าย</div>

            </a>
            <ul class="menu-sub">
                {{-- <li class="menu-item @if (Route::is('Expense/main')) active @endif">
                    <a href="{{ route('Expense/main') }}" class="menu-link">
                        <div data-i18n="ฟอร์มการเบิก">ฟอร์มการเบิก</div>
                    </a>
                </li> --}}
                <li class="menu-item @if (Route::is('Expense.index') || Route::is('Expense.create')) active @endif">
                    <a href="{{ route('Expense.index') }}" class="menu-link">
                        <div data-i18n="รายการเบิก">รายการเบิก</div>
                        <div class="badge bg-danger rounded-pill ms-auto">5</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Normal Approve --}}
        <li class="menu-item @if (Route::is('HeadApprove.index')) active open @endif ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-file-check"></i>
                <div data-i18n="การอนุมัติ">การอนุมัติ</div>

            </a>
            <ul class="menu-sub">
                <li class="menu-item @if (Route::is('HeadApprove.index')) active @endif">
                    <a href="{{ route('HeadApprove.index') }}" class="menu-link">
                        <div data-i18n="รายการขออนุมัติ">รายการขออนุมัติ</div>
                        <div class="badge bg-danger rounded-pill ms-auto">5</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-header fw-medium mt-4">
            <span class="menu-header-text" data-i18n="Other Driver">Other Driver</span>
        </li>
        <!-- Driver and Tech -->
        <li class="menu-item @if (Route::is('DriverClaim.index')) active open @endif ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                <div data-i18n="เบิกค่าใช้จ่ายพขร.">เบิกค่าใช้จ่ายพขร.</div>

            </a>
            <ul class="menu-sub">
                {{-- <li class="menu-item @if (Route::is('DriverClaim.index')) active @endif">
                    <a href="{{ route('DriverClaim.index') }}" class="menu-link">
                        <div data-i18n="ฟอร์มการเบิก">ฟอร์มการเบิก</div>
                    </a>
                </li> --}}
                <li class="menu-item @if (Route::is('DriverClaim.index')) active @endif">
                    <a href="{{ route('DriverClaim.index') }}" class="menu-link">
                        <div data-i18n="รายการเบิก">รายการเบิก</div>
                        <div class="badge bg-danger rounded-pill ms-auto">2</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-file-check"></i>
                <div data-i18n="การอนุมัติ">การอนุมัติ</div>

            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('HeadApprove.index') }}" class="menu-link">
                        <div data-i18n="รายการขออนุมัติ">รายการขออนุมัติ</div>
                        <div class="badge bg-danger rounded-pill ms-auto">5</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-header fw-medium mt-4">
            <span class="menu-header-text" data-i18n="Other Tech">Other Tech</span>
        </li>
        <li class="menu-item ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                <div data-i18n="เบิกค่าใช้จ่ายช่าง">เบิกค่าใช้จ่ายช่าง</div>

            </a>
            <ul class="menu-sub">
                {{-- <li class="menu-item @if (Route::is('DriverClaim.index')) active @endif">
                    <a href="{{ route('DriverClaim.index') }}" class="menu-link">
                        <div data-i18n="ฟอร์มการเบิก">ฟอร์มการเบิก</div>
                    </a>
                </li> --}}
                <li class="menu-item">
                    <a href="{{ route('TechClaim.index') }}" class="menu-link">
                        <div data-i18n="รายการเบิก">รายการเบิก</div>
                        <div class="badge bg-danger rounded-pill ms-auto">3</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-note-search"></i>
                <div data-i18n="ตรวจสอบข้อมูล">ตรวจสอบข้อมูล</div>

            </a>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="{{ route('HeadApprove.index') }}" class="menu-link">
                        <div data-i18n="รายการตรวจสอบ">รายการตรวจสอบ</div>
                        <div class="badge bg-danger rounded-pill ms-auto">5</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-file-check"></i>
                <div data-i18n="การอนุมัติ">การอนุมัติ</div>

            </a>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="{{ route('HeadApprove.index') }}" class="menu-link">
                        <div data-i18n="รายการขออนุมัติ">รายการขออนุมัติ</div>
                        <div class="badge bg-danger rounded-pill ms-auto">5</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-header fw-medium mt-4">
            <span class="menu-header-text" data-i18n="HR">HR</span>
        </li>
        <!-- HR -->
        <li class="menu-item @if (Route::is('HR.index') || Route::is('HR.edit')) active open @endif ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-account-check-outline"></i>
                <div data-i18n="HR">HR</div>

            </a>
            <ul class="menu-sub">
                <li class="menu-item @if (Route::is('HR.index') || Route::is('HR.edit')) active @endif">
                    <a href="{{ route('HR.index') }}" class="menu-link">
                        <div data-i18n="รายการส่งเบิก">รายการส่งเบิก</div>
                        <div class="badge bg-danger rounded-pill ms-auto">2</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <div data-i18n="รายงาน">รายงาน</div>
                    </a>
                </li>
            </ul>
        </li>


        <li class="menu-header fw-medium mt-4">
            <span class="menu-header-text" data-i18n="Admin">Admin</span>
        </li>
        {{-- User --}}
        <li class="menu-item  @if (Route::is('User.index') || Route::is('Role.index') || Route::is('Role.create') || Route::is('User.create')) active open @endif ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-account-multiple-plus"></i>
                <div data-i18n="User">User</div>

            </a>
            <ul class="menu-sub">
                <li class="menu-item  @if (Route::is('User.index') || Route::is('User.create')) active @endif ">
                    <a href="{{ route('User.index') }}" class="menu-link">
                        <div data-i18n="จัดการผู้ใช้งาน">จัดการผู้ใช้งาน</div>
                    </a>
                </li>
                 <li class="menu-item @if (Route::is('Role.index') || Route::is('Role.create')) active @endif">
                    <a href="{{ route('Role.index') }}" class="menu-link">
                        <div data-i18n="สิทธิการใช้งาน">สิทธิการใช้งาน</div>
                    </a>
                </li>
                {{-- <li class="menu-item">
                    <a href="#" class="menu-link">
                        <div data-i18n="รายงาน">รายงาน</div>
                    </a>
                </li> --}}
            </ul>
        </li>
         {{-- Setting --}}
         <li class="menu-item  @if (Route::is('FuelPrice91.index') || Route::is('Pricepermeal.index') || Route::is('Pricepermeal.create') || Route::is('FuelPrice.index') || Route::is('FuelPrice.create') || Route::is('importlist.index') || Route::is('Typegroup.index') || Route::is('DistanceRate.index')) active open @endif">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-cog"></i>
                <div data-i18n="Setting">Setting</div>

            </a>
            <ul class="menu-sub">
                <li class="menu-item @if (Route::is('Pricepermeal.index') || Route::is('Pricepermeal.create')) active @endif">
                    <a href="{{ route('Pricepermeal.index') }}" class="menu-link">
                        <div data-i18n="ราคาต่อมื้อ">ราคาต่อมื้อ</div>
                    </a>
                </li>
                <li class="menu-item @if (Route::is('FuelPrice91.index')) active @endif ">
                    <a href="{{ route('FuelPrice91.index') }}" class="menu-link">
                        <div data-i18n="ค่าน้ำมัน (โซฮอลล์ 91)">ค่าน้ำมัน (โซฮอลล์ 91)</div>
                    </a>
                </li>
                <li class="menu-item @if (Route::is('FuelPrice.index') || Route::is('FuelPrice.create')) active @endif">
                    <a href="{{ route('FuelPrice.index') }}" class="menu-link">
                        <div data-i18n="ช่วงราคาค่าน้ำมัน">ช่วงราคาค่าน้ำมัน</div>
                    </a>
                </li>
                <li class="menu-item @if (Route::is('DistanceRate.index')) active @endif">
                    <a href="{{ route('DistanceRate.index') }}" class="menu-link">
                        <div data-i18n="Rate ระยะทาง">Rate ระยะทาง</div>
                    </a>
                </li>
                <li class="menu-item @if (Route::is('importlist.index') || Route::is('Typegroup.index')) active @endif">
                    <a href="{{ route('importlist.index') }}" class="menu-link">
                        <div data-i18n="Import รายชื่อ">Import รายชื่อ</div>
                    </a>
                </li>

            </ul>
        </li>

    </ul>
</aside>
