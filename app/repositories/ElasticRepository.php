<?php

class ElasticRepository implements ElasticRepositoryInterface{

	/**
	 * Elastic server config
	 * EX
	 * array('host' => 'localhost', 'port' => 9200)
	 * @var array
	 */
	private $elasticServerConfig;

 	/**
 	 * index name of elasticsearch
 	 * @var string
 	 */
 	private $indexName;

 	/**
 	 * type of index of elasticsearch
 	 * @var string
 	 */
 	private $indexType;

 	/**
 	 * Elastic Conect to server Instance
 	 * @var object
 	 */
 	private $elasticaClient;


 	/**
 	 * Construct
 	 */
 	public function __construct($indexName = 'pcms',
 								$indexType = 'product',
 								$elasticServerConfig = array('host' => 'localhost', 'port' => 9200))
 	{
 		//Set indexName
 		$this->indexName = $indexName;

 		//Set typeOfIndex
 		$this->indexType = $indexType;

 		//Set Elastic config
 		$this->elasticServerConfig = $elasticServerConfig;

 		//Elastic Connectiing and setup Process
 		$this->elasticaClient = new \Elastica\Client(array(
 			'servers' => array(
 				$this->elasticServerConfig
 			)
 		));

 	}


 	/**
 	 * Action index to elasticsearch by product
 	 * @param  string $pkey Pkey for relation to get product data at database PCMS
 	 * @return boolean       true = index success, false = index fail
 	 */
 	public function indexingProduct($pkey)
 	{
 		$productRepo        = new ProductRepository();
		//Get data from productRepo
		$productRepoData_tmp = $productRepo->getProductByPkey($pkey);

		//Create or Update Query Elastic
		$docProductRepoData[] = new \Elastica\Document(
							$productRepoData_tmp['pkey'],
							$productRepoData_tmp
						);

 		try
 		{
			$elasticaIndex = $this->elasticaClient->getIndex($this->indexName);
			$elasticaType  = $elasticaIndex->getType($this->indexType);

			$elasticaType->addDocuments($docProductRepoData);
		}
		catch(Exception $e)
		{
			return false;
		}

		return true;

 	}

 	/**
 	 * Action index all product
 	 * @return bollean true = index success, false = index fail
 	 */
 	public function indexingAllProducts()
 	{
 		//Store all pkey after find all product
 		$pkey_tmp = array();
 		//Find pkey from all product
 		$pkey_tmp = Product::whereStatus('publish')->get()->lists('pkey');


 		//Store productData from get product[Repo]
		$docProductRepoData = array();
		$productRepo        = new ProductRepository();
		$countOrganize      = 500; // A good start are 500 documents per bulk operation
 		foreach ($pkey_tmp as $key => $value)
 		{
			$lap       = 0;
			$lap_index = 0;

 			//Get data from productRepo
 			$productRepoData_tmp = $productRepo->getProductByPkey($value);

 			//Create Query Elastic [Bulk Index]
 			$docProductRepoData[$lap_index][] = new \Elastica\Document(
 								$productRepoData_tmp['pkey'],
 								$productRepoData_tmp
 							);

 			$lap++;
 			if ( ($lap % 500) == 0 )
 			{
 				$lap_index++;
 			}

 		}

 		try
 		{
			$elasticaIndex = $this->elasticaClient->getIndex($this->indexName);
			$elasticaType  = $elasticaIndex->getType($this->indexType);

			//Elastic looping for addDocument [Bulk Operation]
			foreach ($docProductRepoData as $key => $values)
			{
				//Action Create Indexing on elasticsearch
				$elasticaType->addDocuments($values);
			}
		}
		catch(Exception $e)
		{
			return false;
		}

		return true;

 	}

 	/**
 	 * Action delete index of product
 	 * @param  string $pkey Pkey for relation to get product data at database PCMS
 	 * @return boolean       true = delete index success, false = delete index fail
 	 */
 	public function deleteIndexProduct($pkey)
 	{

 		//Store productData from get product[Repo]
 		$docProductRepoData = new \Elastica\Document($pkey);

 		try
 		{
			$elasticaIndex = $this->elasticaClient->getIndex($this->indexName);
			$elasticaType  = $elasticaIndex->getType($this->indexType);


			$elasticaType->deleteDocument($docProductRepoData);
		}
		catch(Exception $e)
		{
			return false;
		}

		return true;
 	}


 	/**
 	 * Action delete  all index of product
 	 * @return bollean true = index success, false = index fail
 	 */
 	public function deleteIndexAllProducts()
 	{
 		try
 		{
			$elasticaIndex = $this->elasticaClient->getIndex($this->indexName);
			$elasticaType  = $elasticaIndex->getType($this->indexType);


			$elasticaType->delete();
		}
		catch(Exception $e)
		{
			return false;
		}

		return true;
 	}

 	/**
 	 * Get product in elastic
 	 * @param  string $pkey Pkey for get data form elastic by index name
 	 * @return instance elastic data
 	 */
 	public function getProductByPkey($pkey)
 	{
		$elasticaIndex = $this->elasticaClient->getIndex($this->indexName);

		//Query search data
 		$elasticaQueryString  = new \Elastica\Query\QueryString();
 		$elasticaQueryString->setDefaultOperator('AND');
 		$elasticaQueryString->setQuery($pkey);

		// Create the actual search object with some data.
		$elasticaQuery        = new \Elastica\Query();
		$elasticaQuery->setQuery($elasticaQueryString);
		$elasticaQuery->setLimit(1);

		//Search on the index.
		$elasticaResultSet    = $elasticaIndex->search($elasticaQuery);

		$elasticaResults  = $elasticaResultSet->getResults();
		return $elasticaResults[0];
 	}


 }
