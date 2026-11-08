<?php
declare(strict_types=1);

function conexion(): PDO
{
    $local = is_file(__DIR__ . '/local.php') ? require __DIR__ . '/local.php' : [];
    $get = static fn(string $key, string $default): string => (string)(getenv('DB_' . strtoupper($key)) !== false ? getenv('DB_' . strtoupper($key)) : ($local[$key] ?? $default));
    try {
        return new PDO(
            'mysql:host=' . $get('host', '127.0.0.1') . ';port=' . $get('port', '3306') . ';dbname=' . $get('database', 'db_sistema_web') . ';charset=utf8mb4',
            $get('user', 'root'), $get('password', ''),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
        );
    } catch (PDOException $e) {
        error_log('AI3: conexión no disponible; SQLSTATE=' . $e->getCode());
        throw new RuntimeException('No se pudo conectar con la base de datos. Comprueba que MySQL esté iniciado y que el esquema esté importado.');
    }
}
