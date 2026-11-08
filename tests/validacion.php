<?php
declare(strict_types=1);
require dirname(__DIR__) . '/app/estudiantes.php';
function comprobar(bool $ok, string $caso): void {
    if (!$ok) throw new RuntimeException('FALLO: ' . $caso);
    echo 'OK: ' . $caso . PHP_EOL;
}
$base = ['nombres'=>'Ana María','apellidos'=>"O'Connor",'cedula'=>'0000000001','correo'=>'ANA@example.test','carrera'=>'Sistemas','semestre'=>'3'];
[$datos,$errores] = validar($base);
comprobar(!$errores && $datos['correo']==='ana@example.test', 'datos válidos, Unicode y normalización del correo');
foreach (['0','13','1.5','1e1','-1','abc'] as $semestre) {
    [, $errores] = validar(array_replace($base,['semestre'=>$semestre]));
    comprobar(isset($errores['semestre']), 'rechazo semestre ' . $semestre);
}
foreach (['','123','000000000x','00000000001'] as $cedula) {
    [, $errores] = validar(array_replace($base,['cedula'=>$cedula]));
    comprobar(isset($errores['cedula']), 'rechazo cédula inválida');
}
[, $errores] = validar(array_replace($base,['nombres'=>str_repeat('á',101),'correo'=>['no escalar']]));
comprobar(isset($errores['nombres'],$errores['correo']), 'longitud Unicode y entrada no escalar');
comprobar(escapar('<script>alert("x")</script>') === '&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;', 'codificación HTML contra XSS');
[, $errores] = validar([]);
comprobar(count($errores)===6, 'seis campos obligatorios');
