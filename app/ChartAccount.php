<?php



namespace App;



use Illuminate\Database\Eloquent\Model;
use Carbon;
use DB;



class ChartAccount extends Model

{

    /**

     * The database table used by the model.

     *

     * @var string

     */

    protected $table = 'chart_accounts';



    /**

    * The database primary key value.

    *

    * @var string

    */

    protected $primaryKey = 'id';



    /**

     * Attributes that should be mass-assignable.

     *

     * @var array

     */

    protected $fillable = ['name', 'level', 'account_type_id', 'parent_account_id', 'code', 'sub_account_type_id', 'normal_balance', 'tax_id'];



    public function account_type()

    {

        return $this->belongsTo('App\ChartAccountType', 'account_type_id');

    }



    public function sub_account_type()

    {

        return $this->belongsTo('App\SubAccountType', 'sub_account_type_id');

    }



    public function tax()

    {

        return $this->belongsTo('App\Tax', 'tax_id');

    }



    public static function getByIds($ids = [], $options = [])

    {

        // default options

        $o = [

            'filter' => false,

            'return_as' => 'array',

        ];



        if( ! is_array( $options ) ) 

            $options = [];



        $o = array_merge($o, $options);

        extract( $o );



        if($filter && $filter == 'tax') {

            $chart_accounts = self::select('id', 'name', 'code', 'tax_id', \DB::raw('(SELECT rate FROM taxes WHERE id = chart_accounts.tax_id LIMIT 1) as rate'))

                ->whereIn('id', $ids)

                ->orderBy('name', 'asc')->get();

        }

        elseif($filter && $filter == 'discount') {

            $chart_accounts = self::select('id', 'name', 'code', 'tax_id', \DB::raw('(SELECT rate FROM taxes WHERE id = chart_accounts.tax_id LIMIT 1) as rate'))

                ->whereIn('id', $ids)

                ->orderBy('name', 'asc')->get();

        }

        else {

            $chart_accounts = self::select('id', 'name', 'code')

                ->whereIn('id', $ids)

                ->orderBy('name', 'asc')->get();

        }



        $accounts = (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);



        $accounts_by_id = [];

        foreach($accounts as $acc) {

            if( $return_as == 'object' ) 

                $accounts_by_id[ $acc['id'] ] = (object) $acc;



            elseif( $return_as == 'array' ) 

                $accounts_by_id[ $acc['id'] ] = $acc;



            else 

                $accounts_by_id[ $acc['id'] ] = $acc;

        }



        return $accounts_by_id;

    }



    public static function accounts_payable()

    {

        return DB::table('chart_accounts')->where('name', 'like', 'Accounts Payable%')->first();

    }



    // set level1_parent by code

    public static function setLevel1Parent( $chart_account_id = 0 )

    {

        if( $chart_account_id ) {

            // update a single COA row

            $row = DB::table('chart_accounts')->where('id', $chart_account_id)->first();

            if($row) {

                if($row->level == 1) {

                    DB::update("UPDATE chart_accounts SET level1_parent = '{$row->id}' WHERE id = '$chart_account_id'");

                }

                else {

                    $rows = DB::select("SELECT id FROM chart_accounts WHERE code = (SELECT MAX(code) from chart_accounts WHERE code < '{$row->code}' AND level = 1)");

                    if($rows && count($rows) == 1 && $rows[0]->id)

                        DB::update("UPDATE chart_accounts SET level1_parent = '{$rows[0]->id}' WHERE id = '$chart_account_id'");

                }

            }

        }

        else {

            // update all COA rows

            $rows = DB::select("SELECT * FROM chart_accounts WHERE level = 1 ORDER BY code ASC");

            foreach($rows as $r) {

                DB::update("UPDATE chart_accounts SET level1_parent = '{$r->id}' WHERE level > 1 AND code LIKE '{$r->code}%'");

                DB::update("UPDATE chart_accounts SET level1_parent = '{$r->id}' WHERE id = '{$r->id}'");

            }

        }

    }



