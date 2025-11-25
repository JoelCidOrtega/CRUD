<?php

require_once 'includes/function.php';

$nombre = trim($_POST['nombre'] ?? ''); 
$gusto = trim($_POST['gusto'] ?? '');
$fecha = trim($_POST['fecha'] ?? '');
$hora = trim($_POST['hora'] ?? '');
$preferencia = trim($_POST['preferencia'] ?? '');
$compa = ($_POST['compa'] ?? []);
$neg = ($_POST['neg'] ?? []);
$motivo = trim($_POST['motivo'] ?? '');
$ayuda = trim($_POST['ayuda'] ?? '');
$color = trim($_POST['color'] ?? '');
$comentarios = trim($_POST['comentarios'] ?? '');
$lenguajes = ($_POST['lenguajes'] ?? []);
$stack = trim($_POST['stack'] ?? '');
$roles = ($_POST['roles'] ?? []);
$disponibilidad = ($_POST['disponibilidad'] ?? []);
$observaciones = trim($_POST['observaciones'] ?? '');
$nivel = trim($_POST['nivel'] ?? '');
$ide = trim($_POST['ide'] ?? '');
$git = trim($_POST['git'] ?? '');
$comunicacion = trim($_POST['comunicacion'] ?? '');
$fecha_envio = trim(date('Y-m-d H:i:s'));



$errors = [];
if (strlen($_POST['nombre']) === 0) {
    $errors['nombre'] = 'El nombre es obligatorio.';
}
if (strlen($_POST['nombre']) < 3) {
    $errors['nombre'] = 'El nombre debe tener al menos 3 caracteres.';
}
if (strlen($_POST['fecha']) === 0) {
    $_POST['fecha'] = 'La fecha es obligatoria.';
}

if (strlen($_POST['hora']) === 0) {
    $errors['hora'] = 'La hora es obligatoria.';
}
if (strlen($_POST['gusto']) === 0) {
    $errors['gusto'] = 'El gusto es obligatorio.';
}
if (strlen($_POST['gusto']) <1 || strlen($_POST['gusto']) >5) {
    $errors['gusto'] = 'El gusto debe estar entre 1 y 5.';
}
if (strlen($_POST['preferencia']) === 0) {
    $errors['preferencia'] = 'La preferencia es obligatoria.';
}
if (empty($_POST['compa'])) {
    $errors['compa'] = 'Debes seleccionar al menos un compañero.';
}
if (empty($_POST['neg'])) {
    $errors['neg'] = 'Debes seleccionar al menos un compañero que no deseas.';
}
if (strlen($_POST['motivo']) === 0) {
    $errors['motivo'] = 'El motivo es obligatorio.';
}
if (strlen($_POST['ayuda']) === 0) {
    $errors['ayuda'] = 'La ayuda es obligatoria.';
}
if (strlen($_POST['color']) === 0) {
    $errors['color'] = 'El color es obligatorio.';
}
if (strlen($_POST['comentarios']) === 0) {
    $errors['comentarios'] = 'Los comentarios son obligatorios.';
}
if (empty($_POST['lenguajes'])) {
    $errors['lenguajes'] = 'Debes seleccionar al menos un lenguaje.';
}
if (strlen($_POST['stack']) === 0) {
    $errors['stack'] = 'El stack es obligatorio.';
}
if (empty($_POST['roles'])) {
    $errors['roles'] = 'Debes seleccionar al menos un rol.';
}
if (empty($_POST['disponibilidad'])) {
    $errors['disponibilidad'] = 'Debes seleccionar al menos una disponibilidad.';
}
if (strlen($_POST['observaciones']) === 0) {
    $errors['observaciones'] = 'Las observaciones son obligatorias.';
}
if (strlen($_POST['nivel']) === 0) {
    $errors['nivel'] = 'El nivel es obligatorio.';
}
if (strlen($_POST['ide']) === 0) {
    $errors['ide'] = 'El IDE es obligatorio.';
}
if (strlen($_POST['git']) === 0) {
    $errors['git'] = 'La respuesta sobre Git es obligatoria.';
}
if (strlen($_POST['comunicacion']) === 0) {
    $errors['comunicacion'] = 'Las preferencias de comunicación son obligatorias.';
}


 if ($errors) {
     $old_field = $_POST;
     include 'includes/header.php';
     include 'index.php';
     include 'includes/footer.php';
 }

    $file = 'data/sociograma.json';
$data = load_json($file);
 
 
$registro = $_POST;
$registro['fecha_guardado_servidor'] = date('Y-m-d H:i:s');
 
$data[] = $registro;
 
 
if (!save_json($file, $data)) {
    http_response_code(500);
    echo "Error interno del servidor: No se pudo guardar el archivo JSON.";
    exit;
}
 
include 'includes/header.php';
?>
 
<main class="container">
    <h2>¡Gracias, <?= htmlspecialchars($_POST['nombre']) ?>!</h2>
    <p>Respuesta guardada correctamente.</p>
    <p><a href="index.php">Volver al formulario</a></p>
    <p>Ver respuestas: <a href="api.php" target="_blank">api.php</a></p>
</main>