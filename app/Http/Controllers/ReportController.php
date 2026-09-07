<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\ChartAccount;
use App\Company;
use App\Customer;
use App\Vendor;
use Carbon\Carbon;
use Session;
use DB;
use PDF;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if( count($request->all()) ) {
            return $this->generate_report($request);
        }
        
        $chartaccounts = ChartAccount::select('id', DB::raw('CONCAT(code, " -- ", name) as option_name'))->orderBy('option_name', 'asc')->lists('option_name', 'id');
        $chartaccounts_option = ['' => 'Select'] + (is_callable([$chartaccounts, 'toArray']) ? $chartaccounts->toArray() : []);
        
        return view('report.index', compact('chartaccounts_option'));
    }

    public function index2(Request $request)
    {
        if( count($request->all()) ) {
            return $this->generate_report2($request);
        }
        
        $chartaccounts = ChartAccount::select('id', DB::raw('CONCAT(code, " -- ", name) as option_name'))->orderBy('option_name', 'asc')->lists('option_name', 'id');
        $chartaccounts_option = ['' => 'Select'] + (is_callable([$chartaccounts, 'toArray']) ? $chartaccounts->toArray() : []);
        
        return view('report.index2', compact('chartaccounts_option'));
    }

    private function report_method( $form = '' )
    {
        $method = '_genrep';
        $method .= ($form) ? '_' . str_replace('-', '_', $form) : '';
        return $method;
    }

    public function generate_report2($request)
    {
        $form = $request->input('form');
        $method = $this->report_method($form); 
        //dd( $form, method_exists($this, $method), $method );
        
        if( $form && method_exists($this, $method) ) {
            // Call the existing method in this Controller.
            return $this->{ $method }( $request );
        }
        else {
            // Did not find the method you're calling so bring the page back to report instead.
            return redirect('report');
        }
    }

    public function generate_report($request)
    {
        $vouchers = $this->get_vouchers($request);
        
        /* Some customed abbreviation to shorten the URL. 
         * 'ac'      => 'action'
         * 'dlpdf'   => 'download pdf report'
         * 'dlexcel' => 'download excel report'
        */
        if(!empty($request->ac)) {
            if($request->ac == 'dlpdf') {
                return $this->generate_pdf($vouchers);
            }
            elseif($request->ac == 'dlpdf2') {
                return $this->generate_pdf2($request);
            }
            elseif($request->ac == 'dlexcel') {
                return $this->generate_excel($vouchers);
            }
        }
        
        return view('report.report', compact('vouchers'));
    }

    public function get_vouchers($request)
    {
        $mod_vouchers = [];
        
        $names = '';
        foreach(['vendor', 'customer'] as $n) {
            $names .= !empty( $request->{ $n } ) ? $request->{ $n } : '';
        }
        $are_all_names_blank = empty( $names );
        
        /* linked tables --> vouchers, chart_accounts, supplier_invoices, vendors */
        if($request->vendor || $are_all_names_blank) {
            $mv = DB::table('vouchers as v')
                ->select(
                    'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                    'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'si.id as mod_id', 'si.date as date', 'si.description',
                    DB::raw("TRIM(IF(ven.individual, CONCAT(ven.first_name, ' ', ven.middle_name, ' ', ven.last_name), ven.company_name)) as entity_name") 
                )
                ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
                ->leftJoin('supplier_invoices as si', function($join) { 
                    $join->on('v.ref_id', '=', 'si.id')->on('v.module_alias', '=', DB::raw("'si'"));
                })
                ->leftJoin('vendors as ven', 'si.vendor_id', '=', 'ven.id')
                ->where('v.module_alias', '=', DB::raw("'si'"))
                ->where('si.date', '>=', $request->from)
                ->where('si.date', '<=', $request->to);
                
            // filter by vendor
            if($request->vendor) {
                $s = $request->vendor;
                $mv->where(function($query) use ($s) {
                    $query->where(DB::raw("TRIM(IF(ven.individual, CONCAT(ven.first_name, ' ', ven.middle_name, ' ', ven.last_name), ven.company_name))"), 'LIKE', "$s%")
                        ->orWhere('ven.company_name', 'LIKE', "$s%");
                });
            }
            if($request->chart_account_id) {
                $mv->where('v.chart_account_id', $request->chart_account_id);
            }
            if($request->ref_number) {
                $mv->where('v.ref_number', 'like', "{$request->ref_number}%");
            }
            
            $mod_vouchers[] = $mv;
        }
        /* linked tables --> vouchers, chart_accounts, supplier_invoices, vendors */
        
        
        
        /* linked tables --> vouchers, chart_accounts, cash_payment_vouchers, vendors */
        if($request->vendor || $are_all_names_blank) {
            $mv = DB::table('vouchers as v')
                ->select(
                    'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                    'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'cpv.id as mod_id', 'cpv.date as date', DB::raw("'' as description"),
                    DB::raw("TRIM(IF(ven.individual, CONCAT(ven.first_name, ' ', ven.middle_name, ' ', ven.last_name), ven.company_name)) as entity_name") 
                )
                ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
                ->leftJoin('cash_payment_vouchers as cpv', function($join) { 
                    $join->on('v.ref_id', '=', 'cpv.id')->on('v.module_alias', '=', DB::raw("'cpv'"));
                })
                ->leftJoin('vendors as ven', 'cpv.vendor_id', '=', 'ven.id')
                ->where('v.module_alias', '=', DB::raw("'cpv'"))
                ->where('cpv.date', '>=', $request->from)
                ->where('cpv.date', '<=', $request->to);
                
            // filter by vendor
            if($request->vendor) {
                $s = $request->vendor;
                $mv->where(function($query) use ($s) {
                    $query->where(DB::raw("TRIM(IF(ven.individual, CONCAT(ven.first_name, ' ', ven.middle_name, ' ', ven.last_name), ven.company_name))"), 'LIKE', "$s%")
                        ->orWhere('ven.company_name', 'LIKE', "$s%");
                });
            }
            if($request->chart_account_id) {
                $mv->where('v.chart_account_id', $request->chart_account_id);
            }
            if($request->ref_number) {
                $mv->where('v.ref_number', 'like', "{$request->ref_number}%");
            }
            
            $mod_vouchers[] = $mv;
        }
        /* linked tables --> vouchers, chart_accounts, cash_payment_vouchers, vendors */
        
        
        
        /* linked tables --> vouchers, chart_accounts, cashinvoices, customers */
        if($request->customer || $are_all_names_blank) {
            $mv = DB::table('vouchers as v')
                ->select(
                    'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                    'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'ci.id as mod_id', 'ci.date as date', DB::raw("'' as description"),
                    DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) as entity_name") 
                )
                ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
                ->leftJoin('cashinvoices as ci', function($join) { 
                    $join->on('v.ref_id', '=', 'ci.id')->on('v.module_alias', '=', DB::raw("'ci'"));
                })
                ->leftJoin('customers as cus', 'ci.customer_id', '=', 'cus.id')
                ->where('v.module_alias', '=', DB::raw("'ci'"))
                ->where('ci.date', '>=', $request->from)
                ->where('ci.date', '<=', $request->to);
                
            // filter by customer
            if($request->customer) {
                $s = $request->customer;
                $mv->where(function($query) use ($s) {
                    $query->where(DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name))"), 'LIKE', "$s%")
                        ->orWhere('cus.company_name', 'LIKE', "$s%");
                });
            }
            if($request->chart_account_id) {
                $mv->where('v.chart_account_id', $request->chart_account_id);
            }
            if($request->ref_number) {
                $mv->where('v.ref_number', 'like', "{$request->ref_number}%");
            }
            
            $mod_vouchers[] = $mv;
        }
        /* linked tables --> vouchers, chart_accounts, cashinvoices, customers */
        
        
        
        /* Union All */
        for($i = 1; $i < count($mod_vouchers); $i++) {
            $mod_vouchers[0]->unionAll( $mod_vouchers[$i] );
        }
        
        
        
        // dd( $mod_vouchers[0]->toSql() );
        $mod_voucher = $mod_vouchers[0];
        $vouchers = $mod_voucher->orderBy('date')->get();
        
        $balance = 0;
        foreach( $vouchers as &$v ) {
            $balance = $balance + floatval($v->debit) - floatval($v->credit);
            if(round($balance, 2) == 0) $balance = 0;
            
            $v->balance = $balance;
            $v->debit_formatted = (floatval($v->debit) > 0) ? number_format( floatval($v->debit), 2, '.', ',' ) : '';
            $v->credit_formatted = (floatval($v->credit) > 0) ? number_format( floatval($v->credit), 2, '.', ',' ) : '';
            $v->balance_formatted = number_format( floatval($v->balance), 2, '.', ',' );
        }
        
        return $vouchers;
    }

    public function generate_pdf($vouchers) 
    {
        // return view('report.pdf', compact('vouchers'));
        return PDF::loadView('report.pdf', compact('vouchers'))->download('quicktax-report.pdf');
    }

    /* Experimental */
    public function generate_pdf2($request) 
    {
        if(!empty($request->rndrtype)) {
            if($request->rndrtype == 'html') {
                return view('report.pdf', compact('vouchers'));
            }
            elseif($request->rndrtype == 'local') {
                return PDF::loadFile('http://sample.dev/invoice_template/index.html')->download('invoice - 201705.pdf');
            }
        }
        
        return PDF::loadView('report.pdf2')->download('quicktax-report.pdf');
    }

    public function generate_excel($vouchers) 
    {
        return view('report.excel', compact('vouchers'));
    }

    private function _genrep_accounts_payable_summary($request)
    {
        $print_type = $request->input('action', 'html'); // set html as default

        $ap = ChartAccount::accounts_payable();
        $accounts_payable_id = ( $ap ) ? $ap->id : 0;

        $rows = DB::select("
SELECT vendor_id, ref_id, mod_alias, date, vendor, SUM(payable) AS payable, SUM(payment) AS payment, SUM(payable-payment) AS balance FROM (
  SELECT ven.id vendor_id, si.id as ref_id, 'si' as mod_alias, si.date AS date, TRIM(IF(ven.individual, CONCAT(ven.first_name, ' ', ven.middle_name, ' ', ven.last_name), ven.company_name)) AS vendor, 
  SUM(IFNULL((SELECT credit FROM vouchers WHERE chart_account_id = $accounts_payable_id AND ref_id = si.id AND module_alias = 'si' LIMIT 1), 0)) AS payable, 0 AS payment 
  FROM supplier_invoices si LEFT JOIN vendors ven ON si.vendor_id = ven.id 
  WHERE si.date >= '{$request->from}' AND si.date <= '{$request->to}' 
  GROUP BY ven.id
  UNION ALL 
  SELECT ven.id vendor_id, cpv.id as ref_id, 'cpv' as mod_alias, cpv.date AS date, TRIM(IF(ven.individual, CONCAT(ven.first_name, ' ', ven.middle_name, ' ', ven.last_name), ven.company_name)) AS vendor, 
  0 AS payable, SUM(IFNULL((SELECT debit FROM vouchers WHERE chart_account_id = $accounts_payable_id AND ref_id = cpv.id AND module_alias = 'cpv' LIMIT 1), 0)) AS payment 
  FROM cash_payment_vouchers cpv LEFT JOIN vendors ven ON cpv.vendor_id = ven.id 
  WHERE cpv.date >= '{$request->from}' AND cpv.date <= '{$request->to}' 
  GROUP BY ven.id 
) AS t1 
GROUP BY vendor HAVING balance <> 0 ");
        // dd($rows);

        $total_balance = 0;
        foreach( $rows as &$r ) {
            $r->balance_formatted = number_format( floatval($r->balance), 2, '.', ',' );
            $total_balance += $r->balance;
        }
        // dd($rows);
        $total_balance = number_format( floatval($total_balance), 2, '.', ',' );

        $date = date('F d, Y');

        $company = Company::info();
        $company = Company::getCompanyNameFromInfo( $company );

        $filename = $request->input('form');
        if($print_type == 'pdf') {
            $content = view('report.html.accounts_payable_summary', compact('company', 'rows', 'date', 'total_balance'))->render();
            // return view('report.pdf_report', compact('content'));
            return PDF::loadView('report.pdf_report', compact('content'))->download("$filename.pdf");
        }

        $pdf_link = url()->current() . '?' . http_build_query(['action' => 'pdf'] + $request->all());
        $content = view('report.html.accounts_payable_summary', compact('company', 'rows', 'date', 'total_balance', 'pdf_link'))->render();
        return view('report.html_report', compact('content'));
    }

    private function _genrep_vendor_accounts_payable_detail($request)
    {
        $print_type = $request->input('action', 'html'); // set html as default

        $ap = ChartAccount::accounts_payable();
        $accounts_payable_id = ( $ap ) ? $ap->id : 0;

        $vid = $request->input('vid', 0);
        if( !$vid ) {
            $ven = Vendor::findOneByName( $request->vendor );
            if( $ven && !empty($ven->id) ) $vid = $ven->id;
        }

        $rows = DB::select("
SELECT * FROM (
  SELECT ven.id vendor_id, si.id as ref_id, 'si' as mod_alias, si.date AS date, 
  TRIM(IF(ven.individual, CONCAT(ven.first_name, ' ', ven.middle_name, ' ', ven.last_name), ven.company_name)) AS vendor, 
  'Supplier\'s Invoice' AS invoice, si.invoice_number AS reference, 
  IFNULL((SELECT credit FROM vouchers WHERE chart_account_id = $accounts_payable_id AND ref_id = si.id AND module_alias = 'si' LIMIT 1), 0) AS payable, 0 AS payment 
  FROM supplier_invoices si LEFT JOIN vendors ven ON si.vendor_id = ven.id 
  WHERE ven.id = '{$vid}' AND si.date >= '{$request->from}' AND si.date <= '{$request->to}' 
  UNION ALL 
  SELECT ven.id vendor_id, cpv.id as ref_id, 'cpv' as mod_alias, cpv.date AS date, 
  TRIM(IF(ven.individual, CONCAT(ven.first_name, ' ', ven.middle_name, ' ', ven.last_name), ven.company_name)) AS vendor, 
  'Cash Paym. Voucher' AS invoice, cpv.cv_number AS reference, 
  0 AS payable, IFNULL((SELECT debit FROM vouchers WHERE chart_account_id = $accounts_payable_id AND ref_id = cpv.id AND module_alias = 'cpv' LIMIT 1), 0) AS payment 
  FROM cash_payment_vouchers cpv LEFT JOIN vendors ven ON cpv.vendor_id = ven.id 
  WHERE ven.id = '{$vid}' AND cpv.date >= '{$request->from}' AND cpv.date <= '{$request->to}' 
) AS t1 
ORDER BY date");
        // dd($rows);

        $vendor = '';
        $balance = 0;
        foreach( $rows as &$r ) {
            if(!$vendor) $vendor = $r->vendor;

            $balance = $balance + floatval($r->payable) - floatval($r->payment);
            if(round($balance, 2) == 0) $balance = 0;

            $r->balance = $balance;
            $r->balance_formatted = number_format( floatval($r->balance), 2, '.', ',' );
            $r->amount = ($r->invoice == 'Supplier\'s Invoice') ? floatval($r->payable) : -floatval($r->payment);
            $r->amount_formatted = number_format( floatval($r->amount), 2, '.', ',' );
        }
        // dd($rows);
        $balance_formatted = number_format( floatval($balance), 2, '.', ',' );

        $company = Company::info();
        $company = Company::getCompanyNameFromInfo( $company );

        $filename = $request->input('form');
        if($print_type == 'pdf') {
            $content = view('report.html.vendor_accounts_payable_detail', compact('company', 'rows', 'vendor', 'balance_formatted'))->render();
            return PDF::loadView('report.pdf_report', compact('content'))->download("$filename.pdf");
        }

        $pdf_link = url()->current() . '?' . http_build_query(['action' => 'pdf'] + $request->all());
        $content = view('report.html.vendor_accounts_payable_detail', compact('company', 'rows', 'vendor', 'balance_formatted', 'pdf_link'))->render();
        return view('report.html_report', compact('content'));
    }

    private function _genrep_accounts_receivable_summary($request)
    {
        $print_type = $request->input('action', 'html'); // set html as default

        $rows = DB::select("
SELECT customer_id, date, customer, SUM(payable) AS payable, SUM(payment) AS payment, SUM(payable-payment) AS balance FROM (
  SELECT cus.id customer_id, cri.date AS date, TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) AS customer, 
  SUM(cri.amount_due) AS payable, 0 AS payment 
  FROM creditinvoices cri LEFT JOIN customers cus ON cri.customer_id = cus.id 
  WHERE cri.date >= '{$request->from}' AND cri.date <= '{$request->to}' 
  GROUP BY cus.id
  UNION ALL 
  SELECT cus.id customer_id, cr.date AS date, TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) AS customer, 
  0 AS payable, sum(cr.amount) AS payment 
  FROM collection_receipts cr LEFT JOIN customers cus ON cr.customer_id = cus.id 
  WHERE cr.date >= '{$request->from}' AND cr.date <= '{$request->to}' 
  GROUP BY cus.id 
) AS t1 
GROUP BY customer");
        // dd($rows);

        $total_balance = 0;
        foreach( $rows as &$r ) {
            $r->balance_formatted = number_format( floatval($r->balance), 2, '.', ',' );
            $total_balance += $r->balance;
        }
        // dd($rows);
        $total_balance = number_format( floatval($total_balance), 2, '.', ',' );

        $date = date('F d, Y');

        $company = Company::info();
        $company = Company::getCompanyNameFromInfo( $company );

        $filename = $request->input('form');
        if($print_type == 'pdf') {
            $content = view('report.html.accounts_receivable_summary', compact('company', 'rows', 'date', 'total_balance'))->render();
            return PDF::loadView('report.pdf_report', compact('content'))->download("$filename.pdf");
        }

        $pdf_link = url()->current() . '?' . http_build_query(['action' => 'pdf'] + $request->all());
        $content = view('report.html.accounts_receivable_summary', compact('company', 'rows', 'date', 'total_balance', 'pdf_link'))->render();
        return view('report.html_report', compact('content'));
    }

    private function _genrep_customer_accounts_receivable_detail($request)
    {
        $print_type = $request->input('action', 'html'); // set html as default

        $cid = $request->input('cid', 0);
        if( !$cid ) {
            $cus = Customer::findOneByName( $request->customer );
            if( $cus && !empty($cus->id) ) $cid = $cus->id;
        }

        $rows = DB::select("
SELECT * FROM (
  SELECT cus.id customer_id, cri.date AS date, TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) AS customer, 
  cri.id AS ref_id, 'Credit Invoice' AS invoice, cri.invoice_number AS reference, cri.amount_due AS payable, 0 AS payment 
  FROM creditinvoices cri LEFT JOIN customers cus ON cri.customer_id = cus.id 
  WHERE cus.id = '{$cid}' AND cri.date >= '{$request->from}' AND cri.date <= '{$request->to}' 
  UNION ALL 
  SELECT cus.id customer_id, cr.date AS date, TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) AS customer, 
  cr.id AS ref_id, 'Collection Receipt' AS invoice, cr.cr_number AS reference, 0 AS payable, amount AS payment 
  FROM collection_receipts cr LEFT JOIN customers cus ON cr.customer_id = cus.id 
  WHERE cus.id = '{$cid}' AND cr.date >= '{$request->from}' AND cr.date <= '{$request->to}' 
) AS t1 
ORDER BY date");
        // dd($rows);

        $customer = '';
        $balance = 0;
        foreach( $rows as &$r ) {
            if(!$customer) $customer = $r->customer;

            $balance = $balance + floatval($r->payable) - floatval($r->payment);
            if(round($balance, 2) == 0) $balance = 0;

            $r->balance = $balance;
            $r->balance_formatted = number_format( floatval($r->balance), 2, '.', ',' );
            $r->amount = ($r->invoice == 'Credit Invoice') ? floatval($r->payable) : -floatval($r->payment);
            $r->amount_formatted = number_format( floatval($r->amount), 2, '.', ',' );
        }
        // dd($rows);
        $balance_formatted = number_format( floatval($balance), 2, '.', ',' );

        $company = Company::info();
        $company = Company::getCompanyNameFromInfo( $company );

        $filename = $request->input('form');
        if($print_type == 'pdf') {
            $content = view('report.html.customer_accounts_receivable_detail', compact('company', 'rows', 'customer', 'balance_formatted'))->render();
            return PDF::loadView('report.pdf_report', compact('content'))->download("$filename.pdf");
        }

        $pdf_link = url()->current() . '?' . http_build_query(['action' => 'pdf'] + $request->all());
        $content = view('report.html.customer_accounts_receivable_detail', compact('company', 'rows', 'customer', 'balance_formatted', 'pdf_link'))->render();
        return view('report.html_report', compact('content'));
    }

    private function _genrep_statement_of_finance_position($request)
    {
        $print_type = $request->input('action', 'html'); // set html as default

        // Issue in COA code length: we can no longer guarantee that all level 1 COA's have a length of 4 chars.
        // So need to find another way to identify or group the COA's by there level 1 parent COA.
        // Run the code below before each SFP(Statement of finance) query to ensure that all level1_parent is set to level 1 COA parent.
        ChartAccount::setLevel1Parent();
        
        $this_year_firstdate = date("Y-01-01", strtotime($request->as_of));
        $last_year_firstdate = date('Y-m-d', strtotime("$this_year_firstdate -1 year"));
        $last_year_lastdate = date('Y-m-d', strtotime("$this_year_firstdate -1 day"));
        
        $this_year = substr($this_year_firstdate, 0, 4);
        $last_year = substr($last_year_firstdate, 0, 4);
        $date = date('F d, Y', strtotime($request->as_of));
        $dates = compact('this_year', 'last_year', 'date');
        
        // Ensure that we get the correct uppercase/lowercase label
        $li_eq = [];
        
        $rows = DB::select("SELECT * FROM chart_account_types WHERE name IN ('ASSETS', 'LIABILITIES', 'EQUITY')");
        $account_types = [];
        foreach($rows as $r) {
            $account_types[ $r->id ] = $r;
            
            if(strpos(strtolower($r->name), 'l') === 0)
                $li_eq['li_label'] = $r->name;
            elseif(strpos(strtolower($r->name), 'e') === 0)
                $li_eq['eq_label'] = $r->name;
        }
        $account_type_ids = array_keys($account_types);

        $rows = DB::select("SELECT * FROM sub_account_types WHERE account_type_id IN (". implode(',', $account_type_ids) .")");
        $sub_account_types = [];
        foreach($rows as $r) {
            $sub_account_types[ $r->id ] = $r;
        }
        
        $sql = "
SELECT year, SUM(balance) AS balance, SUM(prev_balance) AS prev_balance, level1_coa, code, account_type_id, sub_account_type_id, level1_parent FROM (
    SELECT '$this_year' AS `year`, IFNULL(SUM(v.debit - v.credit), 0) AS balance, 0 AS prev_balance, 
    (SELECT name FROM chart_accounts WHERE id = ca.level1_parent) as level1_coa, 
    (SELECT code FROM chart_accounts WHERE id = ca.level1_parent) as code, 
    ca.account_type_id, ca.sub_account_type_id, level1_parent 
    FROM chart_accounts ca LEFT JOIN vouchers v ON ca.id = v.chart_account_id 
    WHERE ca.account_type_id IN (". implode(',', $account_type_ids) .") 
    AND v.date >= '{$this_year_firstdate}' AND v.date <= '{$request->as_of}' 
    GROUP BY ca.level1_parent 
    UNION ALL 
    SELECT '$last_year' AS `year`, 0 AS balance, IFNULL(SUM(v.debit - v.credit), 0) AS prev_balance, 
    (SELECT name FROM chart_accounts WHERE id = ca.level1_parent) as level1_coa, 
    (SELECT code FROM chart_accounts WHERE id = ca.level1_parent) as code, 
    ca.account_type_id, ca.sub_account_type_id, level1_parent 
    FROM chart_accounts ca LEFT JOIN vouchers v ON ca.id = v.chart_account_id 
    WHERE ca.account_type_id IN (". implode(',', $account_type_ids) .") 
    AND v.date >= '{$last_year_firstdate}' AND v.date <= '{$last_year_lastdate}' 
    GROUP BY ca.level1_parent 
) AS t1 GROUP BY level1_parent 
ORDER BY account_type_id, sub_account_type_id 
";
        // echo '<pre>' . $sql . '</pre>'; die;
        $rows = DB::select($sql);

        $sfp = $totals = [];
        foreach($rows as $r) {
            // Account type: example: ASSETS
            $account_type_name = $account_types[ $r->account_type_id ]->name;
            if( !isset($sfp[$account_type_name]) ) {
                $sfp[$account_type_name] = [];
                
                $totals[$account_type_name] = [];
                $totals[$account_type_name]['--total--'] = ['balance' => 0, 'prev_balance' => 0, ];
            }
            
            // Sub Account type: example: CURRENT ASSETS
            $sub_account_type_name = $sub_account_types[ $r->sub_account_type_id ]->name;
            if( !isset($sfp[$account_type_name][$sub_account_type_name]) ) {
                $sfp[$account_type_name][$sub_account_type_name] = [];
                
                $totals[$account_type_name][$sub_account_type_name] = ['balance' => 0, 'prev_balance' => 0, ];
            }
            
            $sfp[$account_type_name][$sub_account_type_name][] = $r;
            
            $totals[$account_type_name][$sub_account_type_name]['balance'] += $r->balance;
            $totals[$account_type_name]['--total--']['balance'] += $r->balance;
            $totals[$account_type_name][$sub_account_type_name]['prev_balance'] += $r->prev_balance;
            $totals[$account_type_name]['--total--']['prev_balance'] += $r->prev_balance;
        }
        // dd($sfp, $totals);

        $company = Company::info();
        $company = Company::getCompanyNameFromInfo( $company );

        $filename = $request->input('form');
        if($print_type == 'pdf') {
            $content = view('report.html.statement_of_finance_position', compact('company', 'sfp', 'totals', 'li_eq', 'dates', 'account_types', 'sub_account_types'))->render();
            // return view('report.pdf_report', compact('content'));
            return PDF::loadView('report.pdf_report', compact('content'))->download("$filename.pdf");
        }

        $pdf_link = url()->current() . '?' . http_build_query(['action' => 'pdf'] + $request->all());
        $content = view('report.html.statement_of_finance_position', compact('company', 'sfp', 'totals', 'li_eq', 'dates', 'account_types', 'sub_account_types', 'pdf_link'))->render();
        return view('report.html_report', compact('content'));
    }

    private function _genrep_trial_balance($request)
    {
        $print_type = $request->input('action', 'html'); // set html as default

        $sql = "
SELECT * FROM (
    SELECT ca.id AS ca_id, v.id AS v_id, ca.name AS coa_name, SUM(v.debit) AS debit, SUM(v.credit) AS credit FROM chart_accounts ca 
    LEFT JOIN vouchers v ON ca.id = v.chart_account_id 
    WHERE v.date >= '{$request->from}' AND v.date <= '{$request->to}' 
    GROUP BY ca.id 
    HAVING debit > 0 OR credit > 0
) AS t1 ORDER BY ca_id, v_id;
";
        // echo '<pre>' . $sql . '</pre>'; die;
        $rows = DB::select($sql);
        // dd($rows);

        $totals = (object) ['debit' => 0, 'credit' => 0];
        foreach($rows as &$r) {
            $r->debit_formatted = (floatval($r->debit)) ? number_format( floatval($r->debit), 2, '.', ',' ) : '';
            $r->credit_formatted = (floatval($r->credit)) ? number_format( floatval($r->credit), 2, '.', ',' ) : '';
            $totals->debit += $r->debit;
            $totals->credit += $r->credit;
        }
        $totals->debit_formatted = number_format( floatval($totals->debit), 2, '.', ',' );
        $totals->credit_formatted = number_format( floatval($totals->credit), 2, '.', ',' );

        $from = date('F d, Y', strtotime($request->from));
        $to   = date('F d, Y', strtotime($request->to));
        $dates = compact('from', 'to');

        $company = Company::info();
        $company = Company::getCompanyNameFromInfo( $company );

        $filename = $request->input('form');
        if($print_type == 'pdf') {
            $content = view('report.html.trial_balance', compact('company', 'dates', 'rows', 'totals'))->render();
            // return view('report.pdf_report', compact('content'));
            return PDF::loadView('report.pdf_report', compact('content'))->download("$filename.pdf");
        }

        $pdf_link = url()->current() . '?' . http_build_query(['action' => 'pdf'] + $request->all());
        $content = view('report.html.trial_balance', compact('company', 'dates', 'rows', 'totals', 'pdf_link'))->render();
        return view('report.html_report', compact('content'));
    }

    function _genrep_income_statement_old($request)
    {
        /* 
        Revenue: 
        Sales + Other Income = Total Revenue 
        Total Revenue - Cost of Sales/Service = Gross Profit 
        Gross Profit - Operating Expenses = Net Income before Tax 
        Net Income before Tax - Income Tax = Net Income 
        */
        
        $print_type = $request->input('action', 'html'); // set html as default

        $sql = "
SELECT * FROM (
    SELECT ca.id AS ca_id, v.id AS v_id, ca.name AS coa_name, SUM(v.debit) AS debit, SUM(v.credit) AS credit FROM chart_accounts ca 
    LEFT JOIN vouchers v ON ca.id = v.chart_account_id 
    WHERE v.date >= '{$request->from}' AND v.date <= '{$request->to}' 
    GROUP BY ca.id 
    HAVING debit > 0 OR credit > 0
) AS t1 ORDER BY ca_id, v_id;
";
        // echo '<pre>' . $sql . '</pre>'; die;
        $rows = DB::select($sql);
        // dd($rows);
        $sales = $other_income = $total_revenue = $cost_sales_service = $gross_profit = $operating_expenses = $net_income_b4_tax = 0;

        $from = date('F d, Y', strtotime($request->from));
        $to   = date('F d, Y', strtotime($request->to));
        $dates = compact('from', 'to');

        $amounts = compact('sales', 'other_income', 'total_revenue', 'cost_sales_service', 'gross_profit', 'operating_expenses', 'net_income_b4_tax');

        $company = Company::info();
        $company = Company::getCompanyNameFromInfo( $company );

        $filename = $request->input('form');
        if($print_type == 'pdf') {
            $content = view('report.html.income_statement', compact('company', 'amounts', 'dates'))->render();
            return view('report.pdf_report', compact('content'));
            // return PDF::loadView('report.pdf_report', compact('content'))->download("$filename.pdf");
        }

        $pdf_link = url()->current() . '?' . http_build_query(['action' => 'pdf'] + $request->all());
        $content = view('report.html.income_statement', compact('company', 'amounts', 'dates', 'pdf_link'))->render();
        return view('report.html_report', compact('content'));
    }

    function _genrep_income_statement($request)
    {
        $print_type = $request->input('action', 'html'); // set html as default

        // Issue in COA code length: we can no longer guarantee that all level 1 COA's have a length of 4 chars.
        // So need to find another way to identify or group the COA's by there level 1 parent COA.
        // Run the code below before each SFP(Statement of finance) query to ensure that all level1_parent is set to level 1 COA parent.
        ChartAccount::setLevel1Parent();

        $from = date('F d, Y', strtotime($request->from));
        $to   = date('F d, Y', strtotime($request->to));
        $dates = compact('from', 'to');
        
        // Ensure that we get the correct uppercase/lowercase label
        $rev_exp = [];
        
        $rows = DB::select("SELECT * FROM chart_account_types WHERE name IN ('REVENUE', 'EXPENSES')");
        $account_types = [];
        foreach($rows as $r) {
            $account_types[ $r->id ] = $r;
            
            if(strpos(strtolower($r->name), 'revenue') === 0) // get the revenue label
                $rev_exp['rev_label'] = $r->name;
            elseif(strpos(strtolower($r->name), 'expense') === 0) // get the expenses label
                $rev_exp['exp_label'] = $r->name;
        }
        $account_type_ids = array_keys($account_types);

        $rows = DB::select("SELECT * FROM sub_account_types WHERE account_type_id IN (". implode(',', $account_type_ids) .")");
        $sub_account_types = [];
        foreach($rows as $r) {
            $sub_account_types[ $r->id ] = $r;
        }

        $sql = "
SELECT SUM(balance) AS balance, SUM(prev_balance) AS prev_balance, level1_coa, code, account_type_id, sub_account_type_id, level1_parent FROM (
    SELECT IFNULL(SUM(v.debit - v.credit), 0) AS balance, 0 AS prev_balance, 
    (SELECT name FROM chart_accounts WHERE id = ca.level1_parent) as level1_coa, 
    (SELECT code FROM chart_accounts WHERE id = ca.level1_parent) as code, 
    ca.account_type_id, ca.sub_account_type_id, level1_parent 
    FROM chart_accounts ca LEFT JOIN vouchers v ON ca.id = v.chart_account_id 
    WHERE ca.account_type_id IN (". implode(',', $account_type_ids) .") 
    AND v.date >= '{$request->from}' AND v.date <= '{$request->to}' 
    GROUP BY ca.level1_parent 
) AS t1 GROUP BY level1_parent 
ORDER BY account_type_id, sub_account_type_id 
";
        // echo '<pre>' . $sql . '</pre>';
        $rows = DB::select($sql);
        // dd( $rows, $account_types, $sub_account_types );

        $is = $totals = [];
        foreach($rows as $r) {
            // do not include COA that has zero balance
            if($r->balance == 0) continue;
            
            // Account type: example: REVENUE
            $account_type_name = $account_types[ $r->account_type_id ]->name;
            if( !isset($is[$account_type_name]) ) {
                $is[$account_type_name] = [];
                
                $totals[$account_type_name] = [];
                $totals[$account_type_name]['--total--'] = ['balance' => 0];
            }
            
            // There can be no sub_account_type to pull from DB if ID = 0 so
            // set sub_account_type info manually to an array converted to an OBJECT
            if( !isset($sub_account_types[ $r->sub_account_type_id ]) )
                $sub_account_types[ $r->sub_account_type_id ] = (object) [ 'id' => 0, 'account_type_id' => $r->account_type_id, 'name' => '' ];
            
            $sub_account_type_name = $sub_account_types[ $r->sub_account_type_id ]->name;
            if( !isset($is[$account_type_name][$sub_account_type_name]) ) {
                $is[$account_type_name][$sub_account_type_name] = [];
                
                $totals[$account_type_name][$sub_account_type_name] = ['balance' => 0];
            }
            
            $is[$account_type_name][$sub_account_type_name][] = $r;
            
            $totals[$account_type_name][$sub_account_type_name]['balance'] += $r->balance;
            $totals[$account_type_name]['--total--']['balance'] += $r->balance;
        }
        // dd($is, $totals);

        $company = Company::info();
        $company = Company::getCompanyNameFromInfo( $company );

        $filename = $request->input('form');
        if($print_type == 'pdf') {
            $content = view('report.html.income_statement', compact('company', 'is', 'totals', 'rev_exp', 'dates'))->render();
            // return view('report.pdf_report', compact('content'));
            return PDF::loadView('report.pdf_report', compact('content'))->download("$filename.pdf");
        }

        $pdf_link = url()->current() . '?' . http_build_query(['action' => 'pdf'] + $request->all());
        $content = view('report.html.income_statement', compact('company', 'is', 'totals', 'rev_exp', 'dates', 'pdf_link'))->render();
        return view('report.html_report', compact('content'));
    }
}
