<?php
class DashboardController extends AdminController {

    public function getIndex()
    {
    	// Log::info('debug', range(1, 10));
        $user = Sentry::getUser();

		/*
		$imported_mat_data = DB::table('imported_materials')->where('variant_id',NULL)->get();
		$this->data['number_imported_mat'] = count($imported_mat_data);
		*/
		$this->data['number_imported_mat'] = DB::table('imported_materials')->where('variant_id',NULL)->count();

		$pending_data = DB::table('revisions')->join('products', 'revisions.revisionable_id', '=', 'products.id')->select('products.title', 'revisions.updated_at')
						->where('editor_id',$user->id)->where('revisionable_type','product')->where('revisions.status','draft')
						->orderBy('revisions.updated_at', 'desc')->paginate(30);

		$pending_data_collection = $pending_data->getCollection();
		$that = $this;
		$pending_data_collection->map(function($item) use ($that){
			$item->since = $that->time_elapsed_string(strtotime($item->updated_at));
			$last_updated = strtotime($item->updated_at);
			$today = strtotime(date('Y-m-d H:i:s'));
			if($today - $last_updated > 259200){
				$item->warning = 1;
			}else{
				$item->warning = 0;
			}
		});
		$this->data['pending_data_collection'] = $pending_data_collection;
		$this->data['pending_data'] = $pending_data;

		$rejected_data = DB::table('revisions')->join('products', 'revisions.revisionable_id', '=', 'products.id')->select('products.title', 'revisions.updated_at', 'revisions.note')
						->where('editor_id',$user->id)->where('revisionable_type','product')->where('revisions.status','rejected')
						->orderBy('revisions.updated_at', 'desc')->paginate(30);

		$rejected_data_collection = $rejected_data->getCollection();
		$that = $this;
		$rejected_data_collection->map(function($item) use ($that){
			$item->since = $that->time_elapsed_string(strtotime($item->updated_at));
		});
		$this->data['rejected_data_collection'] = $rejected_data_collection;
		$this->data['rejected_data'] = $rejected_data;


        return $this->theme->of('dashboard', $this->data)->render();
    }

	public function time_elapsed_string($ptime)
			{
			    $etime = time() - $ptime;

			    if ($etime < 1)
			    {
			        return '0 seconds';
			    }

			    $a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
			                30 * 24 * 60 * 60       =>  'month',
			                24 * 60 * 60            =>  'day',
			                60 * 60                 =>  'hour',
			                60                      =>  'minute',
			                1                       =>  'second'
			                );

			    foreach ($a as $secs => $str)
			    {
			        $d = $etime / $secs;
			        if ($d >= 1)
			        {
			            $r = round($d);
			            return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
			        }
			    }
			}






}