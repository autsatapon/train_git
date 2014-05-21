<?php

class Note extends Eloquent {

    public function noteable()
    {
        return $this->morphTo();
    }

}