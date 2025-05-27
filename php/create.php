<?php
include("conection.php");

// Verificar si es un intento de inicio de sesión

if (!empty($_POST['emailL'])  && !empty($_POST['passwordL'])) {
    echo 'Entra';
    $loginInput = $_POST['emailL'];
    $password = $_POST['passwordL'];


    // Buscar por correo o nombre de usuario
    $sql = "SELECT NOMBRE_USUARIO, NOMBRE, APELLIDO_PATERNO, CORREO, CONTRASENIA 
            FROM CLIENTE 
            WHERE (CORREO = :login OR NOMBRE_USUARIO = :login) AND CONTRASENIA = :pw";

    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ':login', $loginInput);
    oci_bind_by_name($stid, ':pw', $password);
    oci_execute($stid);

    $row = oci_fetch_assoc($stid);

    if ($row) {
        // Inicio de sesión exitoso
        $params = http_build_query([
            'login' => 'exitoso',
            'usuario' => $row['NOMBRE_USUARIO'],
            'nombre' => $row['NOMBRE'],
            'apellido' => $row['APELLIDO_PATERNO'],
            'correo' => $row['CORREO']
        ]);
        header("Location: ../html/index.html?$params");
        exit(); 
    } else {
        // Credenciales incorrectas
        $params = http_build_query(['login' => 'fallido']);
        header("Location: ../html/index.html?$params");
    }

    oci_free_statement($stid);
    oci_close($conn);
    exit();
} else {
    // Si no es login, entonces es registro
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

    // Asignar ID de membresía
    if ($membershipType === "basica") {
        $membershipTypeId = 1;
    } elseif ($membershipType === "premium") {
        $membershipTypeId = 2;
    } elseif ($membershipType === "vip") {
        $membershipTypeId = 3;
    }

    // Insertar nuevo cliente
    $sql = 'INSERT INTO CLIENTE
(NOMBRE_USUARIO, NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, CORREO, TELEFONO, METODO_PAGO, ID_MEMBRESIA, CONTRASENIA) 
VALUES (:u, :n, :ap, :am, :c, :t, :mp, :idm, :pw)';

    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ':u', $user);
    oci_bind_by_name($stid, ':n', $name);
    oci_bind_by_name($stid, ':ap', $lastName);
    oci_bind_by_name($stid, ':am', $secondLastName);
    oci_bind_by_name($stid, ':c', $email);
    oci_bind_by_name($stid, ':t', $phone);
    oci_bind_by_name($stid, ':mp', $paymentMethod);
    oci_bind_by_name($stid, ':idm', $membershipTypeId);
    oci_bind_by_name($stid, ':pw', $password);

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
    } else {
        $e = oci_error($stid);
        echo "Error al crear el cliente: " . $e['message'];
    }
}



oci_free_statement($stid);
oci_close($conn);
