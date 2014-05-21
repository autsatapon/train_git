<?php

use Illuminate\Database\Eloquent\Model;

class PCMSPromotion {

    protected static $pcmsPromotionsInstance = array();

    protected static $logs = array();

    protected $instancePromotions = array();

    protected $model = null;

    public static function run(Model $model)
    {
        $instance = new static($model);

        self::$pcmsPromotionsInstance[] = $instance;

        $instance->execute();

        return $instance;
    }

    public static function transferValidPromotions(Model $oldModel, Model $newModel)
    {
        $validPromotions = $oldModel->validPromotions()->get();

        if ($validPromotions->count())
        {
            $validPromotions->each(function($model) use ($newModel) {
                $model->promotionable_type = get_class($newModel);
                $model->promotionable_id = $newModel->getKey();
                $model->save();
            });

            return $validPromotions->count();
        }

        return false;
    }

    public static function getInstances()
    {
        return self::$pcmsPromotionsInstance;
    }

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function execute()
    {
        // get global active promotions
        $promotions = $this->getActivePromotions();

        // get current cart

        // get valid promotions
        $this->model->load('validPromotions');

        // transform active promotions to specific classes
        $activatedPromotionsID = array();
        foreach ($promotions as $promotion)
        {
            $className = 'Promotions\\'.studly_case($promotion->promotion_category).'Promotion';

            $promotionObject = new $className($promotion);
            if ($promotionObject->isActive())
            {
                $validPromotion = $this->model->validPromotions->filter(function($item) use ($promotion) {
                    return ($item->promotion_id == $promotion->getKey());
                })->first();

                // if there is that valid promotion on this cart
                if ($validPromotion)
                {
                    $promotionObject->setValid(true);
                    $promotionObject->setMetaData($validPromotion->meta);
                    $promotionObject->setValidPromotion($validPromotion);
                }
                else
                {
                    $promotionObject->setValid(false);
                }

                $this->instancePromotions[] = $promotionObject;

                // collect promotion id that activated
                $activatedPromotionsID[] = $promotion->getKey();
            }
        }

        $validDeleted = false;

        // check validPromotions if it's not active anymore
        // delete it from validPromotions
        $this->model->validPromotions->each(function($item) use ($activatedPromotionsID, &$validDeleted) {
            if (! in_array($item->promotion_id, $activatedPromotionsID))
            {
                $item->delete();
                $validDeleted = true;
            }
        });

        // call active promotion routine
        foreach ($this->instancePromotions as $promotion)
        {
            // register event
            $promotion->register();
            $promotion->applyEffect();
        }

        // if old valid promotion deleted, we should reload validPromotion via model
        // this will use later in App::after event that will run later
        if ($validDeleted)
        {
            $this->model->load('validPromotions');
        }

        $pcmsPromotion = $this;

        // event after for save valid state
        App::after(function($request, $response) use ($pcmsPromotion) {

            $model = $pcmsPromotion->getModel();

            foreach($pcmsPromotion->getInstancePromotions() as $promotion)
            {
                // check promotion is dirty?
                if ($promotion->isDirty())
                {
                    // get validPromotion
                    $validPromotion = $model->validPromotions->filter(function($item) use ($promotion) {
                        $id = $promotion->getValidPromotion() ? $promotion->getValidPromotion()->promotion_id : null;
                        return ($item->promotion_id == $id);
                    })->first();

                    if ($promotion->isValid())
                    {
                        if (! $validPromotion)
                        {
                            // promotion is valid but don't have in db so create
                            $model->validPromotions()->create(
                                array(
                                    'promotion_id' => $promotion->getModel()->getKey(),
                                    'meta' => $promotion->getMetaData()
                                )
                            );
                        }
                    }
                    else
                    {
                        if ($validPromotion)
                        {
                            // promotion is not valid but in db has it so delete it.
                            $validPromotion->delete();
                        }
                    }
                }
            }

            if (Input::has('pcms_promotion_debug'))
            {
                sd(PCMSPromotion::getLogs());
            }

        });
    }

    public function getInstancePromotions()
    {
        return $this->instancePromotions;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * get all active promotions
     * @return Array
     */
    public static function getActivePromotions()
    {
        $PApp = PApp::getCurrentApp();
        $promotions = array();

        if (! $PApp)
        {
            return $promotions;
        }

        // get all promotion that currently active
        $with = array(
            "promotions" => function($query)
            {
                return $query->active();
            }
        );
        $campaigns = Campaign::with($with)->whereAppId($PApp->getKey())->active()->get();

        // fetch all promotions in each campaign
        // and group them as array
        $campaigns->each(function($campaign) use (&$promotions) {
            $campaign->promotions->each(function($promotion) use (&$promotions) {
                if (empty($promotions[$promotion->getKey()]))
                {
                    $promotions[$promotion->getKey()] = $promotion;
                }
            });
        });

        // return as new array
        return array_values($promotions);
    }

    public static function log(Array $array)
    {
        static::$logs[] = $array;
    }

    public static function getLogs()
    {
        return static::$logs;
    }

}