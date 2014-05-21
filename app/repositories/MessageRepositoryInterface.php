<?php

interface MessageRepositoryInterface {

	public function thanksForOrdering(Order $order);
	public function pleasePayYourOrder(Order $order);
	public function yourOrderWasShipped(Order $order);
	public function thanksForYourPayment(Order $order);

}