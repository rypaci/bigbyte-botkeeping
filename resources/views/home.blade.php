@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection

@section('header_style_postload')
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<style>
.pink {
  border-top-color: #FF6384;
}
.green {
  border-top-color: #00a65a;
}
.yellow {
  border-top-color: #f39c12;
}
.info-box-number{
  font-size: 24px;
}
</style>
@endsection
@section('content')
<?php
    $wrm_sales = App\WaterRefilling::sales();
    $wrm_expenses = $wrm_sales->wrm_expenses;
    $wrm_ca_expenses = $wrm_sales->ca_expenses;
    $wrmSales = $wrm_sales->sales;

    $pos_sales = App\POS_sales::sales();
    $pos_expenses = $pos_sales->pos_expenses;
    $ca_expenses = $pos_sales->ca_expenses;
    $posSales = $pos_sales->sales;
    
    $over_all_expenses = array_map(function () {
      return array_sum(func_get_args());
    }, $ca_expenses, $pos_expenses, $wrm_expenses);

    $over_all_sales = array_map(function () {
      return array_sum(func_get_args());
    }, $posSales, $wrmSales);

    $month = date('M');
    $firstDayofYear = new Carbon\Carbon('first day of January');
    $thisDay = Carbon\Carbon::now();

    $total_sales    = array_sum($over_all_sales);
    $total_cost     = array_sum($pos_sales->costs);
    $total_expenses = array_sum($over_all_expenses);
    $total_taxes    = array_sum($pos_sales->taxes);

    $productOptions = ['' => 'All Products'];
    foreach(App\Product::select('id', 'name', 'sr_priority')->orderBy('name')->get() as $product){
        $productOptions[$product->id] = empty($product->sr_priority) ? $product->name : $product->name.' - '.$product->sr_priority;
    }
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <section class="content-header">
        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; justify-content:space-between;">
            <div>
                <h1 style="margin:0; color:#FF6384;">Charts</h1>
                <small id="daterange-label" style="color:#000; font-size:13px; display:block; margin-top: 20px;">Showing: <?php echo date('M j, Y', strtotime($firstDayofYear)); ?> &ndash; <?php echo date('M j, Y', strtotime($thisDay)); ?></small>
            </div>
            <div class="input-group" style="width:auto; flex:1; max-width:480px;">
                <span class="input-group-addon"><i class="fa fa-filter"></i></span>
                <select id="product-filter" class="form-control" style="min-width:160px; max-width:220px;">
                    <?php foreach($productOptions as $id => $label): ?>
                    <option value="<?php echo $id; ?>"><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input name="daterange" type="text" placeholder="Select date range..." class="form-control" readonly style="min-width:180px;">
                <span class="input-group-btn">
                    <button type="button" id="search" class="btn btn-primary">
                        <i class="fa fa-search"></i> Filter
                    </button>
                    <button type="button" id="reset-filter" class="btn btn-default">
                        <i class="fa fa-refresh"></i> Reset
                    </button>
                </span>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="row">
      
        <div class="col-xs-12 col-md-6 col-lg-4">
          <!-- LINE CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Total Sales</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="chart-sales" style="height:250px"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        
        <div class="col-xs-12 col-md-6 col-lg-4">
          <!-- LINE CHART -->
          <div class="box pink">
            <div class="box-header with-border">
              <h3 class="box-title">Total Cost</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="chart-cost" style="height:250px"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        
        <div class="col-xs-12 col-md-offset-3 col-md-6 col-lg-offset-0 col-lg-4">
          <!-- LINE CHART -->
          <div class="box green">
            <div class="box-header with-border">
              <h3 class="box-title">Total Expenses</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="chart-expenses" style="height:250px"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        
      </div>
      <!-- /.row -->

      <div class="row">
      
        <div class="col-xs-12">
          <!-- LINE CHART -->
          <div class="box yellow">
            <div class="box-header with-border">
              <h3 class="box-title">Total Taxes (monthly or accumulated)</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="chart-taxes" style="height:250px"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      
      </div>
      <!-- /.row -->

      <div class="row" id="summary-boxes">

        <div class="col-xs-12 col-sm-6 col-md-3">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Sales</span>
              <span class="info-box-number" id="total-sales-box"><?php echo number_format($total_sales, 2); ?></span>
            </div>
          </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-3">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Cost</span>
              <span class="info-box-number" id="total-cost-box"><?php echo number_format($total_cost, 2); ?></span>
            </div>
          </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-3">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-bar-chart"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Expenses</span>
              <span class="info-box-number" id="total-expenses-box"><?php echo number_format($total_expenses, 2); ?></span>
            </div>
          </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-3">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-calculator"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Taxes</span>
              <span class="info-box-number" id="total-taxes-box"><?php echo number_format($total_taxes, 2); ?></span>
            </div>
          </div>
        </div>

      </div>
      <!-- /.row (summary) -->

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection


@section('footer_script_preload')
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>
<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
@endsection


