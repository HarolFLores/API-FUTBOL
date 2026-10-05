# FutReto API Documentation

Esta es la documentación oficial de los endpoints RESTful generados para **FutReto**. Toda la API consume y emite formato JSON.

## 🛠 Consideraciones Generales (Para el Equipo Kotlin/Android)
1. **Autenticación (JWT)**: Todos los endpoints protegidos (la mayoría) requieren el envío del JWT en el header de la solicitud usando Retrofit o Ktor.
   * `Authorization: Bearer {tu_token_aqui}`
2. **Accept Header**: Siempre enviar `Accept: application/json` para asegurar que Laravel retorne validaciones en estructura JSON y no HTML.
3. **Url Base**: `http://retofut.incanatoapps.com/api/`

---

## 1. 🔐 Autenticación (`Auth`)
Rutas públicas para acceder a la aplicación.

### Registro de Usuario
* **Endpoint:** `POST /auth/register`
* **Body Request:**
  * `name` (String, Obligatorio)
  * `email` (String, Obligatorio, Único)
  * `password` (String, Obligatorio, Min 6)
  * `password_confirmation` (String, Obligatorio, Igual a password)
* **Response:** Crea automáticamente las estadísticas (`player_stats`) y perfil vacío. Devuelve Token JWT.

### Login
* **Endpoint:** `POST /auth/login`
* **Body Request:** `email`, `password`
* **Response:** Token JWT y tiempo de expiración.

### Refresh / Me / Logout
* **GET `/auth/me`**: Devuelve los datos del usuario logueado.
* **POST `/auth/logout`**: Invalida el token actual.
* **POST `/auth/refresh`**: Refresca el JWT.

---

## 2. 👤 Perfiles de Jugador (`Profile`)
Endpoints para el "Perfil Deportivo" del usuario.

### Mi Perfil
* **Endpoint:** `GET /profile`
* **Response:** Lista el usuario, sus perfiles deportivos extra y sus métricas de juego (Goles, Asistencias, Cartas, etc).

### Actualizar Mi Perfil
* **Endpoint:** `PUT /profile`
* **Body Request (Opcionales):**
  * `nombre` (Actualiza al modelo User)
  * `telefono` 
  * `posicion_principal` (Ej: 'POR', 'DEF', 'MED', 'DEL')
  * `foto` (Url string)

### Tarjeta Jugador (Card FIFA)
* **Endpoint:** `GET /profile/{id}/card`
* **Response:** Objeto compacto con la media general, Ritmo, Disparo, Pase, Defensa, Físico y Regate para dibujar la "Card".

---

## 3. 🛡️ Equipos (`Teams`)
Para crear y gestionar equipos amateur.

### Listar / Crear / Ver
* **GET `/teams`**: Lista de equipos.
* **POST `/teams`**: Crea un equipo.
  * *Body*: `nombre`, `logo` (url), `ciudad`, `descripcion`, `formato_preferido`.
  * *Efecto*: Crea el equipo y asigna automáticamente al emisor como "Capitán".
* **GET `/teams/{id}`**: Perfil detallado con lista de miembros (`players`).
* **PUT `/teams/{id}`**: (Solo Capitanes/Admin) Actualiza el equipo.

### Unión e Invitaciones
* **POST `/teams/{id}/invite`**: (Capitán) Invita a un usuario pasándole `user_id` en el body.
* **POST `/teams/{id}/join`**: Si un usuario recibió invitación, pasa a ser "activo". Si el equipo es abierto, ingresa automáticamente.

### Tarjeta Equipo (Card)
* **GET `/teams/{id}/card`**: Stats condensadas de desempeño Defensivo, Mediocampo y Ataque para el UI.

---

## 4. ⚔️ Retos Oficiales (`Challenges`)
Para desafiar a otros equipos.

### Crear Reto
* **Endpoint:** `POST /challenges`
* **Body:**
  * `challenger_team_id` (Que equipo retará, obligatorio)
  * `challenged_team_id` (Opcional, si es NULL es un reto 'abierto' al primero que llegue)
  * `tipo_reto` ('directo' o 'abierto')
  * `fecha_propuesta` (Date > hoy)
  * `hora_propuesta` (H:i)
  * `ubicacion`, `formato`, `nivel_sugerido`, `costo_compartido`

### Respuestas al Reto
* **POST `/challenges/{id}/accept`**: Acepta el reto. ¡Esto crea automáticamente un Partido Oficial (`GameMatch`)!
* **POST `/challenges/{id}/reject`**: Manda a rechazado.
* **POST `/challenges/{id}/counter`**: Permite enviar `fecha_propuesta` revisada (Contraoferta).

---

## 5. ⚽ Partidos Tipo Equipo (`Matches`)
Encuentros oficiales entre agrupaciones formadas.

* **GET `/matches`**: Listado.
* **GET `/matches/{id}`**: Detalles con miembros de ambos equipos (Team A vs Team B).
* **POST `/matches/{id}/confirm`**: Para que el jugador confirme que ASISTIRÁ físicamente a dicho partido.

---

## 6. 🏟️ Partidos Libres (Pichangas / `Free Matches`)
Donde jugadores sueltos ("Solos") juegan sin pertenecer al mismo equipo.

* **GET `/free-matches`**: Lista de encuentros libres abiertos.
* **POST `/free-matches`**: Crear partido libre. Requiere: `fecha`, `hora`, `ubicacion`, `formato` (Ej: 'futbol7').
* **POST `/free-matches/{id}/join`**: Inscribirse a esta "pichanga".
* **POST `/free-matches/{id}/leave`**: Salirse de los inscritos.
* **POST `/free-matches/{id}/teams`**: (Solo el Organizador). 
  * *Body:* `assignments` (Array de diccionarios con `user_id` y `side: "team_a" o "team_b"`). Empieza el partido.

---

## 7. 📊 Módulo de Resultados (Validación)
El cierre ciclo de los partidos (Funciona igual para equipos o libres).

### Subir Resultado
* **Endpoint:** `POST /matches/{id}/result`
* **Body:**
  * `team_a_score` y `team_b_score` (Integer)
  * `details`: Array opcional de individualidades. Ej: `[{"user_id": 5, "team_id": 2, "goles": 2, "mvp": true, "tarjeta_roja": false}]`
* *Nota:* Deja el estado del partido en `esperando_aprobacion`.

### Votar / Aprobar
* **Endpoint:** `POST /matches/{id}/result/approve`
* *Efecto:* Suma un voto. Si llega a 3 (Quórum), valida estadísticamente a los equipos y cierra el match (`finalizado`).

### Disputas / AntiFraude
* **Endpoint:** `POST /matches/{id}/result/dispute`
* **Body:** `motivo` (String), `evidencia_url` (String, Url).
* *Efecto:* Saca el partido del pipeline normal y llama un administrador (`en_disputa`).
