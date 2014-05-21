<?php

class SupplyChainController extends Controller {

    protected $supplyChain;

    public function __construct(SupplyChainRepositoryInterface $supplyChain)
    {
        $this->supplyChain = $supplyChain;
    }

    public function getDailySync()
    {
        $startDate = Input::get('startDate', date('Y-m-d H:i:s', strtotime('yesterday midnight')));
        $endDate = Input::get('endDate', date('Y-m-d H:i:s', strtotime('now')));

        $this->supplyChain->dailySync($startDate, $endDate);
        $this->supplyChain->syncLot($startDate, $endDate);

        return Redirect::to('/')->withSuccess('Data has been synced.');
    }

    public function getSyncLot()
    {
    	return $this->supplyChain->syncLot( Input::get('startDate'), Input::get('endDate'));
    }
}