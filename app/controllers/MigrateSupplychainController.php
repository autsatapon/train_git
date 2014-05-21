<?php

class MigrateSupplychainController extends BaseController {

	protected $supplychainUrl;

	public function __construct()
	{
		$this->supplychainUrl = Config::get('supplychain.url');
	}

    public function getMigrateMaterial()
    {
    	if (Input::get('continue') == false)
    	{
    		Cron::setValue('stop-migrate-material', null);
    	}

    	if (Cron::getValue('stop-migrate-material'))
    	{
    		return 'completed at '.date('Y-m-d H:i:s');
    	}

    	$repo = App::make('SupplyChainRepositoryInterface');

        $runningId = Cron::getValue('migrate-material-id', 6096);
        $supplyChainUrl = $this->supplychainUrl.'/api_v2/get_new_material_by_id?'.http_build_query(array('start'=>$runningId, 'end'=>$runningId+100, 'limit'=>'no'));
        $data = $repo->curlGet($supplyChainUrl);
		$obj = json_decode($data, true);


		$allData = array_get( $obj, 'jsonData.data.materials' );
        $allDataCount = count($allData);
       	$maxId = array_get($obj, 'jsonData.data.max_id');

        if ($allDataCount > 0)
        {

			$repo->importNewMaterials($allData);
			$lastData = end($allData);
			$lastId = $lastData['id'];

			$nextId = $lastId+1;
		}
		else
		{
			$nextId = $runningId + 101;
		}

		Cron::setValue('migrate-material-id', $nextId);

		if ($nextId > $maxId)
		{
			Cron::setValue('stop-migrate-material', true);
		}

		d($runningId, $nextId-1);

		return 'Running ...<script>setTimeout(function(){window.location="'.URL::to('/migrate-supplychain/migrate-material?continue=1').'"}, 1000);</script>';
    }

    public function getMigrateLot()
    {
    	if (Input::get('continue') == false)
    	{
    		Cron::setValue('stop-migrate-lot', null);
    	}

    	if (Cron::getValue('stop-migrate-lot'))
    	{
    		return 'completed at '.date('Y-m-d H:i:s');
    	}

    	$repo = App::make('SupplyChainRepositoryInterface');

        $runningId = Cron::getValue('migrate-lot-id', 1);
        $supplyChainUrl = $this->supplychainUrl.'/api_v2/get_new_lot_by_id?'.http_build_query(array('start'=>$runningId, 'end'=>$runningId+500, 'limit'=>'no'));
        $data = $repo->curlGet($supplyChainUrl);
		$obj = json_decode($data, true);


		$allData = array_get( $obj, 'jsonData.data.materials' );
        $allDataCount = count($allData);
       	$maxId = array_get($obj, 'jsonData.data.max_id');

        if ($allDataCount > 0)
        {

			$repo->importLots($allData);
			$lastData = end($allData);
			$lastId = $lastData['supplychain_id'];

			$nextId = $lastId+1;
		}
		else
		{
			$nextId = $runningId + 501;
		}

		Cron::setValue('migrate-lot-id', $nextId);

		if ($nextId > $maxId)
		{
			Cron::setValue('stop-migrate-lot', true);
			d($nextId, $maxId);
			return 'Stopped';
		}

		d($runningId, $nextId-1);

		return 'Running ...<script>setTimeout(function(){window.location="'.URL::to('/migrate-supplychain/migrate-lot?continue=1').'"}, 1000);</script>';
    }

}