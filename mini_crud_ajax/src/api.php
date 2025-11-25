<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

/* ------------------------------------------------------------
   FUNCIONES DE RESPUESTA
------------------------------------------------------------ */
function responder_json_exito(mixed $contenidoDatos = [], int $codigoHttp = 200): void
{
    http_response_code($codigoHttp);
    echo json_encode(['ok' => true, 'data' => $contenidoDatos], JSON_UNESCAPED_UNICODE);
    exit;
}

function responder_json_error(string $mensajeError, int $codigoHttp = 400): void
{
    http_response_code($codigoHttp);
    echo json_encode(['ok' => false, 'error' => $mensajeError], JSON_UNESCAPED_UNICODE);
    exit;
}

function existeEmailDuplicado(array $usuarios, string $emailNormalizado): bool
{
    foreach ($usuarios as $u) {
        if (isset($u['email']) && mb_strtolower($u['email']) === $emailNormalizado) {
            return true;
        }
    }
    return false;
}

/* ------------------------------------------------------------
   ARCHIVO DE DATOS
------------------------------------------------------------ */
$rutaArchivoDatosJson = __DIR__ . '/data.json';

if (!file_exists($rutaArchivoDatosJson)) {
    file_put_contents($rutaArchivoDatosJson, json_encode([]));
}

$listaUsuarios = json_decode(file_get_contents($rutaArchivoDatosJson), true);
if (!is_array($listaUsuarios)) {
    $listaUsuarios = [];
}

/* ------------------------------------------------------------
   RUTEO
------------------------------------------------------------ */
$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$accion = $_GET['action'] ?? ($_POST['action'] ?? 'list');

/* ------------------------------------------------------------
   LISTAR
------------------------------------------------------------ */
if ($metodo === 'GET' && $accion === 'list') {
    responder_json_exito($listaUsuarios);
}

/* ------------------------------------------------------------
   LOGIN
------------------------------------------------------------ */
if ($metodo === 'POST' && $accion === 'login') {
    $raw = file_get_contents('php://input');
    $json = $raw ? json_decode($raw, true) : [];

    $email = trim($json['email'] ?? $_POST['email'] ?? '');
    $contrasena = trim($json['contrasena'] ?? $_POST['contrasena'] ?? '');

    if ($email === '' || $contrasena === '') {
        responder_json_error('Email y contraseña obligatorios.', 422);
    }

    $usuarioEncontrado = null;
    foreach ($listaUsuarios as $u) {
        if (isset($u['email']) && mb_strtolower($u['email']) === mb_strtolower($email)) {
            $usuarioEncontrado = $u;
            break;
        }
    }

    if (!$usuarioEncontrado) {
        responder_json_error('Credenciales incorrectas.', 401);
    }

    // Verificar contraseña
    if (!password_verify($contrasena, $usuarioEncontrado['contrasena'])) {
        responder_json_error('Credenciales incorrectas.', 401);
    }

    // Login exitoso
    responder_json_exito([
        'mensaje' => 'Login correcto',
        'rol' => $usuarioEncontrado['rol'] ?? 'usuario'
    ]);
}

/* ------------------------------------------------------------
   CREAR
------------------------------------------------------------ */
if ($metodo === 'POST' && $accion === 'create') {

    $raw = file_get_contents('php://input');
    $json = $raw ? json_decode($raw, true) : [];

    error_log("RAW: " . $raw);
    error_log("JSON: " . print_r($json, true));

    $nombre     = trim($json['nombre']     ?? $_POST['nombre']     ?? '');
    $contrasena = trim($json['contrasena'] ?? $_POST['contrasena'] ?? '');
    $email      = trim($json['email']      ?? $_POST['email']      ?? '');
    $rol = trim($json['rol'] ?? $_POST['rol'] ?? '');


    $emailNorm = mb_strtolower($email);

    // Validaciones
    if ($nombre === '' || $email === '' || $contrasena === '' || $rol === '') {
        responder_json_error('Nombre, email, contraseña y rol son obligatorios.', 422);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        responder_json_error('Email inválido.', 422);
    }

    if (mb_strlen($nombre) > 60) responder_json_error('Nombre demasiado largo.', 422);
    if (mb_strlen($email) > 120) responder_json_error('Email demasiado largo.', 422);
    if (mb_strlen($contrasena) > 120) responder_json_error('Contraseña demasiado larga.', 422);

    if (existeEmailDuplicado($listaUsuarios, $emailNorm)) {
        responder_json_error('Ya existe un usuario con ese email.', 409);
    }

    // Hash real
    $hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar
    $listaUsuarios[] = [
        'nombre'     => $nombre,
        'contrasena' => $hash,
        'email'      => $emailNorm,
        'rol'        => $rol
    ];

    file_put_contents($rutaArchivoDatosJson, json_encode($listaUsuarios, JSON_PRETTY_PRINT));

    responder_json_exito($listaUsuarios, 201);
}

/* ------------------------------------------------------------
   ELIMINAR
------------------------------------------------------------ */
if (($metodo === 'POST' || $metodo === 'DELETE') && $accion === 'delete') {

    $raw = file_get_contents('php://input');
    $json = $raw ? json_decode($raw, true) : [];

    $index = $_GET['index'] ?? $json['index'] ?? $_POST['index'] ?? null;

    if ($index === null) {
        responder_json_error('Falta index.', 422);
    }

    $index = (int)$index;

    if (!isset($listaUsuarios[$index])) {
        responder_json_error('Índice inexistente.', 404);
    }

    unset($listaUsuarios[$index]);
    $listaUsuarios = array_values($listaUsuarios);

    file_put_contents($rutaArchivoDatosJson, json_encode($listaUsuarios, JSON_PRETTY_PRINT));

    responder_json_exito($listaUsuarios);
}

/* ------------------------------------------------------------
   DEFAULT
------------------------------------------------------------ */
responder_json_error('Acción no soportada.', 400);
