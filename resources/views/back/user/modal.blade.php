<div class="modal fade" id="RolesModal" tabindex="-1" aria-labelledby="RolesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="GroupsModalLabel">Roles</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-3">
                    <form action="#" id="frmAddgroups">
                        @csrf
                        <div class="card-body row">
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-4">
                                    <select class="form-select" id="moduleid" name="moduleid"
                                        aria-label="Default select example">
                                        <option value="">เลือกหน้าจอการทำงาน</option>
                                        @foreach ($modules as $module)
                                            <option value="{{ $module->id }}">{{ $module->modulename }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="userid" id="userid">
                                    <input type="hidden" class="form-control" id="idedit" name="idedit">
                                    <label for="moduleid">หน้าจอการทำงาน</label>
                                    <div id="moduleid-error" class="text-danger small"></div>
                                    <div id="userid-error" class="text-danger small"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-4">
                                    <select class="form-select" id="roleid" name="roleid"
                                        aria-label="Default select example">
                                        <option value="">เลือกสิทธิการเข้าถึง</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->rolename }}</option>
                                        @endforeach
                                    </select>
                                    <label for="roleid">สิทธิการเข้าถึง</label>
                                    <div id="roleid-error" class="text-danger small"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-4">
                                    <select class="form-select" id="status" name="status"
                                        aria-label="Default select example">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <label for="status">status</label>
                                    <div id="status-error" class="text-danger small"></div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary rounded-pill" id="btnAddRole"><span
                                        class="mdi mdi-plus-circle"></span> Add</button>
                                <button type="button" class="btn btn-warning rounded-pill hidden"
                                    id="btnEditRole"><span class="mdi mdi-plus-circle"></span> Edit</button>
                                <button type="button" class="btn btn-dark rounded-pill" id="btnResetRole"> <span
                                        class="mdi mdi-refresh-circle"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatables-basic table table-bordered" id="RoleTable">
                                <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>Module</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
