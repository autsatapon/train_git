<?php

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