<?php

use Illuminate\Database\Eloquent\Model as Model;

function migrateImportExcel($pathFile, Array $columnNames, Model $model, $noStrict = false)
{
    $model::truncate();
    $objPHPExcel = PHPExcel_IOFactory::load($pathFile);

    $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

    if ($noStrict == false)
    {
        foreach ($columnNames as $column => $name)
        {
            if (array_get($sheetData, "1.{$column}") != $name)
            {
                throw new Exception("First column name isn't match. Please check excel again before migrate.");
            }
        }
    }


    unset($sheetData[1]);
    foreach ($sheetData as $row)
    {
        $modelInstance = $model->newInstance();
        foreach ($columnNames as $column => $name)
        {
            $name = str_replace(' ', '_', $name);
            $modelInstance->$name = $row[$column];
        }

        if (! $modelInstance->save())
        {
            throw new Exception("Can't save to model. Please review excel and coding.");
        }
    }

    echo "Import to ".get_class($model)." success.<br>";
}