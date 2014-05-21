<?php 

 interface ElasticRepositoryInterface{

 	/**
 	 * Action index to elasticsearch by product
 	 * @param  string $pkey Pkey for relation to get product data at database PCMS
 	 * @return boolean       true = index success, false = index fail
 	 */
 	public function indexingProduct($pkey);

 	/**
 	 * Action index all product
 	 * @return bollean true = index success, false = index fail
 	 */
 	public function indexingAllProducts();

 	/**
 	 * Action delete index of product
 	 * @param  string $pkey Pkey for relation to get product data at database PCMS
 	 * @return boolean       true = delete index success, false = delete index fail
 	 */
 	public function deleteIndexProduct($pkey);

 	/**
 	 * Action delete  all index of product
 	 * @return bollean true = index success, false = index fail
 	 */
 	public function deleteIndexAllProducts();

 	/**
 	 * Get product in elastic
 	 * @param  string $pkey Pkey for get data form elastic by index name
 	 * @return instance elastic data
 	 */
 	public function getProductByPkey($pkey);
 }