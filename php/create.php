<?php
include("conection.php");

// Recoger datos del formulario
$name = $_POST['name'];
$user = $_POST['user'];
$lastName = $_POST['lastName'];
$secondLastName = $_POST['secondLastName'];
$email = $_POST['email'];
$password = $_POST['password'];
$phone = $_POST['phone'];
$paymentMethod = (int)$_POST['paymentMethod'];
$membershipType = $_POST['membershipType'];
$membershipTypeId = null;

if ($membershipType === "Básica") {
    $membershipTypeId = 1;
} elseif ($membershipType === "Premium") {
    $membershipTypeId = 2;
} elseif ($membershipType === "VIP") {
    $membershipTypeId = 3;
}

// Consulta SQL con nombres de parámetros válidos
$sql = 'INSERT INTO CLIENTE
(NOMBRE_USUARIO, NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, CORREO, TELEFONO, METODO_PAGO, ID_MEMBRESIA, CONTRASENIA) 
VALUES (:u, :n, :ap, :am, :c, :t, :mp, :idm, :pw)';

$stid = oci_parse($conn, $sql);

// Enlazar parámetros
oci_bind_by_name($stid, ':u', $user);
oci_bind_by_name($stid, ':n', $name);
oci_bind_by_name($stid, ':ap', $lastName);
oci_bind_by_name($stid, ':am', $secondLastName);
oci_bind_by_name($stid, ':c', $email);
oci_bind_by_name($stid, ':t', $phone);
oci_bind_by_name($stid, ':mp', $paymentMethod);
oci_bind_by_name($stid, ':idm', $membershipTypeId);
oci_bind_by_name($stid, ':pw', $password);

// Ejecutar
$result = oci_execute($stid);

if ($result) {
    $params = http_build_query([
        'registro' => 'exitoso',
        'usuario' => $user,
        'nombre' => $name,
        'apellido' => $lastName,
        'correo' => $email
    ]);
    header("Location: ../html/index.html?$params");
    exit();
} else {
    $e = oci_error($stid);
    echo "Error al crear el cliente: " . $e['message'];
}

oci_free_statement($stid);
oci_close($conn);
?>
