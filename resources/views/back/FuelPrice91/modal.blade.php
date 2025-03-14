<div class="modal fade" id="AddPriceModal" tabindex="-1" aria-labelledby="AddPriceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AddPriceModalLabel">Add</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="fuelPriceForm">
                    @csrf
                    <div class="row g-2">
                        <!-- Date Field -->
                        <div class="col-md-12 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="date" id="dateprice" name="dateprice" class="form-control" />
                                <label for="dateprice">Date</label>
                                <input type="hidden" id="id" name="id" class="form-control" />
                            </div>
                            <div id="dateprice-error" class="text-danger small"></div>
                        </div>

                        <!-- Price Field -->
                        <div class="col-md-12 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="number" id="price" name="price" min="0" step="0.01" class="form-control" />
                                <label for="price">Price</label>
                            </div>
                            <div id="price-error" class="text-danger small"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="submitBtn" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- Del --}}
<div class="modal fade" id="DelPriceModal" tabindex="-1" aria-labelledby="DelPriceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DelPriceModalLabel">Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="fuelDelPriceForm">
                    @csrf
                    <div class="row g-2">
                        <!-- Id Field -->
                        <div class="col-md-12 mb-2">
                            <h5>คุณต้องการลบข้อมูลหรือไม่</h5>
                            <div class="form-floating form-floating-outline">
                                <input type="hidden" id="idprice" name="idprice" class="form-control" />
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" id="ConfirmBtn" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- Del --}}
