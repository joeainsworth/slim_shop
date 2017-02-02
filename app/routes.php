<?php

$app->get('/', ['Shop\Controllers\HomeController', 'index'])->setName('home');