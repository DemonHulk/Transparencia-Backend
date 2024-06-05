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
    ],
    'POST' => [
        'area' => 'AreaController@InsertController',
        'usuario' => 'UsuarioController@InsertController',
        'punto' => 'PuntoController@InsertController',
        'contenido/documento' => 'ContenidoController@InsertDocumentoController',
        'contenido/contenido' => 'ContenidoController@InsertContenidoController',
    ],
    'PUT' => [
        'area/(\d+)' => 'AreaController@UpdateController',
        'usuario/(\d+)' => 'UsuarioController@UpdateController',
        'punto/(\d+)' => 'PuntoController@UpdateController',
        'contenido/(\d+)' => 'ContenidoController@UpdateController',
    ],
    'DELETE' => [
        'area/(\d+)' => 'AreaController@DeleteController',
        'usuario/(\d+)' => 'UsuarioController@DeleteController',
        'punto/(\d+)' => 'PuntoController@DeleteController',
        'contenido/(\d+)' => 'ContenidoController@DeleteController',
    ],
];
