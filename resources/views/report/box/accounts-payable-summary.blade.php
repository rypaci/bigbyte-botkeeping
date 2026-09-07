        {!! Form::open(['method' => 'get']) !!}
          {!! Form::hidden('form', 'accounts-payable-summary') !!}
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Accounts Payable Summary</h3>
            </div>
            
            <div class="box-body">
            <div class="row">
            
            {{--<div class="col-sm-12">
                <div class="form-group">
                    {!! Form::label('company', 'Company', ['class' => 'control-label']) !!}
                    {!! Form::text('company', null, ['class' => 'form-control', 'required']) !!}
                </div>
            </div>--}}
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
            
            </div>
            </div>
            <!-- /.box-body -->
            
            <div class="box-footer">
                    <div class="btn-group">
                      <button type="submit" name="action" value="html" class="btn btn-default btn-html"><i class="fa fa-html5 text-info"></i> Html</button>
                      <button type="submit" name="action" value="pdf" class="btn btn-default btn-pdf"><i class="fa fa-file-pdf-o text-danger"></i> PDF</button>
                    </div>
            </div>
          </div>
          <!-- /.box -->
        {!! Form::close() !!}