@section('footer_script')
<script>
(function() {
    var month = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var chartbgcolor = { blue:"rgb(0, 192, 239, 0.5)",pink:"rgb(255, 99, 132, 0.5)",green:"rgb(0, 166, 90, 0.5)",yellow:"rgb(243, 156, 18, 0.5)" };
    var chartbcolor = { blue:"rgb(0, 192, 239)",pink:"rgb(255, 99, 132)",green:"rgb(0, 166, 90)",yellow:"rgb(243, 156, 18)" };
    var chartLabelMonths = labelMonth('<?php echo $firstDayofYear; ?>', '<?php echo $thisDay; ?>');

    var chart_name = ["chart-sales","chart-cost","chart-expenses","chart-taxes"];
    var chartSales = new Chart(chart_name[0], charts(<?php echo json_encode($over_all_sales); ?>, 'Sales', chartbgcolor.blue, chartbcolor.blue));
    var chartCosts = new Chart(chart_name[1], charts(<?php echo json_encode($pos_sales->costs); ?>, 'Costs', chartbgcolor.pink, chartbcolor.pink));
    var chartExpenses = new Chart(chart_name[2], charts(<?php echo json_encode($over_all_expenses); ?>, 'Expenses', chartbgcolor.green, chartbcolor.green));
    var chartTaxes = new Chart(chart_name[3], charts(<?php echo json_encode($pos_sales->taxes); ?>, 'Taxes', chartbgcolor.yellow, chartbcolor.yellow));
    
    function charts(data, label, bgcolor, bcolor) {
      return {
              type: 'line',
              data: {
                    labels: chartLabelMonths,
                  datasets: [{
                      backgroundColor: bgcolor,
                      borderColor: bcolor,
                      data: data,
                      label: label,
                      fill: 'start'  //false, origin, start, end
                  }]
              },
              options: {
                  scales: {
                      xAxes: [{
                          gridLines: { display: false }
                      }],
                      yAxes: [{
                          gridLines: { display: false }
                      }]
                  }
              }
          };
    }

    function formatAmount(val) {
      return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function sumArray(arr) {
      return arr.reduce(function(a, b) { return a + parseFloat(b); }, 0);
    }

    $('#search').on('click', function() {
      var url = '{{ url("voucher/get-date-range") }}';
      var daterange = $('input[name=daterange]').val();
      var productId = $('#product-filter').val();
      var fromDate, toDate;
      if (daterange != '') {
        var parts = daterange.split(' - ');
        fromDate = parts[0].trim();
        toDate = parts[1].trim();
      } else {
        fromDate = '<?php echo date("m/d/Y", strtotime($firstDayofYear)); ?>';
        toDate = '<?php echo date("m/d/Y", strtotime($thisDay)); ?>';
      }
      $.get(url, { daterange: fromDate + ' - ' + toDate, product_id: productId }, function(data) {
          var newLabels = labelMonth(fromDate, toDate);
          chartSales.data.labels = newLabels;
          chartCosts.data.labels = newLabels;
          chartExpenses.data.labels = newLabels;
          chartTaxes.data.labels = newLabels;
          chartSales.data.datasets[0].data = data[0].sales;
          chartCosts.data.datasets[0].data = data[0].costs;
          chartExpenses.data.datasets[0].data = data[0].expenses;
          chartTaxes.data.datasets[0].data = data[0].taxes;
          chartSales.update();
          chartCosts.update();
          chartExpenses.update();
          chartTaxes.update();
          $('#daterange-label').text('Showing: ' + fromDate + ' – ' + toDate);
          $('#total-sales-box').text(formatAmount(sumArray(data[0].sales)));
          $('#total-cost-box').text(formatAmount(sumArray(data[0].costs)));
          $('#total-expenses-box').text(formatAmount(sumArray(data[0].expenses)));
          $('#total-taxes-box').text(formatAmount(sumArray(data[0].taxes)));
      });
    });

    $('#reset-filter').on('click', function() {
      $('input[name=daterange]').val('');
      $('#product-filter').val('');
      var origLabels = labelMonth('<?php echo $firstDayofYear; ?>', '<?php echo $thisDay; ?>');
      chartSales.data.labels = origLabels;
      chartCosts.data.labels = origLabels;
      chartExpenses.data.labels = origLabels;
      chartTaxes.data.labels = origLabels;
      chartSales.data.datasets[0].data = <?php echo json_encode($over_all_sales); ?>;
      chartCosts.data.datasets[0].data = <?php echo json_encode($pos_sales->costs); ?>;
      chartExpenses.data.datasets[0].data = <?php echo json_encode($over_all_expenses); ?>;
      chartTaxes.data.datasets[0].data = <?php echo json_encode($pos_sales->taxes); ?>;
      chartSales.update();
      chartCosts.update();
      chartExpenses.update();
      chartTaxes.update();
      $('#daterange-label').text('Showing: <?php echo date("M j, Y", strtotime($firstDayofYear)); ?> – <?php echo date("M j, Y", strtotime($thisDay)); ?>');
      $('#total-sales-box').text(formatAmount(<?php echo $total_sales; ?>));
      $('#total-cost-box').text(formatAmount(<?php echo $total_cost; ?>));
      $('#total-expenses-box').text(formatAmount(<?php echo $total_expenses; ?>));
      $('#total-taxes-box').text(formatAmount(<?php echo $total_taxes; ?>));
    });

    function labelMonth(mfrom, mto) {
      mfrom = new Date(mfrom),
      mto = new Date(mto),
      //console.log(mfrom+'-'+mto);
      label_months = [];

      for(var i=mfrom.getMonth();i<=mto.getMonth();i++) {
        label_months.push(month[i]);
      }
      return label_months;
    }

    $('input[name="daterange"]').daterangepicker({
        autoUpdateInput: false,
        "opens":"left",
        locale: {
          cancelLabel: 'Close'
      }
    });

    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
    });
    
    //'chart-sales' 'chart-cost' 'chart-expenses' 'chart-taxes'
    
}());
</script>
@endsection