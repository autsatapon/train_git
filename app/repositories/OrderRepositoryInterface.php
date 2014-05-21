<?php

interface OrderRepositoryInterface {

	public function create($app, $input);

	public function reconcile($input);

}