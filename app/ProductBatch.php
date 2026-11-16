<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    // Batches are flagged this many months before their expiry date.
    const EXPIRY_WARNING_MONTHS = 3;

    protected $table = 'product_batches';

    public $fillable = ['product_id', 'inventory_id', 'batch_code', 'original_qty', 'expiry_date', 'mfg_date', 'received_date', 'note'];

    public static function warningDate()
    {
        return date('Y-m-d', strtotime('+'.self::EXPIRY_WARNING_MONTHS.' months'));
    }

    public static function statusFor($expiryDate)
    {
        if (!$expiryDate) {
            return 'Good';
        }
        if ($expiryDate < date('Y-m-d')) {
            return 'Expired';
        }
        if ($expiryDate <= self::warningDate()) {
            return 'Expiring Soon';
        }
        return 'Good';
    }

    /**
     * Batches of a product in FEFO order with their remaining quantity.
     * Stock added before batches existed is treated as the oldest and sold first,
     * then sales are allocated to batches in FEFO order (first expiry, first out).
     */
    public static function stockFor($productId)
    {
        $added = (int) DB::table('inventories')->where('pro_id', $productId)->sum('added_qty');
        $sold = (int) DB::table('p_o_s_soldstocks')->where('product_id', $productId)->sum('qty');

        $batches = self::where('product_id', $productId)
            ->orderByRaw('expiry_date IS NULL, expiry_date ASC, received_date ASC, id ASC')
            ->get();

        $legacy = max(0, $added - (int) $batches->sum('original_qty'));
        $toAllocate = max(0, $sold - $legacy);

        $rows = [];
        foreach ($batches as $batch) {
            $take = min($batch->original_qty, $toAllocate);
            $toAllocate -= $take;

            $rows[] = [
                'batch_code' => $batch->batch_code,
                'qty' => $batch->original_qty - $take,
                'original_qty' => $batch->original_qty,
                'expiry_date' => $batch->expiry_date,
                'mfg_date' => $batch->mfg_date,
                'status' => self::statusFor($batch->expiry_date),
                'received_date' => $batch->received_date,
            ];
        }

        return ['current_qty' => $added - $sold, 'batches' => $rows];
    }

    /**
     * Batches of active products that still have stock and are expired or
     * within the warning window, soonest expiry first.
     */
    public static function expiringNotices()
    {
        $products = DB::table('product_batches as pb')
            ->join('products as prod', 'prod.id', '=', 'pb.product_id')
            ->where('prod.inventory_status', 'Active')
            ->whereNotNull('pb.expiry_date')
            ->where('pb.expiry_date', '<=', self::warningDate())
            ->groupBy('prod.id', 'prod.name')
            ->select('prod.id', 'prod.name')
            ->get();

        $notices = [];
        foreach ($products as $product) {
            $stock = self::stockFor($product->id);
            foreach ($stock['batches'] as $batch) {
                if ($batch['qty'] > 0 && $batch['status'] !== 'Good') {
                    $batch['product_id'] = $product->id;
                    $batch['product_name'] = $product->name;
                    $batch['days_left'] = (int) floor((strtotime($batch['expiry_date']) - strtotime(date('Y-m-d'))) / 86400);
                    $notices[] = $batch;
                }
            }
        }

        usort($notices, function ($a, $b) {
            return strcmp($a['expiry_date'], $b['expiry_date']);
        });

        return $notices;
    }
}
