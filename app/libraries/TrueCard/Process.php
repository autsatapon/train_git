<?php namespace TrueCard;

use Input;

class Process {

    protected $data;

    public function __construct($data)
    {
        if (Input::get("dump_data") == "truecard")
        {
            sd($data);
        }

        if (is_string($data))
        {
            $data = trim($data);

            $this->data = simplexml_load_string($data);
        }
        else
        {
            $this->data = new \SimpleXMLElement($data);
        }
    }

    public function all()
    {
        return $this->data;
    }

    public function get()
    {
        $xpath = $this->data->xpath('//body/items/card_information/item/grade');

        if ( ! $xpath)
        {
            return false;
        }

        return (string) array_get((array) $xpath, 0);
    }

    public function check()
    {
        switch ($this->get())
        {
            case 'G' : return 'red';
            case 'P' : return 'black';
        }

        return false;
    }

    public function hasCard()
    {
        return (bool) $this->check();
    }

    public function isRed()
    {
        return $this->check() == 'red';
    }

    public function isBlack()
    {
        return $this->check() == 'black';
    }

}