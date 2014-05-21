<?php

use Elastica\Query as ElasticQuery;
use Elastica\Client as ElasticaClient;
use Elastica\Document as ElasticDocument;
use Elastica\Query\Text as ElasticQueryText;
use Elastica\Query\Bool as ElasticQueryBool;
use Elastica\Filter\Term as ElasticaFilterTerm;
use Elastica\Filter\Range as ElasticaFilterRange;
use Elastica\Filter\Exists as ElasticaFilterExists;
use Elastica\Filter\Bool as ElasticaFilterBool;
use Elastica\Query\HasChild as ElasticQueryHasChild;
use Elastica\Query\QueryString as ElasticaQueryString;

class ApiSearchController extends \BaseController {

    protected $query;

    protected $client;

    protected $document;

    protected $queryText;

    protected $queryBool;

    protected $filterTerm;

    protected $filterRange;

    protected $filterBool;

    protected $queryString;

    protected $queryHasChild;

    public function __construct(
        ElasticQuery $query,
        ElasticaClient $client,
        ElasticDocument $document,
        ElasticQueryText $queryText,
        ElasticQueryBool $queryBool,
        ElasticaFilterTerm $filterTerm,
        ElasticaFilterRange $filterRange,
        ElasticaFilterBool $filterBool,
        ElasticaQueryString $queryString)
    {
        $this->query = $query;

        $this->client = $client;

        $this->document = $document;

        $this->queryText = $queryText;

        $this->queryBool = $queryBool;

        $this->filterTerm = $filterTerm;

        $this->filterRange = $filterRange;

        $this->filterBool = $filterBool;

        $this->queryString = $queryString;

        // Set Config for Elastica Client
        $this->client->setConfig(array(
            'host' => Config::get('elastica.host'),
            'port' => Config::get('elastica.port')
        ));

        $this->client->getConnection()->setHost(Config::get('elastica.host'))->setPort(Config::get('elastica.port'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($index, $types = 'all')
    {
        $hits = 0;
        $contents = array();

        // + - && || ! ( ) { } [ ] ^ " ~ * ? : \
        $searchArr = array('+', '-', '(', ')', '{', '}', '[', ']', '^', '~', '*', '?', ':');
        $replaceArr = array('\+', '\-', '\(', '\)', '\{', '\}', '\[', '\]', '\^', '\~', '\*', '\?', '\:');

        // $q = Input::get('q', '');
        $q = str_replace($searchArr, $replaceArr, Input::get('q', ''));
        $limit = (int) Input::get('limit', '');
        $offset = (int) Input::get('offset', '');
        $collectionKey = Input::get('collectionKey', '');
        $brandKey = Input::get('brandKey', '');
        $priceMax = Input::get('priceMax', '');

        // Price min shoude be greater than zero.
        $priceMin = Input::get('priceMin', '');

        $orderBy = Input::get('orderBy', '');
        $order = Input::get('order', 'asc');

        $campaign = Input::get('campaign', '');
        $trueyou = Input::get('trueyou', '');

        // if ($q = Input::get('q') and mb_strlen($q) >= 3)
        // {
            /*
            //$keyword = ApiDocumentController::tokenizer($q);
            $keyword = $q;

            // Replace opertors with uppercase.
            $keyword = preg_replace(array('/and/i', '/or/i'), array('AND', 'OR'), $keyword);

            //$q = 'indexing:'.$keyword;
            $q = $keyword;
            */

            $elasticaIndex = $this->client->getIndex($index);

            $queryString = $this->queryString;

            if ($types == 'all') $types = '';

            if (!empty($q))
            {
                //'And' or 'Or' default : 'Or'
                $queryString->setDefaultOperator('OR');
                // If no fields are set, _all is chosen
                // $queryString->setFields(array('_all'));
                $queryString->setFields(array('title^10', 'brand.name^10', 'tag^10'));
                $queryString->setQuery($q);

                // Create the actual search object with some data.
                $this->query->setQuery($queryString);
            }

            // Set Limit & Offset
            if ($limit > 0)
            {
                $this->query->setLimit($limit);
                $this->query->setFrom($offset);
            }

            if ($collectionKey != '')
            {
                // Set Filter Term
                $this->filterTerm->setTerm('collections.pkey', $collectionKey);
                $this->filterBool->addMust($this->filterTerm);
                $this->query->setFilter($this->filterBool);
            }

            if ($brandKey != '')
            {
                // Set Filter Term
                $this->filterTerm->setTerm('brand.pkey', $brandKey);
                $this->filterBool->addMust($this->filterTerm);
                $this->query->setFilter($this->filterBool);
            }

            /* ========== Filter By Discount, Flashsale, itruemart_tv, today_special ========== */
            if ($campaign == 'discount')
            {
                // Product Listing by percent Discount.
                $filterDiscount = new ElasticaFilterRange;
                $filterDiscount->addField('percent_discount.max', array('gt' => 0));
                $this->filterBool->addMust($filterDiscount);
                $this->query->setFilter($this->filterBool);
            }
            elseif ($campaign != '')
            {
                // Product Listing by Flashsale, itruemart_tv, today_special
                $currentDate = date('Y-m-d');

                // Set Filter Term
                $this->filterTerm->setTerm('variants.active_special_discount.campaign_type', $campaign);
                $this->filterBool->addMust($this->filterTerm);
                $this->query->setFilter($this->filterBool);

                // Set Filter Range
                $filterStartedDate = new ElasticaFilterRange;
                $filterStartedDate->addField('variants.active_special_discount.started_at', array('lte' => $currentDate));
                $this->filterBool->addMust($filterStartedDate);
                $this->query->setFilter($this->filterBool);

                $filterEndedDate = new ElasticaFilterRange;
                $filterEndedDate->addField('variants.active_special_discount.ended_at', array('gte' => $currentDate));
                $this->filterBool->addMust($filterStartedDate);
                $this->query->setFilter($this->filterBool);
            }

            /* ========== Filter by Trueyou ========== */
            if (!empty($trueyou))
            {
                $filterExist = new ElasticaFilterExists('variants.active_trueyou_discount');
                $filterExist->setField('variants.active_trueyou_discount');
                $this->filterBool->addMust($filterExist);
                $this->query->setFilter($this->filterBool);
            }


            // Product price 0 should not show on item listing.
            if ( ! $priceMin)
            {
                // I don't know why elasticsearch cannot set to zero.
                $priceMin = 1;
            }


            /* ========== Filter By Price ========== */
            if (is_numeric($priceMin))
            {
                $filterRangeMax = new ElasticaFilterRange;
                $filterRangeMax->addField('price_range.max', array('gte' => $priceMin));
                $this->filterBool->addMust($filterRangeMax);
                $this->query->setFilter($this->filterBool);
            }

            if (is_numeric($priceMax))
            {
                $filterRangeMin = new ElasticaFilterRange;
                $filterRangeMin->addField('price_range.min', array('lte' => $priceMax));
                $this->filterBool->addMust($filterRangeMin);
                $this->query->setFilter($this->filterBool);
            }
            /* ========== End - Filter By Price ========== */

            /* ========== Sort ========== */
            if (!empty($orderBy))
            {
                $order = ($order == 'asc') ? 'asc' : 'desc' ;

                if ($orderBy == 'price')
                {
                    if ($order == 'asc')
                    {
                        $sortArr = array(
                            'price_range.min' => array('order' => 'asc'),
                        );
                    }
                    else
                    {
                        $sortArr = array(
                            'price_range.max' => array('order' => 'desc'),
                        );
                    }

                }
                elseif ($orderBy == 'discount')
                {
                    if ($order == 'asc')
                    {
                        $sortArr = array(
                            'percent_discount.max' => array('order' => 'asc'),
                        );
                    }
                    else
                    {
                        $sortArr = array(
                            'percent_discount.max' => array('order' => 'desc'),
                        );
                    }

                }
                else
                {
                    $sortArr = array(
                        "{$orderBy}" => array('order' => "{$order}"),
                    );
                }

                $this->query->addSort($sortArr);
            }
            /* ========== Sort ========== */


            if (isset($_GET['kousuke']))
            {
                sd($this->query);
            }

            //Search on the index.
            $elasticaResultSet = $elasticaIndex->getType($types)->search($this->query);

            $results = $elasticaResultSet->getResults();
            $hits = $elasticaResultSet->getTotalHits();

            foreach ($results as $result)
            {
                $content = array(
                    'type'   => $result->getType(),
                    'score'  => $result->getScore(),
                    'data'   => $result->getData(),
                );

                $contents[] = $content;
            }
        // }

        $response = array(
            'hits'    => $hits,
            'results' => $contents
        );

        return API::createResponse($response, 200);
    }

    public function find($index, $types = 'all', $id = 0)
    {
        $indexType = $this->client->getIndex($index)->getType($types);

        try
        {
            $result = $indexType->getDocument($id);
        }
        catch (Exception $e)
        {
            return API::createResponse('Not found', 404);
        }

        return API::createResponse($result->getData(), 200);
    }

    // public function match()
    // {
    //  $text = $this->queryText->setField('source.title', 'ลองจับ QX10 และ QX100 กล้องทรงเลนส์สำหรับสมาร์ทโฟนจากโซนี่');

    //  $bool = $this->queryBool->addShould($text);

    //  $query = $this->query->setQuery($bool);

    //  $q = $query->toArray();

    //  $elasticaIndex = $this->client->getIndex('application');

    //  $results = $elasticaIndex->search($q);

    //  sd($results);
    // }

}