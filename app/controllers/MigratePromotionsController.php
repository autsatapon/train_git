<?php

// set_time_limit(0);
//ini_set('memory_limit', '500MB');

use Carbon\Carbon;

class MigratePromotionsController extends BaseController {

    public function getIndex()
    {





        //sd(DB::getQueryLog());
    }

    public function getImportExcelPromotionCode()
    {
        $number = Input::get('file', 1);
        $pathFile = "./20140515-excel-migrate-itruemart/Promotion code_{$number}.xlsx";

        $column = array(
            'A' => 'discount_group_id',
            'B' => 'code',
            'C' => 'count',
            'D' => 'angpao_used',
            'E' => 'sso_id',
        );

        $model = new MigratePromotionCode;

        migrateImportExcel($pathFile, $column, $model);

    }

    public function getImportExcelPromotionGroup()
    {

        $pathFile = "./20140515-excel-migrate-itruemart/Promotion group.xlsx";

        $column = array(
            'A' => 'discount_group_id',
            'B' => 'name',
            'C' => 'promotion_type',
            'D' => 'type',
            'E' => 'group_type',
            'F' => 'amount',
            'G' => 'type_amount',
            'H' => 'price_minimum',
            'I' => 'price_minimum_type',
            'J' => 'code_amount',
            'K' => 'activated',
            'L' => 'angpao_status',
            'M' => 'started_date',
            'N' => 'ended_date',
            'O' => 'created_date',
            'P' => 'updated_date',
        );

        $model = new MigratePromotionGroup;

        migrateImportExcel($pathFile, $column, $model);
    }

    public function getImportExcelPromotionItem()
    {
        $pathFile = "./20140515-excel-migrate-itruemart/Promotion item.xlsx";

        $column = array(
            'A' => 'discount_group_id',
            'B' => 'item_id',
        );

        $model = new MigratePromotionItem;

        migrateImportExcel($pathFile, $column, $model);

        $items = MigratePromotionItem::all();

        foreach ($items as $key => $item) {
            $group = MigratePromotionGroup::whereDiscountGroupId($item->discount_group_id)->first();

            if ($group && $group->group_type != 'all')
            {
                $item->item_type = $group->group_type;
                $item->save();
            }
        }

    }

    public function getImportExcelPromotionUsed()
    {
        $pathFile = "./20140515-excel-migrate-itruemart/Promotion used.xlsx";

        $column = array(
            'A' => 'discount_group_id',
            'B' => 'code',
            'C' => 'sso_id',
            'D' => 'order_id',
            'E' => 'product_id',
            'F' => 'inventory_id',
            'G' => 'discount_price',
            'H' => 'used_date',
            'I' => 'used_status',
            'J' => 'updated_date',
        );

        $model = new MigratePromotionUsed;

        migrateImportExcel($pathFile, $column, $model);

    }

    public function getMigratePromotion()
    {
        $campaign = new Campaign;
        $campaign->app_id = 1;
        $campaign->name = "Old iTruemart";
        $campaign->status = "activate";
        $campaign->budget = 0;
        $campaign->save();

        // sd($campaign);

        MigratePromotionGroup::setCampaignId($campaign->getKey());

        $groups = MigratePromotionGroup::get();

        $start = Carbon::now();
        $end = Carbon::now();

        $groups->each(function($model) use (&$start, &$end) {

            try
            {
                $attributes = array_except($model->toArray(), array('discount_group_id', 'migrate_status', 'error'));
                $promotion = new Promotion;
                foreach ($attributes as $field => $attribute) {
                    $promotion->{$field} = $attribute;
                }
                $promotion->save();
            }
            catch (Exception $e)
            {
                echo $e->getMessage();

                $model->migrate_status = 0;
                $model->error = $e->getMessage();
                $model->save();
            }

            if ($start->getTimestamp() > strtotime($promotion->start_date))
            {
                $start = new Carbon(strtodate("datetime", $promotion->start_date));
            }

            if ($end->getTimestamp() < strtotime($promotion->end_date))
            {
                $end = new Carbon(strtodate("datetime", $promotion->end_date));
            }

            $model->migrate_status = 1;
            $model->error = '';
            $model->save();

            DB::table('promotion_maps')->insert(
                array(
                    'itruemart_id' => $model->discount_group_id,
                    'pcms_id' => $promotion->id,
                    'pkey' => $promotion->pkey,
                )
            );

        });

        $campaign->start_date = strtodate('datetime', $start->getTimestamp());
        $campaign->end_date = strtodate('datetime', $end->getTimestamp());
        $campaign->save();

        return "55555";
    }

    public function getMigratePromotionCode()
    {
        $lot = Input::get('lot', 0);

        $lotSize = 2000;

        $skip = ($lot * $lotSize);

        $codes = MigratePromotionCode::skip($skip)->take($lotSize)->get();

        if ($codes->count() == 0)
        {
            return "Finish!!!";
        }

        $codes->each(function($model) {

            try
            {
                $attributes = array_except($model->toArray(), array('discount_group_id', 'migrate_status', 'error'));

                $promotionCode = new PromotionCode;
                foreach ($attributes as $field => $attribute) {
                    $promotionCode->{$field} = $attribute;
                }
                $promotionCode->save();
            }
            catch (Exception $e)
            {
                echo $e->getMessage();

                $model->migrate_status = 0;
                $model->error = $e->getMessage();
                $model->save();
                exit;
            }

            $model->migrate_status = 1;
            $model->error = '';
            $model->save();

            DB::table('promotion_code_maps')->insert(
                array(
                    'itruemart_id' => $model->id,
                    'pcms_id' => $promotionCode->id,
                    'code' => $model->code
                )
            );

        });

        $url = URL::current().'?lot='.($lot+1);

        $this->writeMetaRefresh($url);

        return "Lot: ".$lot;
    }

    public function getMigratePromotionUsed()
    {
        $lot = Input::get('lot', 0);

        $lotSize = 2000;

        $skip = ($lot * $lotSize);

        $codes = MigratePromotionUsed::skip($skip)->take($lotSize)->get();

        if ($codes->count() == 0)
        {
            return "Finish!!!";
        }

        $codes->each(function($model) {

            try
            {
                $attributes = array_except($model->toArray(), array('discount_group_id', 'migrate_status', 'error'));

                $promotionCodeLog = new PromotionCodeLog;
                foreach ($attributes as $field => $attribute) {
                    $promotionCodeLog->{$field} = $attribute;
                }
                $promotionCodeLog->save();
            }
            catch (Exception $e)
            {
                echo $e->getMessage();

                $model->migrate_status = 0;
                $model->error = $e->getMessage();
                $model->save();
            }

            $model->migrate_status = 1;
            $model->error = '';
            $model->save();

        });

        $url = URL::current().'?lot='.($lot+1);

        $this->writeMetaRefresh($url);

        return "Lot: ".$lot;
    }

    public function getPromotion()
    {
        $groups = MigratePromotionGroup::take(10)->get();

        s($groups->toArray());
    }

    public function getPromotionCode()
    {
        $codes = MigratePromotionCode::take(10)->get();

        s($codes->toArray());
    }

    public function getPromotionUsed()
    {
        $codes = MigratePromotionUsed::take(10)->get();

        s($codes->toArray());
    }

    private function writeMetaRefresh($url, $second = 5)
    {
        echo '<meta http-equiv="refresh" content="'.$second.';URL='.$url.'" />';
    }

}