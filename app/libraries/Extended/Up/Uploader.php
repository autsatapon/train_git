<?php namespace Extended\Up;

use Teepluss\Up\Uploader as BaseUploader;

class Uploader extends BaseUploader {

	protected function doTransfer($url, $path)
    {
        // Craete upload structure directory.
        if ( ! is_dir($path))
        {
            mkdir($path, 0777, true);
        }

        // Original name.
        $origName = basename($url);

        // Generate a file name with extension.
        $filename = $this->name($url);

        // HARD CODE - for Migrate Product Itruemart
        if ( preg_match('!cdn.itruemart.com!', $url) )
        {
            $url = str_replace('cdn.itruemart.com', '203.144.214.70', $url);
        }

        // Get file binary.
        $ch = curl_init();

        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,120);
        curl_setopt ($ch, CURLOPT_TIMEOUT,120);

        // HARD CODE - for Migrate Product Itruemart
        if ( preg_match('!203.144.214.70!', $url) )
        {
            curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Host: cdn.itruemart.com'));
        }

        // Response returned.
        $bin = curl_exec($ch);

        $info = curl_getinfo($ch);

        curl_close($ch);

        // Path to write file.
        $uploadPath = $path.$filename;

        $f = @fopen($uploadPath, 'w');

        if (!$f)
        {
            return false;
        }

        if ($bytes = fwrite($f, $bin))
        {
        	$results = $this->results($uploadPath);
        }

        fclose($f);

        return (isset($results)) ? $results : false;
    }

}