    // set level1_parent by parent_account_id

    public static function setLevel1Parent_old( $chart_account_id = 0 )

    {

        // If not a single account to set, then 

        // set all level 1 coa's to have a level1_parent = its ID

        // set all level 2 coa's to have a level1_parent = its level1 coa parent

        // set all level 3 coa's to have a level1_parent = its level1 coa parent

        // set all level 4 coa's to have a level1_parent = its level1 coa parent



        $additional_where = ( $chart_account_id ) ? " AND coa.id = '$chart_account_id'" : '';

        DB::update("UPDATE chart_accounts coa 

            SET coa.level1_parent = coa.id WHERE coa.`level` = 1 $additional_where");

        /* "SELECT coa.id, coa.`name`, coa.`level`, coa.level1_parent = coa.id 

        FROM chart_accounts coa 

        WHERE coa.`level` = 1;" */



        $additional_where = ( $chart_account_id ) ? " AND coa2.id = '$chart_account_id'" : '';

        DB::update("UPDATE chart_accounts coa2 

            LEFT JOIN chart_accounts coa ON coa2.parent_account_id = coa.id 

            SET coa2.level1_parent = coa.id WHERE coa2.`level` = 2 $additional_where");

        /* "SELECT coa2.id, coa2.`name`, coa2.`level`, coa.id, coa.`name`, coa.`level`, coa2.level1_parent = coa.id 

        FROM chart_accounts coa2 

        LEFT JOIN chart_accounts coa ON coa2.parent_account_id = coa.id 

        WHERE coa2.`level` = 2;" */



        $additional_where = ( $chart_account_id ) ? " AND coa3.id = '$chart_account_id'" : '';

        DB::update("UPDATE chart_accounts coa3 

            LEFT JOIN chart_accounts coa2 ON coa3.parent_account_id = coa2.id 

            LEFT JOIN chart_accounts coa ON coa2.parent_account_id = coa.id 

            SET coa3.level1_parent = coa.id WHERE coa3.`level` = 3 $additional_where");

        /* "SELECT coa3.id, coa3.`name`, coa3.`level`, coa2.id, coa2.`name`, coa2.`level`, coa.id, coa.`name`, coa.`level`, coa3.level1_parent = coa.id 

        FROM chart_accounts coa3 

        LEFT JOIN chart_accounts coa2 ON coa3.parent_account_id = coa2.id 

        LEFT JOIN chart_accounts coa ON coa2.parent_account_id = coa.id 

        WHERE coa3.`level` = 3;" */



        $additional_where = ( $chart_account_id ) ? " AND coa4.id = '$chart_account_id'" : '';

        DB::update("UPDATE chart_accounts coa4 

            LEFT JOIN chart_accounts coa3 ON coa4.parent_account_id = coa3.id 

            LEFT JOIN chart_accounts coa2 ON coa3.parent_account_id = coa2.id 

            LEFT JOIN chart_accounts coa ON coa2.parent_account_id = coa.id 

            SET coa4.level1_parent = coa.id WHERE coa4.`level` = 4 $additional_where");

    }

    public static function sales() {
        // $firstDayofYear = new Carbon\Carbon('first day of January');
        // $thisDay = Carbon\Carbon::now();

        $firstDayofYear = Carbon\Carbon::parse('01/01/2018')->format('Y-m-d h:m:s');
        $thisDay = Carbon\Carbon::parse('04/03/2018')->format('Y-m-d h:m:s');

        $month = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $charts = $sale_arr = $cost_arr = $expense_arr = $tax_arr = array();
        $s=$c=$e=$t = 0;
        $dateFrom = date('n', strtotime($firstDayofYear));
        $dateTo = date('n', strtotime($thisDay));
        //dd($dateTo);

        $sales = DB::table('vouchers')
                ->select(DB::raw('DATE_FORMAT(created_at, "%b") as month'), 
                DB::raw('IFNULL(SUM(credit), 0) as current_balance'
                )
                )
                ->where('chart_account_id','=','73')
                ->whereRaw('(credit)!=0')
                ->whereBetween('created_at',[$firstDayofYear, $thisDay])
                ->orderBy('created_at', 'asc')
                //->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'))
                ->groupBy(DB::raw('DATE_FORMAT(created_at, "%b")'))
                ->get();
        
        $costs = DB::table('chart_accounts as ca')
                ->join('vouchers as v', 'ca.id','=','v.chart_account_id')
                ->select(DB::raw('DATE_FORMAT(v.created_at, "%b") as month'),
                        DB::raw('IFNULL(SUM(debit-credit), 0) as current_balance'))
                ->whereBetween('v.created_at',[$firstDayofYear, $thisDay])
                ->where(function($q) {
                    $q->where('ca.sub_account_type_id', 7)
                      ->orWhere('ca.sub_account_type_id', 8);
                })
                ->orderBy('v.created_at', 'asc')
                ->groupBy(DB::raw('DATE_FORMAT(v.created_at, "%b")'))
                ->get();
        
        $expenses = DB::table('chart_accounts as ca')
                ->join('vouchers as v', 'ca.id','=','v.chart_account_id')
                ->select(DB::raw('DATE_FORMAT(v.created_at, "%b") as month'),
                        DB::raw('IFNULL(SUM(debit-credit), 0) as current_balance'))
                ->whereBetween('v.created_at',[$firstDayofYear, $thisDay])
                ->where('account_type_id','=',9)
                ->orderBy('v.created_at', 'asc')
                ->groupBy(DB::raw('DATE_FORMAT(v.created_at, "%b")'))
                ->get();
        
        $taxes = DB::table('chart_accounts as ca')
                ->join('vouchers as v', 'ca.id','=','v.chart_account_id')
                ->select(DB::raw('DATE_FORMAT(v.created_at, "%b") as month'),
                        DB::raw('IFNULL(SUM(debit-credit), 0) as current_balance'))
                ->whereBetween('v.created_at',[$firstDayofYear, $thisDay])
                ->where('v.tax_id','>',0)
                ->orderBy('v.created_at', 'asc')
                ->groupBy(DB::raw('DATE_FORMAT(v.created_at, "%b")'))
                ->get();
                
        for($i=$dateFrom-1;$i<$dateTo;$i++){
            
            if(!empty($sales)){
                if($s<count($sales) && $month[$i]==$sales[$s]->month) {
                    $sale_arr[] = $sales[$s]->current_balance;
                    $s++;
                }else {
                    $sale_arr[] = 0;
                }
            }else{
                $sale_arr[] = 0;
            }
            
            if(!empty($costs)) {
                if($c<count($costs) && $month[$i]==$costs[$c]->month) {
                    $cost_arr[] = $cost[$c]->current_balance;
                    $c++;
                }else {
                    $cost_arr[] = 0;
                }
            }else {
                $cost_arr[] = 0;
            }
            
            if(!empty($expenses)) {
                if($e<count($expenses) && $month[$i]==$expenses[$e]->month) {
                    $expense_arr[] = $expenses[$e]->current_balance;
                    $e++;
                }else {
                    $expense_arr[] = 0;
                }
            }else {
                $expense_arr[] = 0;
            }

            if(!empty($taxes)) {
                if($t<count($taxes) && $month[$i]==$taxes[$t]->month) {
                    $tax_arr[] = $taxes[$t]->current_balance;
                    $t++;
                }else {
                    $tax_arr[] = 0;
                }
            }else {
                $tax_arr[] = 0;
            }
        }
        
        return $charts[] = (object) [
            'sales' => $sale_arr,
            'costs' => $cost_arr,
            'expenses' => $expense_arr,
            'taxes' => $tax_arr,
        ];
        //'2018-01-01 00:00:00' AND '2018-01-31 12:59:59'
    }
}

