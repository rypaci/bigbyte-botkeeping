<?php

use Illuminate\Database\Migrations\Migration;

class BackfillPosExpenseItemsFromDescriptions extends Migration
{
    const REMARK = 'Auto-assigned from description';

    // First matching rule wins; product names must match products.name (trimmed, case-insensitive).
    private function rules()
    {
        return [
            ['Crab Seminar', '/seminar|speaker/i'],
            ['Vertical Crab Farming Manual', '/manual/i'],
            ['Crab Paste Original', '/paste|\bjar\b|sealer/i'],
            ['Lobster', '/lobster/i'],
            ['Durian', '/durian|durain|pomelo/i'],
            ['Orobiotics Nutrifying Odor Fixing Probiotics', '/orobiotix|orobiotics|probiotics/i'],
            ['Protein Skimmer', '/^(protein )?skimmer|jebo/i'],
            ['Marine Salt', '/\bsalt\b(?!\s*water)|sea salt/i'],
            ['1 pc Filter Bag', '/filter\s*(bag|sock)s?|filer socks|mesh (filter )?bag|reusable/i'],
            ['K1 Media', '/\bk1\b/i'],
            ['Submersible Pump A6500', '/a-?6500|submersible/i'],
            ['Sticker', '/sticker/i'],
            ['Printing', '/printing|print materials|\bink\b/i'],
            ['BigByte Keychain', '/keychain/i'],
            ['Fish Net', '/^net$|fish net|big net/i'],
            ['Fish', '/^fish$|purchase fish|fish box/i'],
            ['Crab Tub', '/microwa?vable/i'],
            ['1bottle EMAS', '/\bem[- ]?1\b|\bemas\b/i'],
            ['Vertical Crab Setup', '/set ?up|\bras\b|frame|crab ?farm|sump|aquarium|piping|pipe|\bblue\b|pvc|coupling|union|elbow|\btee\b|solvent|teflon|sandflex|adapt[eo]r|ball valve|fitting|gro+m+et|uniseal|drill|bolt|flat ?cord|cable tie|outlet|utility box|breaker|omni|weather|\bssr\b|foam|laton|lapon|woodtek|lamtex|gravel|\bsand\b|pebbles|oyster|lava|ceramic|filter brush|turkey|ammo(lock|nia)|quick start|\buv\b|heater|thermo|refractometer|test ?kit|ph (tester|meter)|eel clip|impeller|air ?pump|water pump|head ?lamp|\bdrum|plywood|carbon|accessor|moulding|molding|epoxy|sealant|expansion|installation|hacksaw|rivet|hinges|screw|\bplug|tie box|wires|storage box|potassium|permanganate|pormaganate|anti ?chlorin|calcium|distilled/i'],
            ['Crabs', '/crab|steamed|tamban/i'],
            ['Vertical Crab Setup', '/\btao\b|dela raiz|peralta|garces|salva|arcenal|latog|tareq|muharrani|moharrani|tugbok|zamboanga|cabadbaran|castillo/i'],
            ['Shipping fee', '/shipping|freight|cargo|delivery|trucking|courier|\blbc\b|j&t|airplane|port expenses|pickup/i'],
        ];
    }

    public function up()
    {
        if (!Schema::hasTable('pos_expense_items')) {
            return;
        }

        $productIds = [];
        foreach (DB::table('products')->select('id', 'name')->get() as $product) {
            $productIds[strtolower(trim($product->name))] = $product->id;
        }

        $rules = [];
        foreach ($this->rules() as $rule) {
            $key = strtolower($rule[0]);
            if (isset($productIds[$key])) {
                $rules[] = [$productIds[$key], $rule[1]];
            }
        }

        $now = date('Y-m-d H:i:s');

        $expenses = DB::table('p_o_s_expenses')
            ->whereNotIn('id', function ($q) {
                $q->select('pos_expense_id')->from('pos_expense_items');
            })
            ->select('id', 'description', 'amount')
            ->get();

        $rows = [];
        foreach ($expenses as $expense) {
            $description = trim($expense->description);
            foreach ($rules as $rule) {
                if (preg_match($rule[1], $description)) {
                    $rows[] = [
                        'pos_expense_id' => $expense->id,
                        'product_id' => $rule[0],
                        'quantity' => 1,
                        'cost_price' => $expense->amount,
                        'total_price' => $expense->amount,
                        'remarks' => self::REMARK,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    break;
                }
            }
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('pos_expense_items')->insert($chunk);
        }
    }

    public function down()
    {
        if (Schema::hasTable('pos_expense_items')) {
            DB::table('pos_expense_items')->where('remarks', self::REMARK)->delete();
        }
    }
}
