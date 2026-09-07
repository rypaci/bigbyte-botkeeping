    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"> Row 1 and Row 2 <small>(coa debit)</small>  </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Account Titles</label>
                  {!! Form::select('assetcoa_debit[]', $chart_accounts_option, null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                </div>
              <!-- /.form-group -->
              </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
    </div>
    
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"> Row 3 <small>(tax debit)</small> </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Account Titles</label>
                  {!! Form::select('assettax_debit[]', $chart_account_taxes_option, null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                </div>
              <!-- /.form-group -->
              </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
    </div>
    
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"> Row 4 <small>(coa credit)</small> </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Account Titles</label>
                  {!! Form::select('assetcoa_credit[]', $chart_accounts_option, null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                </div>
              <!-- /.form-group -->
              </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
    </div>
    
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"> Row 5 <small>(tax credit)</small> </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Account Titles</label>
                  {!! Form::select('assettax_credit[]', $chart_account_taxes_option, null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                </div>
              <!-- /.form-group -->
              </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
    </div>
    
    <div class="row">
      <div class="col-sm-3">
        <div class="form-group">
            {!! Form::submit('Save', ['class' => 'btn btn-primary form-control']) !!}
        </div>
      </div>
    </div>
