<?php
class LogController extends BaseController{
    public function viewLog(){
        $logFile = 'laravel.txt';
        $file = storage_path() . '/logs/' . $logFile;
        //Log::info('test from LogController');
        if(!is_readable($file)){
            // try to write an empty file
            file_put_contents($file, '');
            return 'Cannot read the file ' . $file;
        }
        $content = file_get_contents($file);
        sd($content);
        return $content;
    }
}

?>