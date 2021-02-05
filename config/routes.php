<?php

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;


/** @var \Cake\Routing\RouteBuilder $routes */
$routes->setRouteClass(DashedRoute::class);

$routes->scope('/', function (RouteBuilder $builder) {

    $builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
    $builder->connect('/pages/*', ['controller' => 'Pages', 'action' => 'pages']);
    $builder->fallbacks();
});


 #If you need a different set of middleware or none at all,
 # new scope and define routes there.

 $routes->scope('/api', function (RouteBuilder $builder) {
     // No $builder->applyMiddleware() here.
     $builder->get('/*', ['controller' => 'Api', 'action' => 'show']);
     $builder->post("/*", ['controller' => 'Api', 'action' => 'add']);
     $builder->delete("/*", ['controller' => 'Api', 'action' => 'del']);
     // Parse specified extensions from URLs
     $builder->setExtensions(['json']);

     // Connect API actions here.
 });
