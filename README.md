#Justificación

La justificación de este proyecto se radica en la necesidad de mejorar el módulo de Transparencia de la Universidad Tecnológica de la Costa. La página actual del panel de Transparencia, debido a su diseño anticuado y su falta de funcionalidad, no ofrece una experiencia de usuario satisfactoria. La información crucial sobre la gestión administrativa y el uso de recursos públicos se encuentra en este módulo, y su accesibilidad es vital para asegurar la transparencia y la confianza del público. Mejorar el diseño y la usabilidad del módulo permitirá a los usuarios acceder a la información de manera más eficiente y agradable, cumpliendo así con los principios de transparencia y buena gestión pública.

# Transparencia UT - Backend

Este repositorio contiene el código fuente del backend para el proyecto de transparencia de la Universidad Tecnológica de la Costa. La aplicación backend está desarrollada en PHP nativo y utiliza PostgreSQL como base de datos.

## Tabla de Contenidos

- [Instalación](#instalación)
- [Uso](#uso)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Contribuciones](#contribuciones)
- [Licencia](#licencia)

## Instalación

Para instalar y ejecutar este proyecto en tu entorno local, sigue estos pasos:

1. **Clona el repositorio:**
    ```sh
    git clone https://github.com/DemonHulk/Transparencia-Backend
    cd transparencia-backend
    ```

2. **Configura la base de datos:**
    - Crea una base de datos en PostgreSQL.
    - Copia el archivo `.env.example` a `.env` y configura las variables de entorno de la base de datos.

3. **Instala las dependencias (si aplica):**
    - Si estás utilizando Composer:
      ```sh
      composer install
      ```

4. **Ejecuta las migraciones para crear las tablas:**
    ```sh
    php migrate.php
    ```

5. **Inicia el servidor:**
    ```sh
    php -S localhost:8000 -t public
    ```

## Uso

### Endpoints Disponibles

- `POST /api/login`: Iniciar sesión.
- `POST /api/register`: Registrar un nuevo usuario.
- `GET /api/documents`: Obtener lista de documentos.
- `POST /api/documents`: Subir un nuevo documento.
- `GET /api/documents/{id}`: Obtener detalles de un documento.
- `PUT /api/documents/{id}`: Actualizar un documento.
- `DELETE /api/documents/{id}`: Eliminar un documento.

## Estructura del Proyecto

- **app/controllers**: Controladores que manejan las solicitudes HTTP.
  - `UserController.php`
  - `DocumentController.php`
  - `CategoryController.php`

- **app/models**: Modelos que interactúan con la base de datos.
  - `User.php`
  - `Document.php`
  - `Category.php`

- **app/middleware**: Middleware para la autenticación y autorización.
  - `AuthMiddleware.php`

- **app/routes**: Definición de rutas de la API.
  - `api.php`

- **public**: Carpeta pública para el servidor web.
  - `index.php`

## Contribuciones

Las contribuciones son bienvenidas. Para contribuir, por favor sigue estos pasos:

1. Realiza un fork del proyecto.
2. Crea una rama con tu nueva funcionalidad (`git checkout -b feature/nueva-funcionalidad`).
3. Realiza un commit de tus cambios (`git commit -am 'Añadir nueva funcionalidad'`).
4. Empuja la rama (`git push origin feature/nueva-funcionalidad`).
5. Crea un nuevo Pull Request.

#Roles

Branko Jaziel Lomelí Ríos
Frontend (Principal)
FullStack (Apoyo)
UI

Marco Fabián Gómez Bautista
Frontend (Principal)
FullStack (Apoyo)
UI

Alexis Guadalupe Rivera Cabrera
Tester (Principal)
Quality Assurance
UX

Daniel Contreras Zamarripa
Backend (Principal)
Analista
DBA
UX

Ernesto Ibarra Villanueva
Backend (Principal)
Analista
DBA

Marco Antonio Núñez Andrade
Scrum Master
