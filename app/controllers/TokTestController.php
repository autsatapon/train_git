<?php 

 class TokTestController extends Controller {
 	public function getIndex()
 	{
 		$elasticSearch = new ElasticRepository('pcms', 'product');

 		//$elasticSearch->deleteIndexAllProducts();
 		//exit();
 		//UpdategetSource()
 		$data = $elasticSearch->getProductByPkey('25061378181986');
 		d($data->getSource());
 		exit();

 		$elasticaClient = new \Elastica\Client();

		$elasticaIndex = $elasticaClient->getIndex('pcms');
		$elasticaType  = $elasticaIndex->getType('product');
		//exit();

 		$documents = array();
 		foreach ($products as $key => $value) 
 		{
 			$data = array(
					'id'      => $value['id'],
					'content' => $value,
					'tstamp'  => time(),
					'_boost'  => 1.0
					);
 			$document = new \Elastica\Document($value['id'], $data);
 			$elasticaType->addDocument($document);
 			$elasticaType->getIndex()->refresh();
 		}

		//$elasticaType->addDocuments($documents);
		//$elasticaType->getIndex()->refresh();

 		d($elasticaType);

/* 		$count = 500;
 		$documents = array();
 		while ($count>0) 
 		{
 			 $documents[] = new \Elastica\Document(
 			 		'id' => $count,
 			 		'',
 			 		'_boost'  => 1.0
 			 	)

 			$count--;
 		}

 		d($elasticaClient);*/

 		return 'asdasdasd';
 	}


 	function getTest()
 	{
 		$products = Product::all()->toArray();
 		//d($products);
 		$elasticaClient = new \Elastica\Client();
 		$elasticaIndex = $elasticaClient->getIndex('pcms');

 		$elasticaQueryString  = new \Elastica\Query\QueryString();
 		$elasticaQueryString->setDefaultOperator('AND');
 		$elasticaQueryString->setQuery('25061378181986');


		// Create the actual search object with some data.
		$elasticaQuery        = new \Elastica\Query();
		$elasticaQuery->setQuery($elasticaQueryString);

		//Search on the index.
		$elasticaResultSet    = $elasticaIndex->search($elasticaQuery);

		// $elasticaQuery->setFrom(50);    // Where to start?
		// $elasticaQuery->setLimit(25);   // How many?


		$elasticaResults  = $elasticaResultSet->getResults();
		d($elasticaResults);
 	}

 	public function getRegex($start, $end)
 	{
 		$startEx = explode(':', $start);
 		$endEx = explode(':', $end);

 		$firstHour = intval($startEx[0]);
 		$endHour = intval($endEx[0]);

 		$regExp = array();

 		if ($startEx[1] === '00')
 		{
 			$midHourStart = $firstHour;
 		}
 		else
 		{
 			$midHourStart = $firstHour + 1;
 			
 			// 01:15
 			// (01:(1[5-9])|([2-5][0-9]))
 			$firstHourTenMinute = floor(intval($startEx[1])/10);
 			$firstHourMinute = intval($startEx[1])%10;

 			$pattern = '('.sprintf('%02d', $firstHour).':(('.$firstHourTenMinute.'['.$firstHourMinute.($firstHourMinute<9 ? '-9' : '').'])';
 			if ($firstHourTenMinute < 5)
 				$pattern .= '|(['.($firstHourTenMinute+1).($firstHourTenMinute+1 < 5 ? '-5' : '').'][0-9])';
 			$pattern .= '))';
 			array_push($regExp, $pattern);
 		}

 		if ($endEx[1] === '59')
 		{
 			$midHourEnd = $endHour;
 		}
 		else
 		{
 			$midHourEnd = $endHour - 1;

 			// 03:45
 			// (03:([0-3][0-9])|(4[0-5]))
 			$endHourTenMinute = floor(intval($endEx[1])/10);
 			$endHourMinute = intval($endEx[1])%10;

 			$pattern = '('.sprintf('%02d', $endHour).':(';
 			if ($endHourTenMinute > 0)
 				$pattern .= '([0'.($endHourTenMinute-1 > 0 ? '-'.($endHourTenMinute-1) : '').'][0-9])|';
 			$pattern .= $endHourTenMinute.'['.($endHourMinute>0 ? '0-' : '').$endHourMinute.']))';
 			array_push($regExp, $pattern);
 		}

 		$pattern = ''; // midHourStart - midHourEnd
 		array_push($regExp, $pattern);

 		d($firstHour, $endHour);
 		d($midHourStart, $midHourEnd);

 		d(implode('|',$regExp));

 		return '^'.implode('|', $regExp).'$';

 	}
 }