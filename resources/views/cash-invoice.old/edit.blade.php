@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('header_style_preload')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Create New Cash Invoice</h1>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('cash-invoice.index') }}"> Back</a>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"></button>
                </div>
            </div>
            <div class="col-sm-6 cash-box-left-right">
                {!! Form::open(array('url' => 'cash-invoice/'.$editcashinvoice->id.'/edit','method'=>'GET')) !!}
                    <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                        <div class="input-group-btn"><button type="button" class="btn">Customer</button></div>
                        <input type="text" class="form-control customer_name" name="customer_name" value="">
                        <input type="hidden" value="" name="customer_id">
                        <span class="input-group-btn"><button type="submit" class="btn btn-info btn-flat btn-search" onclick="removeGet()">Go!</button></span>
                    </div>
                {!! Form::close() !!}
                <div class="box box-info box-solid vendor-info-box">
                <div class="box-header with-border">
                    <h3 class="box-title text-sm">Customer Info</h3>
                    <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool"></button></div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12 cash-invoice-input">
                    <div class="box-body">
                      <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-vendor-info">
                            <tbody>
                                @foreach ($displaycustomer as $customer)
                                {{-- */
                                $ids = $customer->id;
                                $company_name = $customer->company_name;
                                $first_name = $customer->first_name;
                                $middle_name = $customer->middle_name;
                                $last_name = $customer->last_name;
                                $individual = $customer->individual;
                                $city = $customer->city;
                                $country = $customer->country;
                                $tin = $customer->tin;
                                $branch_code = $customer->branch_code;
                                $phone_no = $customer->phone_no;
                                $fax = $customer->fax;
                                $email = $customer->email;
                            /* --}}

                                <tr class="individual_name"><th>Full Name/Company Name</th>
                                <td class="name" style="display: block;width: 250px;">
                                <input type="hidden" name="customer_id" value="{{$ids}}">
                                <input type="hidden" name="pay_to" value="{{$company_name.''.$first_name.' '.$middle_name.' '.$last_name}}">{{$company_name.$first_name.' '.$middle_name.' '.$last_name}}
                                </td></tr>
                                <tr><th>Individual?</th><td class="individual">@if($individual=='1') <span class="badge bg-green">Yes</span> @else <span class="badge bg-orange">No</span> @endif</td></tr>
                                <tr><th>City</th><td class="city">{{$city}}</td></tr>
                                <tr><th>Country</th><td class="country">{{$country}}</td></tr>
                                <tr><th>TIN</th><td class="tin">{{$tin}}</td></tr>
                                <tr><th>Branch Code</th><td class="branch_code">{{$branch_code}}</td></tr>
                                <tr><th>Phone</th><td class="phone">{{$phone_no}}</td></tr>
                                <tr><th>Fax</th><td class="fax">{{$fax}}</td></tr>
                                <tr><th>Email</th><td class="email">{{$email}}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                </div>
                    <div style="clear:both;"></div>
                </div>
            </div>

            {!! Form::model($editcashinvoice, ['method' => 'PATCH','route' => ['cash-invoice.update', $editcashinvoice->id]]) !!}
            <div class="col-sm-6 cash-box-left-right">
                <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                    <div class="input-group-btn"><button type="button" class="btn">Inv. No.</button></div>
                        {{-- */
                            $i = 0;
                            $invno = 0;
                            $i = $editcashinvoice->invoice_no;
                            $invno = str_pad($i, 6, "0", STR_PAD_LEFT);
                        /* --}}
                    <input type="text" class="form-control invoice-no" name="invoice_number" value="{{$invno}}">
                    <input type="hidden" name="customer_id" value="{{$ids}}">
                    <input type="hidden" name="pay_to" value="{{$company_name.''.$first_name.' '.$middle_name.' '.$last_name}}">
                </div>
            </div>
            <div class="col-sm-6 cash-box-left-right">
                <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                    <div class="input-group-btn"><button type="button" class="btn">Date</button></div>
                    <input type="text" class="form-control datepicker" name="invoice_date" value="{{$editcashinvoice->cash_inv_date}}">
                </div>
            </div>
            <div class="col-sm-6 cash-box-left-right">
                <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                    <div class="input-group-btn"><button type="button" class="btn">Amount</button></div>
                    <input type="text" class="form-control invoice-amount" name="invoice_amount" value="{{$editcashinvoice->invoice_amount}}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-left" style="margin-bottom: 20px;">
                <button type="submit" class="btn btn-primary update-btn">Update</button>
            </div>
            {!! Form::close() !!}
            <div style="clear: both;"></div>
        </div>
    </section>
</div>

@endsection

@section('footer_script_preload')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('footer_script')
<script>
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
    });
</script>
@endsection