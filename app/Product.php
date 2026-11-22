<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    
    public $fillable = ['name', 'description', 'price', 'inventory_status', 'vendor_id', 'sr_priority', 'cost_price', 'stock_threshold', 'barcode'];

        /**
         * Active products whose remaining stock is zero or at/below their stock threshold,
         * lowest remaining quantity first.
         */
        public static function outOfStockNotices()
        {
            $products = \DB::table('products as prod')
                ->where('prod.inventory_status', 'Active')
                ->select(
                    'prod.id',
                    'prod.name',
                    'prod.stock_threshold',
                    \DB::raw('(SELECT COALESCE(SUM(inv.added_qty), 0) FROM inventories inv WHERE inv.pro_id = prod.id) as total_stock'),
                    \DB::raw('(SELECT COALESCE(SUM(poss.qty), 0) FROM p_o_s_soldstocks poss WHERE poss.product_id = prod.id) as total_soldstock')
                )
                ->orderBy('prod.name', 'ASC')
                ->get();

            $notices = [];
            foreach ($products as $product) {
                $remaining = (int) $product->total_stock - (int) $product->total_soldstock;
                if ($remaining <= 0 || $remaining <= (int) $product->stock_threshold) {
                    $notices[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'qty' => $remaining,
                        'stock_threshold' => (int) $product->stock_threshold,
                    ];
                }
            }

            usort($notices, function ($a, $b) {
                return $a['qty'] - $b['qty'];
            });

            return $notices;
        }
}