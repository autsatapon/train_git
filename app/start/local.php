<?php

error_reporting(E_ALL);

Log::getMonolog()->pushHandler(new Monolog\Handler\ChromePHPHandler());