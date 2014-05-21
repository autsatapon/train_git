<?php

class MigrateShopsController extends BaseController {

    public function getImport()
    {
        $pathFile = "./20140515-excel-migrate-itruemart/Shop.xlsx";

        $column = array(
            'A' => 'shop_id',
            'B' => 'shop_name',
        );

        $model = new MigratedShop;

        migrateImportExcel($pathFile, $column, $model, true);

    }

    public function getMigrate()
    {
        $query = MigratedShop::where('migrate_status', 0);
        $migratedShop = $query->get();

        $migratedShop->each(function($ms) // use (&$shops)
        {
            if ( ! Shop::where('shop_id', $ms->shop_id)->update(array('name' => $ms->shop_name)))
            {
                $shop = new Shop;

                $shop->shop_id = $ms->shop_id;
                $shop->name = $ms->shop_name;

                $shop->save();
            }
        });

        $query->update(array('migrate_status' => 1));
    }

}