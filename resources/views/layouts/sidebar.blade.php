<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="#" class="app-brand-link">
      <span class="app-brand-logo demo me-1"><span style="color: var(--bs-primary)"></span></span>
      <span class="app-brand-text demo menu-text fw-semibold ms-2">Expense Claim</span>
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

    {{-- Normal --}}
    <li class="menu-item {{ request()->routeIs(['Expense.index','Expense.create','Expense.history']) ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
        <div data-i18n="เบิกค่าใช้จ่าย">เบิกค่าใช้จ่าย</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs(['Expense.index','Expense.create']) ? 'active' : '' }}">
          <a href="{{ route('Expense.index') }}" class="menu-link">
            <div data-i18n="รายการเบิก">รายการเบิก</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('Expense.history') ? 'active' : '' }}">
          <a href="{{ route('Expense.history') }}" class="menu-link">
            <div data-i18n="ประวัติการเบิก">ประวัติการเบิก</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- Normal Approve --}}
    @if (isApprover())
      <li class="menu-item {{ request()->routeIs('HeadApprove.index') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons mdi mdi-file-check"></i>
          <div data-i18n="การอนุมัติ">การอนุมัติ</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs('HeadApprove.index') ? 'active' : '' }}">
            <a href="{{ route('HeadApprove.index') }}" class="menu-link">
              <div data-i18n="รายการขออนุมัติ">รายการขออนุมัติ</div>
            </a>
          </li>
        </ul>
      </li>
    @endif

        @if (isApproverHR())
      <li class="menu-item {{ request()->routeIs('HeadHRApprove.index') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons mdi mdi-file-check"></i>
          <div data-i18n="การอนุมัติ [HR]">การอนุมัติ [HR]</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs('HeadHRApprove.index') ? 'active' : '' }}">
            <a href="{{ route('HeadHRApprove.index') }}" class="menu-link">
              <div data-i18n="รายการขออนุมัติ [HR]">รายการขออนุมัติ [HR]</div>
            </a>
          </li>
        </ul>
      </li>
    @endif

    @if (isset($userModuleRoles['AllSystems']) ||
        (isset($userModuleRoles['Driver']) && collect($userModuleRoles['Driver'])->flatten()->intersect(['Staff','Admin','SuperAdmin'])->isNotEmpty()))
      <li class="menu-header fw-medium mt-4">
        <span class="menu-header-text" data-i18n="Other Driver">Other Driver</span>
      </li>

      <li class="menu-item {{ request()->routeIs(['DriverClaim.index','DriverClaim.create','DriverClaim.history']) ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
          <div data-i18n="เบิกค่าใช้จ่ายพขร.">เบิกค่าใช้จ่ายพขร.</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs(['DriverClaim.index','DriverClaim.create']) ? 'active' : '' }}">
            <a href="{{ route('DriverClaim.index') }}" class="menu-link">
              <div data-i18n="รายการเบิก">รายการเบิก</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs('DriverClaim.history') ? 'active' : '' }}">
            <a href="{{ route('DriverClaim.history') }}" class="menu-link">
              <div data-i18n="ประวัติการเบิก">ประวัติการเบิก</div>
            </a>
          </li>
        </ul>
      </li>
    @endif

    @if (isset($userModuleRoles['AllSystems']) ||
        (isset($userModuleRoles['Tech']) && collect($userModuleRoles['Tech'])->flatten()->intersect(['Staff','Admin','SuperAdmin'])->isNotEmpty()))
      <li class="menu-header fw-medium mt-4">
        <span class="menu-header-text" data-i18n="Other Tech">Other Tech</span>
      </li>

      <li class="menu-item {{ request()->routeIs(['TechClaim.index','TechClaim.create','TechClaim.history']) ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
          <div data-i18n="เบิกค่าใช้จ่ายช่าง">เบิกค่าใช้จ่ายช่าง</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs(['TechClaim.index','TechClaim.create']) ? 'active' : '' }}">
            <a href="{{ route('TechClaim.index') }}" class="menu-link">
              <div data-i18n="รายการเบิก">รายการเบิก</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs('TechClaim.history') ? 'active' : '' }}">
            <a href="{{ route('TechClaim.history') }}" class="menu-link">
              <div data-i18n="ประวัติการเบิก">ประวัติการเบิก</div>
            </a>
          </li>
        </ul>
      </li>
    @endif

    @if (isset($userModuleRoles['AllSystems']) ||
        (isset($userModuleRoles['HR']) && collect($userModuleRoles['HR'])->flatten()->intersect(['Staff','Admin','SuperAdmin'])->isNotEmpty()))
      <li class="menu-header fw-medium mt-4">
        <span class="menu-header-text" data-i18n="HR">HR</span>
      </li>

      <li class="menu-item {{ request()->routeIs([
            'HR.index','HR.edit','HR.approved','HR.view',
            'HR.hrdriver','HR.driverapproved','HR.hrnextapprove','HR.grouplist'
          ]) ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons mdi mdi-account-check-outline"></i>
          <div data-i18n="HR">HR</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs(['HR.index','HR.edit']) ? 'active' : '' }}">
            <a href="{{ route('HR.index') }}" class="menu-link">
              <div data-i18n="รายการส่งเบิก">รายการส่งเบิก</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs('HR.hrdriver') ? 'active' : '' }}">
            <a href="{{ route('HR.hrdriver') }}" class="menu-link">
              <div data-i18n="รายการส่งเบิก พขร.">รายการส่งเบิก พขร.</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs(['HR.approved','HR.hrnextapprove']) ? 'active' : '' }}">
            <a href="{{ route('HR.approved') }}" class="menu-link">
              <div data-i18n="รายการตรวจสอบแล้ว">รายการตรวจสอบแล้ว</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs('HR.grouplist') ? 'active' : '' }}">
            <a href="{{ route('HR.grouplist') }}" class="menu-link">
              <div data-i18n="รายการอนุมัติ">รายการอนุมัติ</div>
            </a>
          </li>
        </ul>
      </li>
    @endif

    @if (isset($userModuleRoles['AllSystems']) ||
        (isset($userModuleRoles['Account']) && collect($userModuleRoles['Account'])->flatten()->intersect(['Staff','Admin','SuperAdmin'])->isNotEmpty()))
      <li class="menu-header fw-medium mt-4">
        <span class="menu-header-text" data-i18n="บัญชี">บัญชี</span>
      </li>

      <li class="menu-item {{ request()->routeIs(['Account.index','Account.manage','Account.view','Account.listhold','Account.listapproved']) ? 'open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons mdi mdi-currency-usd"></i>
          <div data-i18n="บัญชี">บัญชี</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs(['Account.index','Account.manage']) ? 'active' : '' }}">
            <a href="{{ route('Account.index') }}" class="menu-link">
              <div data-i18n="รายการกลุ่มอนุมัติ">รายการกลุ่มอนุมัติ</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs(['Account.listapproved','Account.view']) ? 'active' : '' }}">
            <a href="{{ route('Account.listapproved') }}" class="menu-link">
              <div data-i18n="รายการอนุมัติแล้ว">รายการอนุมัติแล้ว</div>
            </a>
          </li>
          <li class="menu-item" style="">
                  <a href="javascript:void(0);" class="menu-link menu-toggle waves-effect">
                    <div data-i18n="รายงาน">รายงาน</div>
                  </a>
                  <ul class="menu-sub">
                    <li class="menu-item">
                      <a href="{{ route('Account.AllowanceReport.index') }}" class="menu-link">
                        <div data-i18n="สรุปเบี้ยเลี้ยงประจำเดือน">สรุปเบี้ยเลี้ยงประจำเดือน</div>
                      </a>
                    </li>
                    {{-- <li class="menu-item">
                      <a href="extended-ui-timeline-fullscreen.html" class="menu-link">
                        <div data-i18n="Fullscreen">Fullscreen</div>
                      </a>
                    </li> --}}
                  </ul>
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
        (isset($userModuleRoles['User']) && collect($userModuleRoles['User'])->flatten()->intersect(['Staff','Admin','SuperAdmin'])->isNotEmpty()))
      {{-- User --}}
      <li class="menu-item {{ request()->routeIs(['User.index','User.create','Role.index','Role.create']) ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons mdi mdi-account-multiple-plus"></i>
          <div data-i18n="User">User</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs(['User.index','User.create']) ? 'active' : '' }}">
            <a href="{{ route('User.index') }}" class="menu-link">
              <div data-i18n="จัดการผู้ใช้งาน">จัดการผู้ใช้งาน</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs(['Role.index','Role.create']) ? 'active' : '' }}">
            <a href="{{ route('Role.index') }}" class="menu-link">
              <div data-i18n="สิทธิการใช้งาน">สิทธิการใช้งาน</div>
            </a>
          </li>
        </ul>
      </li>
    @endif

    {{-- Setting --}}
    @if (isset($userModuleRoles['AllSystems']) ||
        (isset($userModuleRoles['Setting']) && collect($userModuleRoles['Setting'])->flatten()->intersect(['Staff','Admin','SuperAdmin'])->isNotEmpty()))
      <li class="menu-item {{ request()->routeIs([
            'FuelPrice91.index',
            'Pricepermeal.*',
            'FuelPrice.*',
            'importlist.index',
            'Typegroup.index',
            'DistanceRate.index',
            'HRgroup.*',
            'ACgroup.*',
            'MessageAlert.index',
            'SpecialApprove.index',
            'DriverApprove.*'
          ]) ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons mdi mdi-cog"></i>
          <div data-i18n="Setting">Setting</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs('Pricepermeal.*') ? 'active' : '' }}">
            <a href="{{ route('Pricepermeal.index') }}" class="menu-link">
              <div data-i18n="ราคาต่อมื้อ">ราคาต่อมื้อ</div>
            </a>
          </li>

          <li class="menu-item {{ request()->routeIs('FuelPrice91.index') ? 'active' : '' }}">
            <a href="{{ route('FuelPrice91.index') }}" class="menu-link">
              <div data-i18n="ค่าน้ำมัน (โซฮอลล์ 91)">ค่าน้ำมัน (โซฮอลล์ 91)</div>
            </a>
          </li>

          <li class="menu-item {{ request()->routeIs('FuelPrice.*') ? 'active' : '' }}">
            <a href="{{ route('FuelPrice.index') }}" class="menu-link">
              <div data-i18n="ช่วงราคาค่าน้ำมัน">ช่วงราคาค่าน้ำมัน</div>
            </a>
          </li>

          <li class="menu-item {{ request()->routeIs('DistanceRate.index') ? 'active' : '' }}">
            <a href="{{ route('DistanceRate.index') }}" class="menu-link">
              <div data-i18n="Rate ระยะทาง">Rate ระยะทาง</div>
            </a>
          </li>

          <li class="menu-item {{ request()->routeIs(['importlist.index','Typegroup.index']) ? 'active' : '' }}">
            <a href="{{ route('importlist.index') }}" class="menu-link">
              <div data-i18n="Import รายชื่อ">Import รายชื่อ</div>
            </a>
          </li>

          <li class="menu-item {{ request()->routeIs('HRgroup.*') ? 'active' : '' }}">
            <a href="{{ route('HRgroup.index') }}" class="menu-link">
              <div data-i18n="กลุ่มอนุมัติ HR">กลุ่มอนุมัติ HR</div>
            </a>
          </li>

          <li class="menu-item {{ request()->routeIs('ACgroup.*') ? 'active' : '' }}">
            <a href="{{ route('ACgroup.index') }}" class="menu-link">
              <div data-i18n="กลุ่มอนุมัติ บัญชี">กลุ่มอนุมัติ บัญชี</div>
            </a>
          </li>

          <li class="menu-item {{ request()->routeIs('DriverApprove.*') ? 'active' : '' }}">
            <a href="{{ route('DriverApprove.index') }}" class="menu-link">
              <div data-i18n="สายอนุมัติ พขร.">สายอนุมัติ พขร.</div>
            </a>
          </li>

          <li class="menu-item {{ request()->routeIs('MessageAlert.index') ? 'active' : '' }}">
            <a href="{{ route('MessageAlert.index') }}" class="menu-link">
              <div data-i18n="ข้อความแจ้งเตือน">ข้อความแจ้งเตือน</div>
            </a>
          </li>

           <li class="menu-item {{ request()->routeIs('SpecialApprove.index') ? 'active' : '' }}">
            <a href="{{ route('SpecialApprove.index') }}" class="menu-link">
              <div data-i18n="รายการอนุมัติ M3">รายการอนุมัติ M3</div>
            </a>
          </li>
        </ul>
      </li>
    @endif

    <li class="menu-header fw-medium mt-4">
      <span class="menu-header-text" data-i18n="User Manual">User Manual</span>
    </li>
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
        <div data-i18n="คู่มือการใช้งาน">คู่มือการใช้งาน</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ asset('storage/manuals/Expense_Claims_MU_BG_V0.2.pdf') }}" class="menu-link" target="_blank" rel="noopener">
            <div data-i18n="พนักงานทั่วไป">พนักงานทั่วไป</div>
          </a>
          <a href="{{ asset('storage/manuals/Expense_Claims_MU_HR_V0.2.pdf') }}" class="menu-link" target="_blank" rel="noopener">
            <div data-i18n="HR">HR</div>
          </a>
          <a href="{{ asset('storage/manuals/Expense_Claims_MU_AC_V0.2.pdf') }}" class="menu-link" target="_blank" rel="noopener">
            <div data-i18n="Account">Account</div>
          </a>
          <a href="{{ asset('storage/manuals/Expense_Claims_MU_Setting_V0.2.pdf') }}" class="menu-link" target="_blank" rel="noopener">
            <div data-i18n="Setting">Setting</div>
          </a>
        </li>
      </ul>
    </li>
  </ul>
</aside>
