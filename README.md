# WidiTrade: Prueba de API de Contenido

Este proyecto implementa una API para un sitio de marketplace, permitiendo a los usuarios registrados publicar contenidos y reseñas. La API está desarrollada en Symfony y cumple con los requisitos específicos descritos a continuación.

## Requisitos del Proyecto

- Utilizar Symfony para el desarrollo de la API.
- Dockerizar el proyecto para facilitar su despliegue y evaluación.
- Implementar autenticación JWT para gestionar usuarios y sesiones.
- Desarrollar endpoints CRUD para gestionar contenidos multimedia y operaciones de marketplace.


## Instalación del Proyecto

Para instalar y ejecutar este proyecto en tu entorno local, sigue los siguientes pasos:

1. **Clonar el repositorio:**

   ```bash
   git clone <url-del-repositorio>
   cd <nombre-del-proyecto>
   ```

2. **Instalar dependencias:**

   ```bash
   composer install
   ```

3. **Configurar variables de entorno:**

   Crea un archivo `.env.local` basado en `.env` y configura las variables necesarias, como la conexión a la base de datos y las claves de cifrado para JWT.

4. **Crear la base de datos:**

   ```bash
   php bin/console doctrine:database:create
   ```

5. **Ejecutar migraciones:**

   ```bash
   php bin/console doctrine:migrations:migrate
   ```

6. **Iniciar el servidor Symfony:**

   ```bash
   symfony server:start
   ```

   La API estará disponible en `http://localhost:8000` por defecto.

---


## Endpoints de la API

A continuación se detallan los endpoints implementados, indicando los métodos HTTP, parámetros de entrada y salida, así como una descripción de cada uno:

### Autenticación

- `POST /api/register`

  **Descripción**: Registro de un nuevo usuario en el sistema.

  **Body:**
  ```json
  {
    "email": "usuario@example.com",
    "password": "password"
  }
  ```

- `POST /api/login`

  **Descripción**: Autenticación de un usuario y obtención de token JWT.

  **Body:**
  ```json
  {
    "email": "usuario@example.com",
    "password": "password"
  }
  ```

  **Respuesta Exitosa:**
  ```json
  {
    "token": "eyJhbGciOiJIUzI1NiIsIn..."
  }
  ```

### Perfil de Usuario

- `GET /api/user`

  **Descripción**: Obtener los datos del usuario autenticado.

  **Authorization Header:**
  ```
  Authorization: Bearer <token>
  ```

- `PUT /api/user`

  **Descripción**: Actualizar el perfil del usuario autenticado.

  **Authorization Header:**
  ```
  Authorization: Bearer <token>
  ```

  **Body (opcional):**
  ```json
  {
    "email": "nuevoemail@example.com",
    "password": "nuevapassword"
  }
  ```

### Contenido

- `POST /api/content`

  **Descripción**: Crear un nuevo contenido en el marketplace.

  **Authorization Header:**
  ```
  Authorization: Bearer <token>
  ```

  **Body:**
  ```json
  {
    "title": "Nuevo Artículo",
    "description": "Descripción del nuevo artículo",
    "content": "Contenido enriquecido del nuevo artículo"
  }
  ```

- `GET /api/content`

  **Descripción**: Obtener una lista de contenidos filtrados por título o descripción.

  **Parámetros de consulta opcionales:**
  - `title`: Filtra por título que haga match parcial.

- `GET /api/content/{id}`

  **Descripción**: Obtener detalles específicos de un contenido por su ID.

- `PUT /api/content/{id}`

  **Descripción**: Actualizar un contenido existente en el marketplace.

  **Authorization Header:**
  ```
  Authorization: Bearer <token>
  ```

  **Body:**
  ```json
  {
    "title": "Título actualizado",
    "description": "Descripción actualizada",
    "content": "Contenido enriquecido actualizado"
  }
  ```

- `DELETE /api/content/{id}`

  **Descripción**: Eliminar un contenido del marketplace.

  **Authorization Header:**
  ```
  Authorization: Bearer <token>
  ```

### Marketplace

- `POST /api/content/{id}/rate`

  **Descripción**: Valorar un contenido específico.

  **Authorization Header:**
  ```
  Authorization: Bearer <token>
  ```

  **Body:**
  ```json
  {
    "value": 5
  }
  ```

- `POST /api/content/{id}/favorite`

  **Descripción**: Marcar un contenido como favorito.

  **Authorization Header:**
  ```
  Authorization: Bearer <token>
  ```

- `GET /api/content/favorites`

  **Descripción**: Obtener una lista de contenidos marcados como favoritos por el usuario autenticado.

  **Authorization Header:**
  ```
  Authorization: Bearer <token>
  ```

----

## Propuestas de Mejoras Adicionales

1. **Implementación de Paginación y Ordenamiento**

   **Justificación**: Actualmente, los endpoints que devuelven listados de contenidos no incluyen paginación ni opción de ordenamiento. Implementar paginación permitirá manejar grandes volúmenes de datos de manera eficiente, reduciendo la carga en el servidor y mejorando la experiencia del usuario al dividir los resultados en páginas manejables. Además, ofrecer opciones de ordenamiento (por ejemplo, por fecha, popularidad, etc.) proporcionará flexibilidad al usuario para explorar y encontrar contenido relevante de manera más efectiva.

2. **Integración de Servicio de Almacenamiento en la Nube para Contenidos Multimedia**

   **Justificación**: En lugar de utilizar un sistema de almacenamiento de archivos local, integrar un servicio de almacenamiento en la nube (como AWS S3, Google Cloud Storage o Azure Blob Storage) ofrece varias ventajas. Mejora la escalabilidad al permitir almacenar grandes cantidades de datos sin preocuparse por la capacidad del servidor local. Además, proporciona redundancia y durabilidad mejorada de los datos, así como opciones avanzadas de seguridad y gestión de acceso a archivos.

3. **Implementación de Auditoría y Registro de Actividades (Logs)**

   **Justificación**: Mantener un registro detallado de todas las actividades y eventos dentro de la aplicación (auditoría) es esencial para la supervisión de seguridad, la solución de problemas y la cumplimiento de normativas. Los registros deben incluir acciones como acceso a endpoints, cambios en datos sensibles, y operaciones de alta seguridad (como cambios en la contraseña o eliminación de datos). Estos registros no solo ayudan a detectar y responder rápidamente a posibles amenazas, sino que también son valiosos para la trazabilidad y la auditoría interna.


----

### Sección de Licencia y Autor

#### Licencia

Este proyecto está licenciado bajo la [GNU General Public License v3.0 (GPL-3.0)](https://www.gnu.org/licenses/gpl-3.0.html).

#### Autor

El autor de este proyecto es Aythami Melián Perdomo (<ajmelper@gmail.com>), desarrollado en junio de 2024 bajo petición de la empresa Widitrade.

