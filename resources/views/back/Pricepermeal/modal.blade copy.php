<div class="modal fade" id="GroupsModal" tabindex="-1" aria-labelledby="GroupsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="GroupsModalLabel">Groups</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-3">
                    <form action="#" id="frmAddgroups">
                        @csrf
                        <div class="card-body row">
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control" id="groups" name="groups"
                                        placeholder="Group name">
                                    <label for="groups">Group name</label>
                                    <div id="groups-error" class="text-danger small"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-4">
                                    <select class="form-select" id="level" name="level"
                                        aria-label="Default select example">
                                        <option value="">select level</option>
                                        @foreach ($levels as $level)
                                            <option value="{{ $level->id }}">{{ $level->levelname }}</option>
                                        @endforeach
                                    </select>
                                    <label for="level">level</label>
                                    <div id="level-error" class="text-danger small"></div>
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
                                    <input type="text" class="form-control" id="idedit" name="idedit">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary rounded-pill" id="btnAddGroups"><span
                                        class="mdi mdi-plus-circle"></span> Add</button>
                                <button type="button" class="btn btn-warning rounded-pill hidden"
                                    id="btnEditGroups"><span class="mdi mdi-plus-circle"></span> Edit</button>
                                <button type="button" class="btn btn-dark rounded-pill" id="btnResetGroups"> <span
                                        class="mdi mdi-refresh-circle"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatables-basic table table-bordered" id="GroupsTable">
                                <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>Groups</th>
                                        <th>Level</th>
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

