<?php

return [
    'GET' => [
        'area' => 'AreaController@QueryAllController',
        'area/(\d+)' => 'AreaController@QueryOneController',
        'usuario' => 'UsuarioController@QueryAllController',
        'usuario/(\d+)' => 'UsuarioController@QueryOneController',
        'punto' => 'PuntoController@QueryAllController',
        'punto/(\d+)' => 'PuntoController@QueryOneController',
        'contenido' => 'ContenidoController@QueryAllController',
        'contenido/(\d+)' => 'ContenidoController@QueryOneController',
        'trimestre' => 'TrimestreController@QueryAllController',
        'trimestre/(\d+)' => 'TrimestreController@QueryOneController',
    ],
    'POST' => [
        'area' => 'AreaController@InsertController',
        'usuario' => 'UsuarioController@InsertController',
        'punto' => 'PuntoController@InsertController',
        'trimestre' => 'TrimestreController@InsertController',
    ],
    'PUT' => [
        'area/(\d+)' => 'AreaController@UpdateController',
        'usuario/(\d+)' => 'UsuarioController@UpdateController',
        'punto/(\d+)' => 'PuntoController@UpdateController',
        'trimestre/(\d+)' => 'TrimestreController@UpdateController',
    ],
    'DELETE' => [
        'area/(\d+)' => 'AreaController@DeleteController',
        'usuario/(\d+)' => 'UsuarioController@DeleteController',
        'punto/(\d+)' => 'PuntoController@DeleteController',
        'trimestre/(\d+)' => 'TrimestreController@DeleteController',
    ],
];
