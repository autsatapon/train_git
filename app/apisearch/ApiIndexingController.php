<?php

// http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/indices-put-mapping.html

use Elastica\Client as ElasticaClient;

class ApiIndexingController extends \BaseController {

    protected $client;

    public function __construct(ElasticaClient $client)
    {
        $this->client = $client;

        // Set Config for Elastica Client
        $this->client->setConfig(array(
            'host' => Config::get('elastica.host'),
            'port' => Config::get('elastica.port')
        ));

        $this->client->getConnection()->setHost(Config::get('elastica.host'))->setPort(Config::get('elastica.port'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($indexname)
    {
        $inputs = array(
            'index' => $indexname
        );

        $validator = Validator::make($inputs, array(
            'index' => 'required|alpha_num'
        ));

        if ($validator->fails())
        {
            $errors = $validator->messages()->all(':message');

            return API::createResponse(compact('errors'), 400);
        }

        $default = array(
            'index' => array(
                'number_of_shards'   => 1,
                'number_of_replicas' => 0
            )
        );

        $index = $this->client->getIndex($indexname);
        $index->create($default, true);

        $indexing = array(
            'index' => $indexname
        );

        return API::createResponse($indexing, 201);
    }

    public function map($indexname, $type)
    {
        $index = $this->client->getIndex($indexname);

        // Create a new type.
        $elasticaType = $index->getType($type);

        // Define mapping
        $mapping = new \Elastica\Type\Mapping();
        $mapping->setType($elasticaType);
        $mapping->setParam('analyzer', 'thai');
        //$mapping->setParam('_source', array('enabled' => false));

        $mappingProperties = $this->getMappingProperties($type);

        $mapping->setProperties($mappingProperties);

        $mapping->send();

        $indexing = array(
            'index' => $indexname
        );

        return API::createResponse($indexing, 201);
    }

    private function getMappingProperties($type)
    {
        if ($type == 'brands')
        {
            $mappingProperties = array();
        }
        elseif ($type == 'collections')
        {
            $mappingProperties = array();
        }
        else
        {
            /* ========== $type == 'products' ========== */
            $mappingProperties = array(
                "id" => array(
                    "type" => "integer"
                ),
                "pkey" => array(
                    "type" => "string",
                    "index" => "not_analyzed"
                ),
                "collections" => array(
                    "properties" => array(
                        "pkey" => array(
                            "type" => "string",
                            "index" => "not_analyzed"
                        ),
                        "name" => array(
                            "type" => "string",
                            "index" => "no"
                        )
                    )
                ),
                "brand" => array(
                    "properties" => array(
                        "pkey" => array(
                            "type" => "string",
                            "index" => "not_analyzed"
                        ),
                        "name" => array(
                            "type" => "string",
                        )
                    )
                ),
                "title" => array(
                    "type" => "string",
                    "analyzer" => "thai"
                ),
                "slug" => array(
                    "type" => "string",
                    "index" => "not_analyzed"
                ),
                "description" => array(
                    "type" => "string",
                    "store" => "yes",
                    "index" => "not_analyzed"
                ),
                "key_feature" => array(
                    "type" => "string",
                    "store" => "yes",
                    "index" => "not_analyzed"
                ),
                "tag" => array(
                    "type" => "string",
                    "analyzer" => "thai"
                ),
                "installment" => array(
                    "type" => "object",
                    "index" => "no"
                ),
                "has_variants" => array(
                    "type" => "integer",
                    "index" => "no"
                ),
                "variants" => array(
                    "properties" => array(
                        "pkey" => array(
                            "type" => "string",
                            "index" => "not_analyzed"
                        ),
                        "inventory_id" => array(
                            "type" => "string",
                            "index" => "not_analyzed"
                        ),
                        "title" => array(
                            "type" => "string",
                            "index" => "not_analyzed"
                        ),
                        "normal_price" => array(
                            "type" => "float",
                            "index" => "not_analyzed"
                        ),
                        "price" => array(
                            "type" => "float",
                            "index" => "not_analyzed"
                        ),
                        "unit_type" => array(
                            "type" => "string",
                            "index" => "no"
                        ),
                        "installment" => array(
                            "type" => "object",
                            "index" => "no"
                        ),
                        "active_special_discount" => array(
                            "properties" => array(
                                "campaign_type" => array(
                                    "type" => "string",
                                    "index" => "not_analyzed"
                                ),
                                "discount_price" => array(
                                    "type" => "float",
                                    "index" => "not_analyzed"
                                ),
                                "discount" => array(
                                    "type" => "float",
                                    "index" => "not_analyzed"
                                ),
                                "discount_type" => array(
                                    "type" => "string",
                                    "index" => "not_analyzed"
                                ),
                                "started_at" => array(
                                    "type" => "date",
                                    "index" => "not_analyzed"
                                ),
                                "ended_at" => array(
                                    "type" => "date",
                                    "index" => "not_analyzed"
                                )
                            )
                        ),
                        "active_trueyou_discont" => array(
                            "type" => "string",
                            "index" => "no"
                        ),
                        "net_price" => array(
                            "type" => "float",
                        ),
                        "special_price" => array(
                            "type" => "float",
                        ),
                        "media_contents" => array(
                            "properties" => array(
                                "mode" => array(
                                    "type" => "string"
                                ),
                                "url" => array(
                                    "type" => "string",
                                    "index" => "no"
                                ),
                            )
                        ),
                    )
                ),
                /*
                "policies"    => array(),
                "style_types" => array(),
                "translate"   => array(),
                */
                "image_cover" => array(
                    "properties" => array(
                        "normal" => array(
                            "type" => "string",
                            "store" => "yes",
                            "index" => "no"
                        ),
                        "thumbnails" => array(
                            "properties" => array(
                                "small" => array(
                                    "type" => "string",
                                    "store" => "yes",
                                    "index" => "no"
                                ),
                                "medium" => array(
                                    "type" => "string",
                                    "store" => "yes",
                                    "index" => "no"
                                ),
                                "square" => array(
                                    "type" => "string",
                                    "store" => "yes",
                                    "index" => "no"
                                ),
                                "large" => array(
                                    "type" => "string",
                                    "store" => "yes",
                                    "index" => "no"
                                ),
                                "zoom" => array(
                                    "type" => "string",
                                    "store" => "yes",
                                    "index" => "no"
                                )
                            )
                        )
                    )
                ),
                "media_contents" => array(
                    "properties" => array(
                        "mode" => array(
                            "type" => "string",
                            "index" => "no"
                        ),
                        "url" => array(
                            "type" => "string",
                            "index" => "no"
                        ),
                        "thumb" => array(
                            "properties" => array(
                                "normal" => array(
                                    "type" => "string",
                                    "index" => "no"
                                ),
                                "thumbnails" => array(
                                    "properties" => array(
                                        "small" => array(
                                            "type" => "string",
                                            "store" => "yes",
                                            "index" => "no"
                                        ),
                                        "medium" => array(
                                            "type" => "string",
                                            "store" => "yes",
                                            "index" => "no"
                                        ),
                                        "square" => array(
                                            "type" => "string",
                                            "store" => "yes",
                                            "index" => "no"
                                        ),
                                        "large" => array(
                                            "type" => "string",
                                            "store" => "yes",
                                            "index" => "no"
                                        ),
                                        "zoom" => array(
                                            "type" => "string",
                                            "store" => "yes",
                                            "index" => "no"
                                        )
                                    )
                                )
                            )
                        )
                    )
                ),
                "price_range" => array(
                    "properties" => array(
                        "max" => array(
                            "type" => "float",
                            "store" => "yes",
                            // "index" => "no"
                        ),
                        "min" => array(
                            "type" => "float",
                            "store" => "yes",
                            // "index" => "no"
                        )
                    )
                ),
                "net_price_range" => array(
                    "properties" => array(
                        "max" => array(
                            "type" => "float",
                            "store" => "yes",
                            "index" => "no"
                        ),
                        "min" => array(
                            "type" => "float",
                            "store" => "yes",
                            "index" => "no"
                        )
                    )
                ),
                "special_price_range" => array(
                    "properties" => array(
                        "max" => array(
                            "type" => "float",
                            "store" => "yes",
                            "index" => "no"
                        ),
                        "min" => array(
                            "type" => "float",
                            "store" => "yes",
                            "index" => "no"
                        )
                    )
                ),
                "percent_discount" => array(
                    "properties" => array(
                        "max" => array(
                            "type" => "float",
                            "store" => "yes",
                            // "index" => "no"
                        ),
                        "min" => array(
                            "type" => "float",
                            "store" => "yes",
                            // "index" => "no"
                        )
                    )
                ),
                "published_at" => array(
                    "type" => "date",
                    "index" => "not_analyzed"
                ),
                "created_at" => array(
                    "type" => "date",
                    "index" => "not_analyzed"
                ),
                "updated_at" => array(
                    "type" => "date",
                    "index" => "not_analyzed"
                )
            );
        }

        return $mappingProperties;
    }

}