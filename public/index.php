<?php
declare(strict_types=1);
ini_set('display_errors', '0');
session_start(['cookie_httponly'=>true, 'cookie_samesite'=>'Lax', 'cookie_secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'use_strict_mode'=>true]);
header('Content-Type: text/html; charset=UTF-8');
header("Content-Security-Policy: default-src 'self'; style-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
require dirname(__DIR__) . '/config/conexion.php';
require dirname(__DIR__) . '/app/estudiantes.php';
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
$datos = array_fill_keys(['nombres','apellidos','cedula','correo','carrera','semestre'], '');
$errores = []; $general = ''; $filas = [];
$exito = $_SESSION['exito'] ?? ''; unset($_SESSION['exito']);
try {
    $pdo = conexion();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        [$datos, $errores] = validar($_POST);
        if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
            http_response_code(403); $general = 'La sesión del formulario expiró. Recarga la página e inténtalo de nuevo.';
        } elseif (!$errores) {
            try {
                registrar($pdo, $datos);
                $_SESSION['exito'] = 'Estudiante registrado correctamente.';
                $_SESSION['csrf'] = bin2hex(random_bytes(32));
                header('Location: index.php', true, 303); exit;
            } catch (PDOException $e) {
                if ((int)($e->errorInfo[1] ?? 0) === 1062) {
                    $general = 'Ya existe un estudiante con esa cédula o correo.';
                    http_response_code(409);
                } else { throw $e; }
            }
        } else { http_response_code(422); }
    }
    $filas = consultar($pdo);
} catch (Throwable $e) {
    http_response_code(503);
    error_log('AI3: operación no disponible; tipo=' . get_class($e));
    $general = 'Servicio de datos no disponible. Inicia MySQL e importa database/schema.sql según el README.';
}
?><!doctype html>
<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Campus · Gestión de estudiantes</title><link rel="stylesheet" href="assets/style.css"></head>
<body><header><a class="brand" href="index.php"><span class="logo">E</span> CAMPUS <small>GESTIÓN ACADÉMICA</small></a><span class="tag">ECOTEC · Diseño Web</span></header>
<main><div class="eyebrow">SISTEMA WEB DE GESTIÓN DE ESTUDIANTES</div><h1>El futuro comienza<br>con un registro.</h1><p class="intro">Organiza la información de tus estudiantes en un solo lugar.</p>
<div class="summary"><div><strong><?= count($filas) ?></strong><span>Registros visibles · máximo 500</span></div><div><strong>01 — 12</strong><span>Semestres académicos</span></div><div><strong>PDO</strong><span>Acceso parametrizado a datos</span></div></div>
<?php if ($exito): ?><div role="status" class="notice success"><?= escapar($exito) ?></div><?php endif ?>
<?php if ($general): ?><div role="alert" class="notice"><?= escapar($general) ?></div><?php endif ?>
<div class="layout"><section class="card"><div class="section-title"><span class="number">01</span><div><h2>Nuevo estudiante</h2><p>Todos los campos son obligatorios.</p></div></div>
<form method="post" action="index.php"><input type="hidden" name="csrf" value="<?= escapar($_SESSION['csrf']) ?>">
<?php foreach (['nombres'=>'Nombres','apellidos'=>'Apellidos','cedula'=>'Cédula','correo'=>'Correo electrónico','carrera'=>'Carrera','semestre'=>'Semestre'] as $campo=>$etiqueta): ?>
<div class="field"><label for="<?= $campo ?>"><?= $etiqueta ?></label><input id="<?= $campo ?>" name="<?= $campo ?>" value="<?= escapar($datos[$campo]) ?>" required type="<?= $campo==='correo'?'email':($campo==='semestre'?'number':'text') ?>" <?= $campo==='cedula'?'pattern="[0-9]{10}" inputmode="numeric" minlength="10" maxlength="10"':($campo==='semestre'?'min="1" max="12" step="1"':'maxlength="'.($campo==='nombres'||$campo==='apellidos'?100:150).'"') ?> <?= isset($errores[$campo])?'aria-invalid="true" aria-describedby="error-'.$campo.'"':'' ?>>
<?php if (isset($errores[$campo])): ?><small class="error" id="error-<?= $campo ?>"><?= escapar($errores[$campo]) ?></small><?php endif ?></div>
<?php endforeach ?><button type="submit">Registrar estudiante <span>↗</span></button><p class="form-note">La cédula y el correo deben ser únicos.</p></form></section>
<section class="card records"><div class="section-title"><span class="number">02</span><div><h2>Directorio académico</h2><p>Los registros más recientes aparecen primero.</p></div></div>
<div class="table-wrap" tabindex="0" role="region" aria-label="Directorio de estudiantes"><table><caption>Estudiantes registrados</caption><thead><tr><th>ID</th><th>Estudiante</th><th>Cédula</th><th>Carrera</th><th>Sem.</th><th>Creación</th></tr></thead><tbody>
<?php foreach ($filas as $fila): ?><tr><td><?= escapar($fila['id']) ?></td><td><strong><?= escapar($fila['nombres'].' '.$fila['apellidos']) ?></strong><small><?= escapar($fila['correo']) ?></small></td><td><?= escapar($fila['cedula']) ?></td><td><?= escapar($fila['carrera']) ?></td><td><span class="pill"><?= escapar($fila['semestre']) ?></span></td><td><?= escapar($fila['fecha_creacion']) ?></td></tr><?php endforeach ?>
<?php if (!$filas): ?><tr><td colspan="6" class="empty">Tu directorio está listo.<br><small>Registra al primer estudiante para comenzar.</small></td></tr><?php endif ?>
</tbody></table></div></section></div></main><footer>ECOTEC · Actividad Integradora 3 <span>Desarrollado por Bryan Enrique Moreira Intriago · 2026</span></footer></body></html>
