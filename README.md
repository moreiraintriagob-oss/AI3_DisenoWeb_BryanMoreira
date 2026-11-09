# Sistema Web de Gestión de Estudiantes

Actividad Integradora 3 · Diseño Web · Universidad ECOTEC.

**Estudiante:** Bryan Enrique Moreira Intriago.  
**Repositorio público:** https://github.com/moreiraintriagob-oss/AI3_DisenoWeb_BryanMoreira

Aplicación modular PHP 8 con PDO y MySQL/MariaDB. Implementa las operaciones **Create y Read** exigidas en la rúbrica: registro y directorio de estudiantes. No requiere Composer ni servicios de terceros.

## Ejecutar con XAMPP en Windows

1. Inicia **Apache** y **MySQL** desde XAMPP Control Panel.
2. Coloca esta carpeta en `C:\xampp\htdocs\AI3_DisenoWeb_BryanMoreira`.
3. Abre `http://localhost/phpmyadmin`. En **Importar**, selecciona `database/schema.sql` y pulsa **Importar**. El script crea `db_sistema_web` sin borrar datos existentes.
4. Abre `http://localhost/AI3_DisenoWeb_BryanMoreira/public/`.
5. Completa los seis campos y pulsa **Registrar estudiante**. El directorio mostrará el nuevo registro.

Configuración inicial: servidor `127.0.0.1`, puerto `3306`, usuario local XAMPP `root`, contraseña vacía. Si tu instalación usa otros valores, copia `config/example.php` como `config/local.php` y establece allí tus valores; ese archivo está excluido de Git. También se admiten `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USER`, `DB_PASSWORD` como variables de entorno y tienen prioridad. No subas credenciales al repositorio.

### Alternativa sin copiar a htdocs

Con MySQL iniciado y el esquema importado, abre PowerShell en esta carpeta:

```powershell
& C:\xampp\php\php.exe -S 127.0.0.1:8083 -t public
```

Visita `http://127.0.0.1:8083/`. El servidor incorporado es exclusivamente para desarrollo local. Deténlo con `Ctrl+C`.

## Estructura

```text
database/schema.sql    DDL e integridad de datos
config/conexion.php    conexión PDO y manejo de errores
config/example.php     ejemplo sin secretos
app/estudiantes.php    validación, inserción y consulta
public/index.php       controlador y vista HTML
public/assets/         estilos responsivos
tests/validacion.php   pruebas de validación y codificación HTML
docs/                  investigación, evidencias e informe
```

Publica únicamente `public/` como raíz web al desplegar con un virtual host. No sirvas las carpetas privadas. La ruta sencilla con `htdocs` es para el laboratorio local. Esta actividad no incluye autenticación de usuarios: utiliza únicamente datos ficticios durante la demostración y añade autenticación y control de acceso antes de usar datos reales en un servidor público.

## Reglas y seguridad

- Cédula: texto de exactamente 10 dígitos; conserva ceros iniciales. Se valida formato, no el dígito verificador ecuatoriano, que no se exige en el enunciado.
- Correo válido y máximo 150 caracteres; cédula y correo únicos también en la base.
- Semestre entre 1 y 12, decisión de implementación; campos de nombres de 100 caracteres y carrera de 150.
- PDO con `ERRMODE_EXCEPTION`, `utf8mb4` y preparaciones nativas. `prepare()` + `execute()` separan los valores de la estructura SQL. PDO por sí solo no evita inyecciones.
- `htmlspecialchars()` con `ENT_QUOTES | ENT_SUBSTITUTE` y UTF-8 codifica toda salida dinámica de usuario en HTML.
- Validación tanto HTML5 como PHP, token CSRF por sesión, cookies HttpOnly/SameSite, CSP y redirección 303 tras insertar.
- Duplicados responden con 409, datos inválidos con 422, CSRF con 403 y fallo de datos con 503. Los mensajes del motor y las credenciales no se muestran al navegador.
- El directorio muestra los últimos 500 registros para limitar la carga; el contador indica registros visibles, no el total histórico.

## Verificación

```powershell
& C:\xampp\php\php.exe tests/validacion.php
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { & C:\xampp\php\php.exe -l $_.FullName }
```

La evidencia de integración HTTP y MariaDB se conserva en `docs/pruebas.md`. Las capturas se encuentran en `docs/evidencias/`. El informe debe leerse junto con la URL pública del repositorio para verificar los commits reales.

## Resolver problemas

**Servicio de datos no disponible:** comprueba MySQL, el puerto y la importación del DDL. **Ya existe un estudiante:** usa otra cédula y correo ficticios. **Token expirado:** recarga el formulario. **could not find driver:** habilita `pdo_mysql` en PHP y reinicia Apache. **Función mb_strlen no disponible:** habilita `mbstring`.

PHP requerido: 8.0 o superior, extensiones `pdo_mysql` y `mbstring`; MySQL 8.0.16+ o MariaDB 10.4+ para aplicar las restricciones CHECK del esquema.
