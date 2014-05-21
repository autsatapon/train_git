<?php

class MigrateFlashsaleController extends BaseController {

    public function getDiscounts()
    {
        $raws = DB::table('raw_discount_product')->where('migrate_status', 'no')->get();
        d(count($raws));

        foreach ($raws as $key => $value)
        {
            try
            {
                $pcmsProductId = DB::table('product_maps')->where('itruemart_id', $value->product_id)->pluck('pcms_id');
                $product = Product::find($pcmsProductId);

                $pcmsCampaignId = DB::table('campaign_maps')->where('itruemart_id', $value->discount_campaign_id)->pluck('pcms_id');

                if ( empty($product) )
                {
                    echo 'Empty Product ...';
                    d($value->product_id, $pcmsProductId);
                    continue;
                }

                // $variants = $product->variants;

                // if ( $variants->isEmpty() )
                // {
                //     echo 'Empty Variant ...';
                //     d($product->variants);
                //     continue;
                // }

                $variants = DB::table('variants')->where('product_id', $pcmsProductId)->get();

                if ( empty($variants) )
                {
                    echo 'Empty Variant ...';
                    continue;
                }

                foreach ($variants as $variant)
                {
                    $specialDiscount = new SpecialDiscount;
                    $specialDiscount->app_id = 1;
                    if ($value->campaign_type == 'Flash Sale')
                    {
                        $specialDiscount->campaign_type = 'flash_sale';
                    }
                    elseif ($value->campaign_type == 'Today Special')
                    {
                        $specialDiscount->campaign_type = 'itruemart_tv';
                    }
                    else
                    {
                        $specialDiscount->campaign_type = 'on_sale';
                    }
                    $specialDiscount->discount_campaign_id = $pcmsCampaignId;
                    $specialDiscount->variant_id = $variant->id;
                    $specialDiscount->inventory_id = $variant->inventory_id;
                    $specialDiscount->discount = $value->discount;
                    $specialDiscount->discount_type = ( $value->discount_type == 'Percent' ) ? 'percent' : 'price' ;
                    $specialDiscount->started_at = $value->started_at;
                    $specialDiscount->ended_at = $value->ended_at;

                    /*
                     * Calculate discount_price
                     */
                    $price = ($variant->normal_price > 0) ? $variant->normal_price : $variant->price ;
                    if ($specialDiscount->discount_type == 'price')
                    {
                        // Price
                        // $specialDiscount->discount_price = $variant->price - $specialDiscount->discount;
                        $specialDiscount->discount_price = $price - $specialDiscount->discount;
                    }
                    else
                    {
                        // Percent
                        // $specialDiscount->discount_price = floor( $variant->price * (100 - $specialDiscount->discount) / 100 );
                        $specialDiscount->discount_price = floor( $price * (100 - $specialDiscount->discount) / 100 );
                    }

                    $specialDiscount->save();

                    DB::table('raw_discount_product')->where('id', $value->id)->update( array('migrate_status' => 'yes') );
                }

                ElasticUtils::updateProduct($product);
            }
            catch (Exception $e)
            {
                d($value, $e);
                DB::table('raw_discount_product')->where('id', $value->id)->update( array('migrate_status' => 'error') );
            }
        }

        echo 'migrate complete.';
    }

    public function getCampaigns()
    {
        $raws = DB::table('raw_discount_campaign')->where('migrate_status', 'no')->whereIn('discount_type', array('Money', 'Percent'))->get();

        foreach ($raws as $key => $value)
        {
            try
            {
                $discountCampaign = new DiscountCampaign;
                $discountCampaign->app_id = 1;
                if ($value->type == 'Flash Sale')
                {
                    $discountCampaign->type = 'flash_sale';
                }
                elseif ($value->type == 'Today Special')
                {
                    $discountCampaign->type = 'itruemart_tv';
                }
                else
                {
                    $discountCampaign->type = 'on_sale';
                }
                $discountCampaign->code = $discountCampaign->type . '_' . $value->campaign_id;
                $discountCampaign->name = $value->name;
                $discountCampaign->description = $value->description;
                $discountCampaign->note = $value->note;
                $discountCampaign->discount = $value->discount;
                $discountCampaign->discount_type = ( $value->discount_type == 'Percent' ) ? 'percent' : 'price' ;
                $discountCampaign->started_at = $value->started_date;
                $discountCampaign->ended_at = $value->ended_date;
                $discountCampaign->active = ( $value->active == 'Y' ) ? '1' : '0' ;

                $discountCampaign->save();

                DB::table('campaign_maps')->insert(
                    array(
                        'itruemart_id' => $value->campaign_id,
                        'pcms_id' => $discountCampaign->id,
                        'pkey' => $discountCampaign->pkey,
                    )
                );

                DB::table('raw_discount_campaign')->where('id', $value->id)->update( array('migrate_status' => 'yes') );
            }
            catch (Exception $e)
            {
                d($e);
            }
        }

        echo 'migrate complete.';
    }

    public function getImportCampaign()
    {
        $pathFile = "./20140515-excel-migrate-itruemart/discount_campaign.xlsx";
        $objPHPExcel = PHPExcel_IOFactory::load($pathFile);

        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        unset($sheetData[1]);

        $records = array();
        foreach ($sheetData as $v)
        {
            $records[] = array(
                'campaign_id'    => $v['A'],
                'app_id'         => $v['B'],
                'type'           => $v['C'],
                'name'           => $v['D'],
                'description'    => $v['E'],
                'note'           => $v['F'],
                'discount'       => $v['G'],
                'discount_type'  => $v['H'],
                'started_date'   => $v['I'],
                'ended_date'     => $v['J'],
                'active'         => $v['K'],
                'campaign_map'   => $v['L'],
                'migrate_status' => 'no'
            );
        }

        DB::table('raw_discount_campaign')->insert($records);

        echo "Import Data from Excel - Complete";
        echo "<br> Path file - {$pathFile}";
    }

    public function getImportDiscount()
    {
        $pathFile = "./20140515-excel-migrate-itruemart/discount_product.xlsx";
        $objPHPExcel = PHPExcel_IOFactory::load($pathFile);

        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        unset($sheetData[1]);

        $records = array();
        foreach ($sheetData as $v)
        {
            $records[] = array(
                'app_id'               => $v['A'],
                'campaign_type'        => $v['B'],
                'discount_campaign_id' => $v['C'],
                'product_id'           => $v['D'],
                'discount_price'       => $v['E'],
                'discount'             => $v['F'],
                'discount_margin'      => $v['G'],
                'discount_type'        => $v['H'],
                'started_at'           => $v['I'],
                'ended_at'             => $v['J'],
                'migrate_status'       => 'no'
            );
        }

        DB::table('raw_discount_product')->insert($records);

        echo "Import Data from Excel - Complete";
        echo "<br> Path file - {$pathFile}";
    }

}