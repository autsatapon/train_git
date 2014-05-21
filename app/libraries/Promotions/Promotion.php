<?php namespace Promotions;

use Promotion as EloquentPromotion;
use ValidPromotion;
use Exception;
use PCMSPromotion;

abstract class Promotion {

    // for event sorting
    protected $eventPriority;
    private $valid = null;

    private $dirty = false;

    protected $validPromotion;
    protected $promotion;

    protected $metaData = null;

    // abstract functions
    abstract public function isActive();
    abstract public function register();
    abstract public function applyEffect();

    /**
     * __construct
     * @param EloquentPromotion $promotion
     */
    final public function __construct(EloquentPromotion $promotion)
    {
        // check promotion is exists or not
        if (! $promotion->exists)
        {
            throw new Exception('This promotion is empty model.');
        }

        if (is_null($this->eventPriority))
        {
            throw new Exception('You must declare protected $eventPriority in '.get_class($this).' class.');
        }

        $this->promotion = $promotion;

        $this->metaData = array();
    }

    public function setValid($valid)
    {
        // first set valid - set it and stop
        if (is_null($this->valid))
        {
            $this->valid = (boolean) $valid;
            return;
        }

        if ($this->valid != $valid)
        {
            $this->valid = (boolean) $valid;

            $this->dirty = true;
        }

    }

    public function isValid()
    {
        return (boolean) $this->valid;
    }

    public function isDirty()
    {
        return (boolean) $this->dirty;
    }

    public function bind($event, $method)
    {
        $that = $this;

        \Event::listen($event, function() use ($that, $method, $event)
        {
            $result = call_user_func_array(array($that, $method), func_get_args());

            $log = array(
                'event' => $event,
                'class' => get_class($that),
                'method' => $method,
                'result' => $result,
                'promotion_id' => $that->getPromotion()->getKey(),
                // 'parameters' => func_get_args()
            );
            PCMSPromotion::log($log);

        }, $this->eventPriority);
    }

    public function bindValidator($event, $method)
    {
        $that = $this;

        \Event::listen($event, function() use ($that, $method, $event)
        {
            $result = call_user_func_array(array($that, $method), func_get_args());

            $log = array(
                'event' => $event,
                'class' => get_class($that),
                'method' => $method,
                'result' => $result,
                'promotion_id' => $that->getPromotion()->getKey(),
                // 'parameters' => func_get_args()
            );
            PCMSPromotion::log($log);

            if ($result === true)
            {
                $that->setValid(true);
            }

            if ($result === false)
            {
                $that->setValid(false);
            }

            return $result ?: null;
        }, $this->eventPriority);
    }

    public function bindApplyIfValid($event, $method)
    {
        $that = $this;

        \Event::listen($event, function() use ($that, $method, $event)
        {
            if ($that->isValid())
            {
                $result = call_user_func_array(array($that, $method), func_get_args());

                $log = array(
                    'event' => $event,
                    'class' => get_class($that),
                    'method' => $method,
                    'result' => $result,
                    'promotion_id' => $that->getPromotion()->getKey(),
                    // 'parameters' => func_get_args()
                );
                PCMSPromotion::log($log);

                return $result ?: null;
            }

            return null;
        }, $this->eventPriority);
    }

    public function setMetaData(array $data)
    {
        $this->metaData = $data;
    }

    public function getMetaData()
    {
        return $this->metaData;
    }

    public function setValidPromotion(ValidPromotion $model)
    {
        $this->validPromotion = $model;
    }

    public function getValidPromotion()
    {
        return $this->validPromotion;
    }

    public function getPromotion()
    {
        return $this->promotion;
    }

    public function getModel()
    {
        return $this->promotion;
    }

}