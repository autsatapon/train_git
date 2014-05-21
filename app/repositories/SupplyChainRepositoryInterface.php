<?php

interface SupplyChainRepositoryInterface {
    /**
     *  get all materials from SupplyChain
     */
    public function dailySync();

    /**
     * get lot movement from SupplyChain
     * @param string
     * @param string
     */
    public function syncLot($startDate, $endDate);
}