<?php

return [
    'GET' => [
        'area' => 'AreaController@QueryAllController',
        'area/(.+)' => 'AreaController@QueryOneController',
        'areaAct/(.+)' => 'AreaController@ActivateController',
        'areaAct' => 'AreaController@QueryActController',
        'puntoAct/(.+)' => 'PuntoController@ActivateController',
        'usuario' => 'UsuarioController@QueryAllController',
        'usuario/(.+)' => 'UsuarioController@QueryOneController',
        'punto' => 'PuntoController@QueryAllController',
        'punto/(.+)' => 'PuntoController@QueryOneController',

        'areapunto_punto/(.+)' => 'PuntosAreasController@QueryAreaPunto_PuntoController',
        'puntosareas/allpuntosaccesoarea/(.+)' => 'PuntosAreasController@QueryAllPuntosAccesoAreaController',
        'userAcces/(.+)' => 'UsuarioController@QueryAllUsuariosAccesoAreaController',
        'contenido' => 'ContenidoController@QueryAllController',
        'contenido/(\d+)' => 'ContenidoController@QueryOneController',
        'trimestre' => 'TrimestreController@QueryAllController',
        'trimestre/(.+)' => 'TrimestreController@QueryOneController',
        'activarUser/(.+)' => 'UsuarioController@ActivateController',
        'ejercicio' => 'EjercicioController@QueryAllController',
        'ejercicio/(.+)' => 'EjercicioController@QueryOneController',
        'ejercicioAct/(.+)' => 'EjercicioController@ActivateController',
        'trimestreAct/(.+)' => 'TrimestreController@ActivateController',
    ],
    'POST' => [
        'area' => 'AreaController@InsertController',
        'usuario' => 'UsuarioController@InsertController',
        'punto' => 'PuntoController@InsertController',
        'trimestre' => 'TrimestreController@InsertController',
        'verificarUser' => 'UsuarioController@VerificarUserController',
        'puntosareas/insertoractivate_puntoArea' => 'PuntosAreasController@InsertOrActivate_PuntoAreaController',
        'puntosareas/desactivate_puntoarea' => 'PuntosAreasController@Desactivate_PuntoAreaController',
        'ejercicio' => 'EjercicioController@InsertController',
    ],
    'PUT' => [
        'area/(.+)' => 'AreaController@UpdateController',
        'usuario/(.+)' => 'UsuarioController@UpdateController',
        'punto/(.+)' => 'PuntoController@UpdateController',
        'trimestre/(.+)' => 'TrimestreController@UpdateController',
        'ejercicio/(.+)' => 'EjercicioController@UpdateController',
    ],
    'DELETE' => [
        'area/(.+)' => 'AreaController@DeleteController',
        'usuario/(.+)' => 'UsuarioController@DeleteController',
        'punto/(.+)' => 'PuntoController@DeleteController',
        'trimestre/(.+)' => 'TrimestreController@DeleteController',
        'ejercicio/(.+)' => 'EjercicioController@DeleteController',
    ],
];


