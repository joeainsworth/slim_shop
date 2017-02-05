<?php

$app->get('/', ['Shop\Controllers\HomeController', 'index'])->setName('home');
$app->get('/products/{slug}', ['Shop\Controllers\ProductController', 'index'])->setName('product.get');

$app->get('/cart', ['Shop\Controllers\CartController', 'index'])->setName('cart.index');
$app->get('/cart/add/{slug}/{quantity}', ['Shop\Controllers\CartController', 'add'])->setName('cart.add');
$app->post('/cart/update/{slug}', ['Shop\Controllers\CartController', 'update'])->setName('cart.update');