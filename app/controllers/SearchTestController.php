<?php
class SearchTestController extends Controller {

    // public function getIndex()
    // {
    //     die('aaa');
    //     $elastica = App::make('elastica');

    //     $elasticaIndex = $elastica->getIndex('pcms');

    //     // didn't define Analysis.
    //     /*
    //     $elasticaIndex->create(
    //         array(

    //         ),
    //         true
    //     );
    //     */

    //     $elasticaType = $elasticaIndex->getType('kousuke');

    //     // define Mapping
    //     /*
    //     $mapping = new \Elastica\Type\Mapping();
    //     $mapping->setType($elasticaType);

    //     $mapping->setProperties(array(
    //         'id'          => array('type' => 'integer'),
    //         'brand'       => array('type' => 'string'),
    //         'title'       => array('type' => 'string'),
    //         'description' => array('type' => 'string'),
    //         'key_feature' => array('type' => 'string'),
    //         'tag'         => array('type' => 'string'),
    //         'created_at'  => array('type' => 'date'),
    //         'updated_at'  => array('type' => 'date'),
    //     ));

    //     $mapping->send();
    //     */

    //     $id = 12345;

    //     $kousuke = array(
    //         'id'          => $id,
    //         'brand'       => 'NARUTO',
    //         'title'       => 'Uzumaki Naruto',
    //         'description' => 'Until I become Hokage, I refuse to die.',
    //         'key_feature' => "Hero's come back !!",
    //         'tag'         => 'manga, anime, comic',
    //         'created_at'  => time(),
    //         'updated_at'  => time(),
    //     );
    //     $kousukeDocument = new \Elastica\Document($id, $kousuke);

    //     $elasticaType->addDocument($kousukeDocument);

    //     $documents = array();
    //     $documents[] = new \Elastica\Document(23456, array(
    //         'id'          => 23456,
    //         'brand'       => 'Ueki',
    //         'title'       => 'Ueki no Housoku',
    //         'description' => 'The Law of Ueki',
    //         'key_feature' => "Believe in Justice.",
    //         'tag'         => 'manga, anime, comic',
    //         'created_at'  => time(),
    //         'updated_at'  => time(),
    //     ));
    //     $documents[] = new \Elastica\Document(34567, array(
    //         'id'          => 34567,
    //         'brand'       => 'Gurren Lagann',
    //         'title'       => 'Tengen Toppa Gurren Lagann',
    //         'description' => 'Who the hell do you think I am.',
    //         'key_feature' => "Believe in me who believe in you.",
    //         'tag'         => 'anime',
    //         'created_at'  => time(),
    //         'updated_at'  => time(),
    //     ));

    //     $elasticaType->addDocuments($documents);

    //     $elasticaType->getIndex()->refresh();
    // }

    // public function getInsert()
    // {
    //     die('bbb');
    // }

    public function getUpdateProduct()
    {
        $products = Product::where('status', 'publish')->where('active', 1)->get();

        foreach ($products as $key=>$product)
        {
            ElasticUtils::updateProduct($product);
            echo "Update Product - {$product->id}<br>";
        }

        $count = count($products);
        echo "<h1>{$count} products UPDATE !</h1>";
    }

    public function getRebuild($index = 'itruemart', $type = 'products')
    {
        $apps = PApp::all();
        $type = 'products';

        foreach ($apps as $app)
        {
            $index = $app->slug;

            try
            {
                // Delete $index
                $path = 'http://' . Request::server('HTTP_HOST') . ":9200/{$index}/{$type}";
                // API::delete("http://pcms-true.igetapp.com:9200/{$index}/{$type}");
                API::delete($path, array());
            }
            catch(Exception $e)
            {

            }

            echo "<p>delete index - {$index}</p>";

            // Create Mapping
            API::get("api-search/indexing/{$index}/{$type}", array());

            echo "<p>Create Mapping index - {$index}</p>";
        }

        // Update Product
        $this->getUpdateProduct();

        // echo 'rebuild.';
    }


    public function getAsdf($id)
    {
        /*
        $product = Product::find($id);

        $product->created_at = date('Y-m-d H:i:s');
        $product->save();
        */

        $app = PApp::find(1);

        PApp::setCurrentApp($app);

        $product = Product::find($id);

        // d($product->pkey); die();

        $pRepo = new ProductRepository;
        $rs = $pRepo->getProductByPkey($product->pkey);

        d($rs);
    }

    public function getTranslate($id)
    {
        $product = Product::find($id);

        // d($product->translate);
        d($product, $product->translate()->toArray());
    }

}