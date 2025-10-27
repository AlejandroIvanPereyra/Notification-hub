# Notification-Hub - Challenge Backend Sirius

**Notification-Hub**
es un backend de REST API que permite a los usuarios enviar mensajes a distintos servicios como Telegram, Slack, Discord o Teams, usando un canal o chat específico. Está diseñado para ser usado en un entorno dockerizado y poder escalarse a más servicios.

---

## Tecnologías utilizadas

* PHP 8+ con Laravel 10
* PostgreSQL como base de datos
* Redis para caching y límites diarios
* Nginx como servidor web
* Docker y Docker Compose para orquestación de contenedores

---

## Instalación

1. Clonar el repositorio:
git clone https://github.com/AlejandroIvanPereyra/Notification-hub.git
cd Notification-hub
2. Crear el archivo de variables de entorno `.env` (puedes copiar el `.env.example`):
cp .env.example .env
3. Configurar variables de entorno en `.env`, incluyendo conexión a base de datos y claves de servicios externos (Telegram, Slack, Discord, Teams, etc.).
4. Levantar los contenedores con Docker Compose:
docker compose up --build -d
5. Ejecutar migraciones y seeders:
docker compose exec app php artisan migrate:fresh --seed

## Endpoints principales

### Autenticación
POST /api/auth/register
POST /api/auth/login


* Registro y login de usuarios.
* Devuelven un JWT para autenticación en los endpoints protegidos.

### Mensajería
POST /api/messages/send
* Envía un mensaje a uno o varios servicios.
* Requiere JWT y aplica límite diario por usuario (`daily.limit` middleware).

GET /api/messages
* Lista los mensajes enviados por el usuario autenticado o todos si es administrador.
* Requiere JWT.

### Métricas (solo administradores)
GET /api/metrics
* Devuelve métricas diarias de mensajes por usuario.
* Requiere JWT y rol de administrador (`admin.auth` middleware).

## Middleware y restricciones

* `jwt.auth`: asegura que el usuario esté autenticado con JWT.
* `daily.limit`: controla la cantidad máxima de mensajes diarios por usuario (configurable vía `.env`).
* `admin.auth`: restringe acceso a métricas solo a usuarios administradores.

---

## Uso
Para su uso y prueba el proyecto se encuentra documentado con swagger, para acceder a la documentacion utilizar el endpoint /api/documentation

---

## Licencia

* MIT License
