        {!! Form::open(['method' => 'get']) !!}
          <div class="box box-solid">
            <div class="box-header with-border">
              <!-- <i class="fa fa-text-width"></i> -->
              <h3 class="box-title">Chart of Account Breakdown</h3>
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
            
            </div>
            <!-- /.row -->
            </div>
            <!-- /.box-body -->
            
            <div class="box-footer">
                    <div class="btn-group">
                      {{--<button type="submit" name="action" value="print_pdf" class="btn btn-default btn-pdf"><i class="fa fa-file-pdf-o text-danger"></i> PDF</button>--}}
                      {{--<button type="submit" name="action" value="print_excel" class="btn btn-default btn-excel"><i class="fa fa-file-excel-o text-success"></i> Excel</button>--}}
                      <button type="submit" name="action" value="print_html" class="btn btn-default btn-html"><i class="fa fa-html5 text-info"></i> Html</button>
                    </div>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        {!! Form::close() !!}
