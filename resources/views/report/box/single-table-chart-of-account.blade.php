        {!! Form::open() !!}
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Single Table Chart of Account</h3>
            </div>
            <!-- /.box-header -->
            
            <div class="box-body">
            <div class="row">
            
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('from', 'From', ['class' => 'control-label']) !!}
                    {!! Form::text('from', null, ['class' => 'form-control datepicker', 'required']) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('to', 'To', ['class' => 'control-label']) !!}
                    {!! Form::text('to', null, ['class' => 'form-control datepicker', 'required']) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('chart_account_id', 'Chart Account', ['class' => 'control-label']) !!}
                    {!! Form::select('chart_account_id', $chartaccounts_option, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('vendor', 'Vendor', ['class' => 'control-label']) !!}
                    {!! Form::text('vendor', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('customer', 'Customer', ['class' => 'control-label']) !!}
                    {!! Form::text('customer', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('ref_number', 'Ref #', ['class' => 'control-label']) !!}
                    {!! Form::text('ref_number', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            
            </div>
            <!-- /.row -->
            </div>
            <!-- /.box-body -->
            
            <div class="box-footer">
                    <div class="btn-group">
                      <button type="button" class="btn btn-default btn-pdf"><i class="fa fa-file-pdf-o text-danger"></i> PDF</button>
                      <button type="button" class="btn btn-default btn-excel"><i class="fa fa-file-excel-o text-success"></i> Excel</button>
                      <button type="button" class="btn btn-default btn-html"><i class="fa fa-html5 text-info"></i> Html</button>
                    </div>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        {!! Form::close() !!}
