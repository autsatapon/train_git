<?php

use Teepluss\Theme\Widget;

class CMessage extends Widget {

    public $template = 'message';

    /**
     * Arrtibutes pass from a widget.
     *
     * @var array
     */
    public $attributes = array(
        'type'     => 'error',
        'messages' => array()
    );

    /**
     * Code to start this widget.
     *
     * @return void
     */
    public function init()
    {
        // Initialize widget.
    }

    /**
     * Logic given to a widget and pass to widget's view.
     *
     * @return array
     */
    public function run()
    {
        $attributes = $this->getAttributes();

        return $attributes;
    }

}