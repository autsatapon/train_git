<?php

class CampaignsController extends AdminController {

    /**
     * New constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->theme->breadcrumb()->add('Manage Campaign', URL::to('campaigns'));
    }

    /**
     * List all brands and policies.
     *
     * @return string
     */
    public function getIndex()
    {
		$campaignData = Campaign::orderBy('created_at', 'desc')->get();
        $this->data['campaignData'] = $campaignData;

        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

        $this->theme->setTitle('Campaign Management');

        return $this->theme->of('campaigns.index', $this->data)->render();
    }
	public function getCreate()
    {
		// $this->theme->asset()->container('footer')->usePath()->add('jquery-ui-datetimepicker', 'plugins/timepicker/jquery-ui-timepicker-addon.js', 'jquery');
        $this->theme->asset()->container('footer')->usePath()->add('jquery-ui-datetimepicker', 'plugins/timepicker/jquery-ui-timepicker-addon.js', 'jquery');
        $this->theme->asset()->container('footer')->usePath()->add('jquery-ui-timepicker', 'jui/js/timepicker/jquery-ui-timepicker.min.js', 'jquery'); 
        $this->theme->breadcrumb()->add('Create Campaign', URL::to('campaigns/create'));
        $this->theme->setTitle('Create Campaign');
        $apps = PApp::lists('name', 'id');
        $view = compact('apps');

        return $this->theme->of('campaigns.create', $view)->render();
    }

	public function isNull($val){
		if  (!empty($val)) {
			return true;
		} else {
			return false;
		}
	}
	public function postCreate()
    {
		$campaign = new Campaign;
        $campaign->app_id = Input::get('app_id');
        $campaign->name = Input::get('campaign_name');
        $campaign->detail = Input::get('detail');
		//$campaign->note = Input::get('note');

		if ($this->isNull(Input::get('start_date'))) {
			$campaign->start_date = Input::get('start_date') ;
		}
		if ($this->isNull(Input::get('end_date'))) {
			$campaign->end_date = Input::get('end_date');
		}

		$campaign->budget = Input::get('budget');
		$campaign->budget = str_replace(",", "", $campaign->budget);
		$campaign->status = Input::get('status');

		if ( ! $campaign->save())
        {
            return Redirect::to('campaigns/create')->withInput()->withErrors($campaign->errors());
        }

        if (Input::get('note') != '')
        {
            $note = new Note;
            $note->detail = Input::get('note');
            $campaign->note()->save($note);
        }

        return Redirect::to('campaigns');
	}

    public function getEdit($id = 0)
    {
    	$this->theme->asset()->container('footer')->usePath()->add('jquery-ui-datetimepicker', 'plugins/timepicker/jquery-ui-timepicker-addon.js', 'jquery');
		$campaignData = Campaign::findOrFail($id);

        $campaign = $campaignData;

        $formData = array(
            'app_id'     => $campaignData->app_id,
            'name'       => $campaignData->name,
            'detail'	 => $campaignData->detail,
            'note' 		 => @$campaignData->note->detail,
            'budget'	 => $campaignData->budget,
            'status' 	 => $campaignData->status,
            'start_date' => $campaignData->start_date,
            'end_date'   => $campaignData->end_date

        );

        $this->theme->breadcrumb()->add('Edit Campaign', URL::to('campaigns/edit/'.$id));
        $this->theme->setTitle('Edit Campaign');
        $apps = PApp::lists('name', 'id');
        $view = compact('apps','campaign','formData');

        return $this->theme->of('campaigns.edit', $view)->render();
    }

    public function postEdit($id = 0)
    {
		$campaignData = Campaign::findOrFail($id);
        $campaignData->name = Input::get('name');
        $campaignData->detail = Input::get('detail');
		//$campaignData->note = Input::get('note');
		$campaignData->budget = Input::get('budget');
		$campaignData->budget = str_replace(",", "", $campaignData->budget);
		$campaignData->status = Input::get('status');

		if ($this->isNull(Input::get('start_date'))) {
				$campaignData->start_date = Input::get('start_date');

		}
		if ($this->isNull(Input::get('end_date'))) {
				$campaignData->end_date = Input::get('end_date');
		}


        if ( !$campaignData->save() )
        {
            return Redirect::to('campaigns/edit/'.$id)->withInput()->withErrors($campaignData->errors());
        }

        // create note
        $note = $campaignData->note;

        if ( !empty($note) )
        {
            $note->detail = Input::get('note');
            $note->save();
        }
        else
        {
            $note = new Note;
            $note->detail = Input::get('note');
            $campaignData->note()->save($note);
        }


        return Redirect::to('campaigns');
    }

    /**
     * Re-build campaign promotions.
     *
     * @param  integer $id campaignId
     */
    public function getRebuild($id = null)
    {
        // sd( ProductVariant::find(32)->promotions->toArray() );

        if (is_null($id))
        {
            $campaigns = Campaign::with('promotions.campaign')->active()->get();
            foreach ($campaigns as $campaign)
            {
                foreach ($campaign->promotions as $promotion)
                {
                    $promotion->rebuildPromotion();
                }
            }

            return Redirect::to('campaigns')->withSuccess('Rebuild all promotions completed.');
        }
        else
        {
            $campaign = Campaign::with('promotions.campaign')->findOrFail($id);
            foreach ($campaign->promotions as $promotion)
            {
                $promotion->rebuildPromotion();
            }

            return Redirect::to('campaigns')->withSuccess('Rebuild promotions completed.');
        }

        // d($campaign);
        // exit;

        // foreach ($campaign->promotions as $promotion)
        // {

            // if ($promotion->promotion_category != 'trueyou') continue;

            // // s($promotion->toArray());

            // $p = Promotion\Promotion::factory($promotion->promotion_category);

            // $p->setRoute($promotion->effects['discount']['which'], $promotion->effects['discount']['following_items']);

            // $p->setExcludeProducts($promotion->effects['discount']['exclude_product']['un_following_items']);
            // $p->setExcludeVariants($promotion->effects['discount']['exclude_variant']['un_following_items']);

            // $discount_type = $promotion->effects['discount']['type'];
            // $discount = ($discount_type == 'price') ? $promotion->effects['discount']['baht'] : $promotion->effects['discount']['percent'] ;
            // $condition = ($promotion->conditions['trueyou'][0]['type'] == 'black_card') ? 'black' : 'red' ;
            // $hint = $promotion->name;

            // $attrs = array(
            //     'app_id'        => $campaign->app_id,
            //     'started_at'    => $promotion->start_date,
            //     'ended_at'      => $promotion->end_date,
            //     'discount'      => $discount,
            //     'discount_type' => $discount_type,
            //     'condition'     => $condition,
            //     'hint'          => $hint,
            // );

            // $p->attach($promotion, $attrs);
        // }


        // return 'Rebuild Promotion - complete.';

        // $p = Promotion\Promotion::factory('trueyou');

        // $p->setRoute('Brand', [111, 222, 333]);

        // $p->setExcludeBrands();
        // $p->setExcludeProducts();
        // $p->setExcludeVariants();


    }

}