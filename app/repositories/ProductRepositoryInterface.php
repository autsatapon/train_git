<?php

interface ProductRepositoryInterface {

	// public function search($criteria, $value);

	public function find($id);

	public function getExecuteFormSearch();

	public function executeFormSearch();

	// public function saveDraft($product);
	public function saveDraft($id, $data);

}