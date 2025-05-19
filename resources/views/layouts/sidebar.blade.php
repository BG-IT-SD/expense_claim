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
        <li class="menu-item @if (Route::is('Expense.index') || Route::is('Expense.create') || Route::is('Expense.history')) active open @endif ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                <div data-i18n="เบิกค่าใช้จ่าย">เบิกค่าใช้จ่าย</div>

            </a>
            <ul class="menu-sub">
                <li class="menu-item @if (Route::is('Expense.index') || Route::is('Expense.create')) active @endif">
                    <a href="{{ route('Expense.index') }}" class="menu-link">
                        <div data-i18n="รายการเบิก">รายการเบิก</div>
                        {{-- <div class="badge bg-danger rounded-pill ms-auto">5</div> --}}
                    </a>
                </li>
                <li class="menu-item  @if (Route::is('Expense.history')) active @endif">
                    <a href="{{ route('Expense.history') }}" class="menu-link">
                        <div data-i18n="ประวัติการเบิก">ประวัติการเบิก</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Normal Approve --}}
        @if (isApprover())
        <li class="menu-item @if (Route::is('HeadApprove.index')) active open @endif ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-file-check"></i>
                <div data-i18n="การอนุมัติ">การอนุมัติ</div>

            </a>
            <ul class="menu-sub">
                <li class="menu-item @if (Route::is('HeadApprove.index')) active @endif">
                    <a href="{{ route('HeadApprove.index') }}" class="menu-link">
                        <div data-i18n="รายการขออนุมัติ">รายการขออนุมัติ</div>
                        {{-- <div class="badge bg-danger rounded-pill ms-auto">5</div> --}}
                    </a>
                </li>
            </ul>
        </li>
        @endif
        @if (isset($userModuleRoles['AllSystems']) ||
                (isset($userModuleRoles['Driver']) &&
                    collect($userModuleRoles['Driver'])->flatten()->intersect(['Staff', 'Admin', 'SuperAdmin'])->isNotEmpty()))
            <li class="menu-header fw-medium mt-4">
                <span class="menu-header-text" data-i18n="Other Driver">Other Driver</span>
            </li>
            <!-- Driver and Tech -->
            <li class="menu-item @if (Route::is('DriverClaim.index') || Route::is('DriverClaim.create') || Route::is('DriverClaim.history')) active open @endif ">
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
                    <li class="menu-item @if (Route::is('DriverClaim.index') || Route::is('DriverClaim.create')) active @endif">
                        <a href="{{ route('DriverClaim.index') }}" class="menu-link">
                            <div data-i18n="รายการเบิก">รายการเบิก</div>
                            {{-- <div class="badge bg-danger rounded-pill ms-auto">2</div> --}}
                        </a>
                    </li>
                    <li class="menu-item @if (Route::is('DriverClaim.history')) active @endif">
                        <a href="{{ route('DriverClaim.history') }}" class="menu-link">
                            <div data-i18n="ประวัติการเบิก">ประวัติการเบิก</div>
                            {{-- <div class="badge bg-danger rounded-pill ms-auto">2</div> --}}
                        </a>
                    </li>
                </ul>
            </li>
            {{-- <li class="menu-item">
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
            </li> --}}
        @endif

        @if (isset($userModuleRoles['AllSystems']) ||
                (isset($userModuleRoles['Tech']) &&
                    collect($userModuleRoles['Tech'])->flatten()->intersect(['Staff', 'Admin', 'SuperAdmin'])->isNotEmpty()))
            <li class="menu-header fw-medium mt-4">
                <span class="menu-header-text" data-i18n="Other Tech">Other Tech</span>
            </li>
            <li class="menu-item @if (Route::is('TechClaim.index') || Route::is('TechClaim.create') || Route::is('TechClaim.history')) active open @endif ">
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
                    <li class="menu-item @if (Route::is('TechClaim.index') || Route::is('TechClaim.create')) active @endif">
                        <a href="{{ route('TechClaim.index') }}" class="menu-link">
                            <div data-i18n="รายการเบิก">รายการเบิก</div>
                            {{-- <div class="badge bg-danger rounded-pill ms-auto">3</div> --}}
                        </a>
                    </li>
                    <li class="menu-item  @if (Route::is('TechClaim.history')) active @endif">
                        <a href="{{ route('TechClaim.history') }}" class="menu-link">
                            <div data-i18n="ประวัติการเบิก">ประวัติการเบิก</div>
                        </a>
                    </li>
                </ul>
            </li>
            {{-- <li class="menu-item ">
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
            </li> --}}
            {{-- <li class="menu-item ">
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
            </li> --}}
        @endif

        @if (isset($userModuleRoles['AllSystems']) ||
                (isset($userModuleRoles['HR']) &&
                    collect($userModuleRoles['HR'])->flatten()->intersect(['Staff', 'Admin', 'SuperAdmin'])->isNotEmpty()))
            <li class="menu-header fw-medium mt-4">
                <span class="menu-header-text" data-i18n="HR">HR</span>
            </li>
            <!-- HR -->
            <li class="menu-item @if (Route::is('HR.index') ||
                    Route::is('HR.edit') ||
                    Route::is('HR.approved') ||
                    Route::is('HR.view') ||
                    Route::is('HR.hrdriver') ||
                    Route::is('HR.driverapproved') ||
                    Route::is('HR.hrnextapprove') ||
                    Route::is('HR.grouplist')) active open @endif ">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-account-check-outline"></i>
                    <div data-i18n="HR">HR</div>

                </a>
                <ul class="menu-sub">
                    <li class="menu-item @if (Route::is('HR.index') || Route::is('HR.edit')) active @endif">
                        <a href="{{ route('HR.index') }}" class="menu-link">
                            <div data-i18n="รายการส่งเบิก">รายการส่งเบิก</div>
                            {{-- <div class="badge bg-danger rounded-pill ms-auto">2</div> --}}
                        </a>
                    </li>
                    {{-- <li class="menu-item @if (Route::is('HR.approved')) active @endif">
                        <a href="{{ route('HR.approved') }}" class="menu-link">
                            <div data-i18n="รายการตรวจสอบแล้ว">รายการตรวจสอบแล้ว</div>
                        </a>
                    </li> --}}
                    {{-- <li class="menu-header fw-medium mt-4">
                        <span class="menu-header-text" data-i18n="สำหรับ พขร.">สำหรับ พขร.</span>
                    </li> --}}
                    <li class="menu-item @if (Route::is('HR.hrdriver')) active @endif">
                        <a href="{{ route('HR.hrdriver') }}" class="menu-link">
                            <div data-i18n="รายการส่งเบิก พขร.">รายการส่งเบิก พขร.</div>
                            {{-- <div class="badge bg-danger rounded-pill ms-auto">2</div> --}}
                        </a>
                    </li>
                    {{-- <li class="menu-item @if (Route::is('HR.driverapproved')) active @endif">
                        <a href="{{ route('HR.driverapproved') }}" class="menu-link">
                            <div data-i18n="รายการตรวจสอบแล้ว พขร.">รายการตรวจสอบแล้ว พขร.</div>
                        </a>
                    </li> --}}
                    {{-- <li class="menu-header fw-medium mt-4">
                        <span class="menu-header-text" data-i18n="ตรวจสอบข้อมูล">ตรวจสอบข้อมูล</span>
                    </li> --}}
                    <li class="menu-item @if (Route::is('HR.approved') || Route::is('HR.hrnextapprove')) active @endif">
                        <a href="{{ route('HR.approved') }}" class="menu-link">
                            <div data-i18n="รายการตรวจสอบแล้ว">รายการตรวจสอบแล้ว</div>
                        </a>
                    </li>
                    <li class="menu-item @if (Route::is('HR.grouplist')) active @endif">
                        <a href="{{ route('HR.grouplist') }}" class="menu-link">
                            <div data-i18n="รายการอนุมัติ">รายการอนุมัติ</div>
                        </a>
                    </li>
                    {{-- <li class="menu-header fw-medium mt-4">
                        <span class="menu-header-text" data-i18n="รายงาน">รายงาน</span>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div data-i18n="รายการอนุมัติแล้ว">รายการอนุมัติแล้ว</div>
                        </a>
                    </li> --}}
                </ul>
            </li>
        @endif

        @if (isset($userModuleRoles['AllSystems']) ||
                (isset($userModuleRoles['Account']) &&
                    collect($userModuleRoles['Account'])->flatten()->intersect(['Staff', 'Admin', 'SuperAdmin'])->isNotEmpty()))
            <li class="menu-header fw-medium mt-4">
                <span class="menu-header-text" data-i18n="บัญชี">บัญชี</span>
            </li>
            <!-- Account -->
            <li class="menu-item @if (Route::is('Account.index') || Route::is('Account.manage') || Route::is('Account.view') ) open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-currency-usd"></i>
                    <div data-i18n="บัญชี">บัญชี</div>

                </a>
                <ul class="menu-sub">
                    <li class="menu-item @if (Route::is('Account.index') || Route::is('Account.manage') || Route::is('Account.view')) active @endif">
                        <a href="{{ route('Account.index') }}" class="menu-link">
                            <div data-i18n="รายการกลุ่มอนุมัติ">รายการกลุ่มอนุมัติ</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif


        @if (isset($userModuleRoles['AllSystems']) || isset($userModuleRoles['Setting']) || isset($userModuleRoles['User']))
            <li class="menu-header fw-medium mt-4">
                <span class="menu-header-text" data-i18n="Admin">Admin</span>
            </li>
        @endif
        @if (isset($userModuleRoles['AllSystems']) ||
                (isset($userModuleRoles['User']) &&
                    collect($userModuleRoles['User'])->flatten()->intersect(['Staff', 'Admin', 'SuperAdmin'])->isNotEmpty()))
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
        @endif
        {{-- Setting --}}
        @if (isset($userModuleRoles['AllSystems']) ||
                (isset($userModuleRoles['Setting']) &&
                    collect($userModuleRoles['Setting'])->flatten()->intersect(['Staff', 'Admin', 'SuperAdmin'])->isNotEmpty()))
            <li class="menu-item  @if (Route::is('FuelPrice91.index') ||
                    Route::is('Pricepermeal.index') ||
                    Route::is('Pricepermeal.create') ||
                    Route::is('FuelPrice.index') ||
                    Route::is('FuelPrice.create') ||
                    Route::is('importlist.index') ||
                    Route::is('Typegroup.index') ||
                    Route::is('DistanceRate.index')) active open @endif">
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
        @endif
    </ul>
</aside>
