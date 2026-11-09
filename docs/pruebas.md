# Pruebas de integración

Ejecución real: 2026-11-08T22:14:00.874Z.

PHP 8.3.36, MariaDB 10.11.16, Google Chrome sin interfaz, Windows. Base aislada en work/mysql-evidence-data, puerto 3306; aplicación en 8086 y phpMyAdmin en 8087. Se usó una carpeta de sesiones PHP dentro del espacio de trabajo porque el entorno restringido no permite escribir en el directorio temporal de XAMPP.

- OK - MariaDB disponible en entorno aislado de pruebas
- OK - DDL importado con claves únicas y CHECK
- OK - pruebas PHP de validación
- OK - GET inicial 200 y conexión PDO
- OK - Create + redirección + Read con Unicode y apóstrofe
- OK - duplicado rechazado sin mensajes SQL
- OK - validación de servidor rechaza semestre fuera de rango
- OK - CSRF incorrecto rechazado
- OK - carga SQL tratada como dato
- OK - XSS almacenado codificado y sin elemento ejecutable
- OK - la tabla persiste después de la carga SQL
- OK - segundo registro ficticio
- OK - móvil de 390 px sin desbordamiento de página
- OK - estructura real de la tabla visible en phpMyAdmin
- OK - cabeceras CSP presentes
- OK - fallo de conexión controlado sin credenciales ni detalles internos

Los casos maliciosos y las fichas de ejemplo usan exclusivamente datos ficticios. Las capturas 01, 03, 04 y 06 proceden de esta ejecución. La captura 02 se sustituyó después por un detalle de una captura auténtica del archivo abierto en Notepad++, conservando números de línea y resaltado del editor. Su original se conserva durante la preparación del informe.

Para phpMyAdmin se descargó la distribución oficial 5.2.3 a una carpeta temporal. La prueba reemplazó, solo en ese proceso, el comprobador `is_readable()` de PHP por una comprobación de apertura real con `fopen()`, debido a una incompatibilidad del token restringido de Windows. Esto no concedió permisos, no cambió ACL y no forma parte de la aplicación entregada. La captura muestra la estructura e índices reales consultados a MariaDB.
