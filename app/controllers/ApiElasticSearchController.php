<?php

class ApiElasticSearchController extends ApiBaseController {

    public function anyRebuild($app)
    {
        $index = $app->slug;
        $type = 'products';

        // d(Config::get('elastica.host'), Config::get('elastica.port'));
        $host = Config::get('elastica.host');
        $port = Config::get('elastica.port');

        try
        {
            // Delete type products
            $path = "http://{$host}:{$port}/{$index}/{$type}";
            API::delete($path, array());
        }
        catch(Exception $e)
        {

        }

        echo "delete index - {$index} <br>\r\n";

        // ReCreate Mapping
        API::get("api-search/indexing/{$index}/{$type}", array());

        echo "Create Mapping index - {$index} <br>\r\n";

        // Update Product
        $this->updateProduct();
    }

    private function updateProduct()
    {
        $products = Product::where('status', 'publish')->where('active', 1)->get();

        $i = 0;
        foreach ($products as $key=>$product)
        {
            ElasticUtils::updateProduct($product);
            echo "Update Product - {$product->id} <br>\r\n";
            $i++;
        }

        echo "{$i} products UPDATE !\r\n";
    }

}

