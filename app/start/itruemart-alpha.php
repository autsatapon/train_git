<?php

//Log::getMonolog()->pushHandler(new Monolog\Handler\ChromePHPHandler());


/**
 * Insert log to database.
 */
Log::listen(function($level, $message, $context)
{
    Logger::create(array(
        'level'   => $level,
        'message' => $message,
        'context' => $context
    ));
});