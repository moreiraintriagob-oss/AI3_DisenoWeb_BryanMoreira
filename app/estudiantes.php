<?php
declare(strict_types=1);

function escapar(mixed $valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function validar(array $entrada): array
{
    $datos = []; $errores = [];
    foreach (['nombres'=>100, 'apellidos'=>100, 'cedula'=>10, 'correo'=>150, 'carrera'=>150, 'semestre'=>2] as $campo=>$limite) {
        $datos[$campo] = is_string($entrada[$campo] ?? null) ? trim($entrada[$campo]) : '';
        if ($datos[$campo] === '' || mb_strlen($datos[$campo], 'UTF-8') > $limite) {
            $errores[$campo] = 'Campo obligatorio, máximo ' . $limite . ' caracteres.';
        }
    }
    if (!preg_match('/^[0-9]{10}$/D', $datos['cedula'])) $errores['cedula'] = 'La cédula debe contener exactamente 10 dígitos.';
    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) $errores['correo'] = 'Ingresa un correo válido.';
    $datos['correo'] = mb_strtolower($datos['correo'], 'UTF-8');
    if (!preg_match('/^(?:[1-9]|1[0-2])$/D', $datos['semestre'])) $errores['semestre'] = 'El semestre debe estar entre 1 y 12.';
    return [$datos, $errores];
}

function registrar(PDO $pdo, array $datos): void
{
    $stmt = $pdo->prepare('INSERT INTO estudiantes (nombres, apellidos, cedula, correo, carrera, semestre) VALUES (:nombres, :apellidos, :cedula, :correo, :carrera, :semestre)');
    $stmt->execute($datos);
}

function consultar(PDO $pdo): array
{
    return $pdo->query('SELECT id, nombres, apellidos, cedula, correo, carrera, semestre, fecha_creacion FROM estudiantes ORDER BY id DESC LIMIT 500')->fetchAll();
}
