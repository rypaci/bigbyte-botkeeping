<style>
.radio-wrapper > label:not(:first-of-type), .checkbox.icheck > label:not(:first-of-type) {
    margin-left: 12px;
}
.form-group.grp-taxdisc {
    min-height: 40px;
}
</style>
<div class="modal fade" id="modal-account-row-options">
  <div class="modal-dialog">
    <div class="modal-content">
      {!! Form::open(['method' => 'get']) !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Account Row Options</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="account_number" class="control-label">Account #</label>
          {!! Form::text('account_number', null, ['class' => 'form-control', 'readonly']) !!}
        </div>
        <div class="form-group">
          <label for="coa_id" class="control-label">Account Title</label>
          {!! custom_form_select(
              'coa_id',   // name
              '',         // value
              ['class' => 'form-control input-sm chart-account-dropdown', 'id' => 'coa_id'], // attribs
              $accounts,  // the options in array
              ['code'],         /* the data-fields in each <option> */
              false
          ) !!}
        </div>
        <div class="form-group">
          <div class="checkbox icheck">
            <label><input type="checkbox" name="taxordiscount" value="tax"> Tax </label>
            <label><input type="checkbox" name="taxordiscount" value="discount"> Discount </label>
          </div>
          {!! custom_form_select('tax_id', '', ['class' => 'form-control tax hidden', 'id' => 'tax_id'], $taxes, ['rate'], false) !!}
          {!! custom_form_select('discount_id', '', ['class' => 'form-control discount hidden', 'id' => 'discount_id'], $discounts, ['rate'], false) !!}
        </div>
        <div class="form-group">
          <label for="coa_id" class="control-label">Type</label>
          <div class="radio-wrapper icheck">
            <label><input type="radio" name="type" value="debit" checked> Debit </label>
            <label><input type="radio" name="type" value="credit"> Credit </label>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-update-account-row" data-dismiss="modal">Go</button>
      </div>
    {!! Form::close() !!}
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
