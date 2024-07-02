<?php
return [
    'GET' => [
        'area' => ['controller' => 'AreaController', 'action' => 'QueryAllController'],
        'area/(.+)' => ['controller' => 'AreaController', 'action' => 'QueryOneController'],
        'areaAct/(.+)' => ['controller' => 'AreaController', 'action' => 'ActivateController'],
        'areaAct' => ['controller' => 'AreaController', 'action' => 'QueryActController'],
        'puntoAct/(.+)' => ['controller' => 'PuntoController', 'action' => 'ActivateController'],
        'usuario' => ['controller' => 'UsuarioController', 'action' => 'QueryAllController'],
        'usuario/(.+)' => ['controller' => 'UsuarioController', 'action' => 'QueryOneController'],
        'punto' => ['controller' => 'PuntoController', 'action' => 'QueryAllController'],
        'punto/(.+)' => ['controller' => 'PuntoController', 'action' => 'QueryOneController'],
        'contenidoDinamicoAct/(.+)' => ['controller' => 'ContenidoDinamicoController', 'action' => 'ActivateController'],
        'areapunto_punto/(.+)' => ['controller' => 'PuntosAreasController', 'action' => 'QueryAreaPunto_PuntoController'],
        'puntosareas/allpuntosaccesoarea/(.+)' => ['controller' => 'PuntosAreasController', 'action' => 'QueryAllPuntosAccesoAreaController'],
        'userAcces/(.+)' => ['controller' => 'UsuarioController', 'action' => 'QueryAllUsuariosAccesoAreaController'],
        'contenido' => ['controller' => 'ContenidoController', 'action' => 'QueryAllController'],
        'contenido/(\d+)' => ['controller' => 'ContenidoController', 'action' => 'QueryOneController'],
        'trimestre' => ['controller' => 'TrimestreController', 'action' => 'QueryAllController'],
        'trimestre/(.+)' => ['controller' => 'TrimestreController', 'action' => 'QueryOneController'],
        'activarUser/(.+)' => ['controller' => 'UsuarioController', 'action' => 'ActivateController'],
        'ejercicio' => ['controller' => 'EjercicioController', 'action' => 'QueryAllController'],
        'ejercicio/(.+)' => ['controller' => 'EjercicioController', 'action' => 'QueryOneController'],
        'ejercicioAct/(.+)' => ['controller' => 'EjercicioController', 'action' => 'ActivateController'],
        'trimestreAct/(.+)' => ['controller' => 'TrimestreController', 'action' => 'ActivateController'],
        'titulosAct/(.+)' => ['controller' => 'TitulosController', 'action' => 'ActivateController'],
        'puntoUser/(.+)' => ['controller' => 'PuntoController', 'action' => 'QueryPuntoUserController'],
        'titulo/(.+)' => ['controller' => 'TitulosController', 'action' => 'QueryOneController'],
        'titulosCompletos/(.+)' => ['controller' => 'TitulosController', 'action' => 'mostrarTitulosSubtitulos'],
        'tituloPadre/(.+)' => ['controller' => 'TitulosController', 'action' => 'QueryTituloPadre'],
        'titulosdepunto/(.+)' => ['controller' => 'TitulosController', 'action' => 'QueryTitulosPuntoController'],
        'titulosmaspunto/(.+)' => ['controller' => 'TitulosController', 'action' => 'QueryTitulosMasPuntoController'],
        'subtitulosPorTema/(.+)' => ['controller' => 'TitulosController', 'action' => 'mostrarSubtitulosByTitulo'],
        'subtituloData/(.+)' => ['controller' => 'TitulosController', 'action' => 'QueryOneControllerSubtema'],
        'contenidoDinamico/(.+)' => ['controller' => 'ContenidoDinamicoController', 'action' => 'QueryAllController'],
        'onecontenidoDinamico/(.+)' => ['controller' => 'ContenidoDinamicoController', 'action' => 'QueryOneController'],
        'contenidoEstatico/(.+)' => ['controller' => 'ContenidoEstaticoController', 'action' => 'QueryAllController'],
        'contenidoEstaticoAct/(.+)' => ['controller' => 'ContenidoEstaticoController', 'action' => 'ActivateController'],
        'oneContenidoEstatico/(.+)' => ['controller' => 'ContenidoEstaticoController', 'action' => 'QueryOneController'],
        'getDocument/(.+)' => ['controller' => 'ContenidoDinamicoController', 'action' => 'getDocument'],
        'historial' => ['controller' => 'HistorialController', 'action' => 'QueryAllVistosController'],
        'historialNoVisto' => ['controller' => 'HistorialController', 'action' => 'QueryAllNoVistosController'],
        'verHistorial/(.+)' => ['controller' => 'HistorialController', 'action' => 'verController'],
        'historialAct/(.+)' => ['controller' => 'HistorialController', 'action' => 'ActivateController'],
    ],
    'POST' => [
        'area' => ['controller' => 'AreaController', 'action' => 'InsertController'],
        'usuario' => ['controller' => 'UsuarioController', 'action' => 'InsertController'],
        'punto' => ['controller' => 'PuntoController', 'action' => 'InsertController'],
        'trimestre' => ['controller' => 'TrimestreController', 'action' => 'InsertController'],
        'verificarUser' => ['controller' => 'UsuarioController', 'action' => 'VerificarUserController'],
        'puntosareas/insertoractivate_puntoArea' => ['controller' => 'PuntosAreasController', 'action' => 'InsertOrActivate_PuntoAreaController'],
        'puntosareas/desactivate_puntoarea' => ['controller' => 'PuntosAreasController', 'action' => 'Desactivate_PuntoAreaController'],
        'ejercicio' => ['controller' => 'EjercicioController', 'action' => 'InsertController'],
        'titulos' => ['controller' => 'TitulosController', 'action' => 'InsertController'],
        'subtitulo' => ['controller' => 'TitulosController', 'action' => 'InsertSubtemaController'],
        'contenidoDinamico' => ['controller' => 'ContenidoDinamicoController', 'action' => 'InsertDocumentoController'],
        'UpdatecontentDinamico/(.+)' => ['controller' => 'ContenidoDinamicoController', 'action' => 'UpdateDocumentoController'],
        'contenidoEstatico' => ['controller' => 'ContenidoEstaticoController', 'action' => 'InsertContenidoEstaticoController'],
        'orderPuntos' => ['controller' => 'PuntoController', 'action' => 'UpdateOrderPuntos'],
        'buscarPDF' => ['controller' => 'ContenidoDinamicoController', 'action' => 'SearchFile'],
        'VerifySesion' => ['controller' => 'UsuarioController', 'action' => 'VerifySesion'],
    ],
    'PUT' => [
        'area/(.+)' => ['controller' => 'AreaController', 'action' => 'UpdateController'],
        'usuario/(.+)' => ['controller' => 'UsuarioController', 'action' => 'UpdateController'],
        'punto/(.+)' => ['controller' => 'PuntoController', 'action' => 'UpdateController'],
        'trimestre/(.+)' => ['controller' => 'TrimestreController', 'action' => 'UpdateController'],
        'ejercicio/(.+)' => ['controller' => 'EjercicioController', 'action' => 'UpdateController'],
        'titulos/(.+)' => ['controller' => 'TitulosController', 'action' => 'UpdateController'],
        'subtitulo/(.+)' => ['controller' => 'TitulosController', 'action' => 'UpdateControllerSubtema'],
        'UpdatecontentEstatico/(.+)' => ['controller' => 'ContenidoEstaticoController', 'action' => 'UpdateController'],
    ],
    'DELETE' => [
        'area/(.+)' => ['controller' => 'AreaController', 'action' => 'DeleteController', 'require_session' => true],
        'usuario/(.+)' => ['controller' => 'UsuarioController', 'action' => 'DeleteController', 'require_session' => true],
        'punto/(.+)' => ['controller' => 'PuntoController', 'action' => 'DeleteController', 'require_session' => true],
        'trimestre/(.+)' => ['controller' => 'TrimestreController', 'action' => 'DeleteController', 'require_session' => true],
        'ejercicio/(.+)' => ['controller' => 'EjercicioController', 'action' => 'DeleteController', 'require_session' => true],
        'titulos/(.+)' => ['controller' => 'TitulosController', 'action' => 'DeleteController', 'require_session' => true],
        'contenidoDinamico/(.+)' => ['controller' => 'ContenidoDinamicoController', 'action' => 'DeleteController', 'require_session' => true],
        'contenidoEstatico/(.+)' => ['controller' => 'ContenidoEstaticoController', 'action' => 'DeleteController', 'require_session' => true],
        'historial/(.+)' => ['controller' => 'HistorialController', 'action' => 'DeleteController', 'require_session' => true],
    ],
];
