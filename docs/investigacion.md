# Investigación técnica: PDO y MySQLi

Universidad ECOTEC · Diseño Web · Actividad Integradora 3.  
Autor: Bryan Enrique Moreira Intriago. Consulta de documentación: 8 de noviembre de 2026.

## Comparación y decisión

| Criterio | PDO | MySQLi |
|---|---|---|
| Arquitectura | Interfaz orientada a objetos con controladores para distintos motores. | Extensión específica para MySQL y MariaDB, con interfaces orientada a objetos y procedural. |
| Abstracción | Ofrece una API común de conexión, preparación y recuperación de resultados. | Expone una API centrada en las funcionalidades de la familia MySQL. |
| Portabilidad | Permite mantener parte del código de acceso al cambiar de motor, si existe su controlador. | Requiere sustituir la capa de acceso al migrar a otra familia de motores. |
| Rendimiento | Debe medirse con consultas y datos representativos. La capa de acceso es solo una parte de la petición. | Tampoco garantiza una ventaja general: influyen consultas, índices, latencia y volumen de resultados. |
| Seguridad | Admite sentencias preparadas y parámetros. Concatenar entradas en SQL sigue siendo peligroso. | También admite sentencias preparadas; la seguridad exige utilizarlas correctamente. |
| Excepciones | `PDO::ERRMODE_EXCEPTION` permite capturar `PDOException` explícitamente. | Puede configurarse con `MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT` para lanzar `mysqli_sql_exception`. |

La tabla resume las capacidades de las API descritas por el manual oficial [1]. No se realizó un benchmark comparativo; sería incorrecto asignar un porcentaje de ventaja sin medir una misma carga y configuración. En este módulo, el acceso mediante claves únicas y la limitación de resultados son decisiones más directamente relacionadas con el trabajo solicitado.

Seleccioné PDO porque una interfaz orientada a objetos consistente permite aislar la conexión y las operaciones del almacenamiento en módulos pequeños. Los parámetros nombrados hacen legible el vínculo entre campos y valores. Las excepciones facilitan distinguir un duplicado de una indisponibilidad de datos y controlar qué mensajes recibe el usuario.

PDO no es un ORM ni traduce automáticamente los dialectos SQL. Cambiar a PostgreSQL, por ejemplo, exigiría revisar el DDL de `AUTO_INCREMENT`, los tipos y restricciones, el DSN y las consultas dependientes del motor. La abstracción de la API mejora la portabilidad del acceso; no vuelve universal el esquema.

## Medidas de seguridad del proyecto

La inserción utiliza una plantilla SQL fija y seis marcadores nombrados. `execute($datos)` transmite los valores por separado. Se desactiva la emulación con `PDO::ATTR_EMULATE_PREPARES => false` para usar la preparación nativa de MySQL. Una entrada como `Robert'); DROP TABLE estudiantes; --` se almacena como dato y no modifica la consulta [2]. Los parámetros representan valores: no se deben usar para nombres de tablas o palabras clave; estos permanecen fijos en el código.

La conexión usa `utf8mb4` y `PDO::ERRMODE_EXCEPTION`. `try-catch` controla los errores sin imprimir el mensaje original del motor, rutas internas ni credenciales. El registro de incidencias conserva solo el tipo de fallo o SQLSTATE, y la interfaz muestra mensajes operativos genéricos. MySQLi también puede gestionar excepciones; no se eligió PDO porque MySQLi carezca de esa capacidad [3][5].

Para reducir XSS, `escapar()` codifica la información al emitirla en texto y atributos HTML, usando `htmlspecialchars(..., ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')` [4]. Codificar la salida HTML no reemplaza las consultas parametrizadas y tampoco es una regla universal para contextos JavaScript o URL. Esta aplicación no interpola datos del estudiante en esos contextos.

Se agregaron validación de servidor y HTML5, token CSRF, cookies de sesión HttpOnly/SameSite, CSP, restricciones únicas y un rango de semestre. Después de una inserción correcta, una redirección HTTP 303 evita repetirla al recargar. El módulo no incorpora autenticación porque la rúbrica pide inserción y consulta; su demostración se realiza localmente con datos ficticios.

## Diseño relacional y normalización

`estudiantes` tiene ocho atributos atómicos y una clave sustituta `id`. Las claves candidatas `cedula` y `correo` se protegen con índices únicos. La cédula es texto para conservar ceros iniciales y no realizar operaciones aritméticas. `fecha_creacion` se genera en el servidor de datos con `CURRENT_TIMESTAMP`.

En el alcance de una ficha por estudiante, los valores dependen de la clave; no se introducen grupos repetidos ni atributos como el nombre de facultad derivados de una carrera. Se puede describir el diseño como compatible con tercera forma normal bajo estas dependencias. Que varios estudiantes cursen la misma carrera no viola por sí solo la normalización. Si se requieren catálogos de carreras, facultades o matrículas históricas, deben agregarse tablas y relaciones específicas. El diseño actual guarda una carrera y un semestre vigentes por estudiante.

## Fuentes primarias

1. PHP Group. Choosing an API. https://www.php.net/manual/en/mysqlinfo.api.choosing.php
2. PHP Group. PDO: Prepared statements and stored procedures. https://www.php.net/manual/en/pdo.prepared-statements.php
3. PHP Group. PDO: Errors and error handling. https://www.php.net/manual/en/pdo.error-handling.php
4. PHP Group. htmlspecialchars. https://www.php.net/manual/en/function.htmlspecialchars.php
5. PHP Group. mysqli_report. https://www.php.net/manual/en/mysqli-driver.report-mode.php
