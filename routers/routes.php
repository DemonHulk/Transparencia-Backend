<?php

return [
    'GET' => [
        'area' => 'AreaController@QueryAllController',
        'area/(\d+)' => 'AreaController@QueryOneController',
        'usuario' => 'UsuarioController@QueryAllController',
        'usuario/(\d+)' => 'UsuarioController@QueryOneController',
        'punto' => 'PuntoController@QueryAllController',
        'punto/(\d+)' => 'PuntoController@QueryOneController',
    ],
    'POST' => [
        'area' => 'AreaController@InsertController',
        'usuario' => 'UsuarioController@InsertController',
        'punto' => 'PuntoController@InsertController',
    ],
    'PUT' => [
        'area/(\d+)' => 'AreaController@UpdateController',
        'usuario/(\d+)' => 'UsuarioController@UpdateController',
        'punto/(\d+)' => 'PuntoController@UpdateController',
    ],
    'DELETE' => [
        'area/(\d+)' => 'AreaController@DeleteController',
        'usuario/(\d+)' => 'UsuarioController@DeleteController',
        'punto/(\d+)' => 'PuntoController@DeleteController',
    ],
];
