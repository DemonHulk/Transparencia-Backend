<?php

return [
    'GET' => [
        'area' => 'AreaController@QueryAllController',
        'area/(\d+)' => 'AreaController@QueryOneController',
        'area/activar/(\d+)' => 'AreaController@ActivateController',
        'usuario' => 'UsuarioController@QueryAllController',
        'usuario/(\d+)' => 'UsuarioController@QueryOneController',
        'punto' => 'PuntoController@QueryAllController',
        'punto/(\d+)' => 'PuntoController@QueryOneController',
        'puntosareas/allpuntosaccesoarea/(\d+)' => 'PuntosAreasController@QueryAllPuntosAccesoAreaController',
        'usuario/usuariosaccesoarea/(\d+)' => 'UsuarioController@QueryAllUsuariosAccesoAreaController',
        'contenido' => 'ContenidoController@QueryAllController',
        'contenido/(\d+)' => 'ContenidoController@QueryOneController',
        'trimestre' => 'TrimestreController@QueryAllController',
        'trimestre/(\d+)' => 'TrimestreController@QueryOneController',
        'usuario/activar/(\d+)' => 'UsuarioController@ActivateController',
    ],
    'POST' => [
        'area' => 'AreaController@InsertController',
        'usuario' => 'UsuarioController@InsertController',
        'punto' => 'PuntoController@InsertController',
        'trimestre' => 'TrimestreController@InsertController',
        'verificarUser' => 'UsuarioController@VerificarUserController',
        'puntosareas/insertoractivate_puntoArea' => 'PuntosAreasController@InsertOrActivate_PuntoAreaController',
        'puntosareas/desactivate_puntoarea' => 'PuntosAreasController@Desactivate_PuntoAreaController',
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


