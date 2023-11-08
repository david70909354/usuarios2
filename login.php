<?php
session_start(); // Agregar esta línea para iniciar la sesión

require 'database.php';

$message = ''; // Inicializar la variable de mensaje

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['email']) && !empty($_POST['password'])) {
    // Utiliza un bloque try-catch para manejar cualquier excepción potencial
    try {
      $email = $_POST['email'];
      $password = $_POST['password'];

      // Comprueba si el usuario ya existe en la base de datos
      $existingUser = $conn->prepare('SELECT id FROM usuarios WHERE email = :email');
      $existingUser->bindParam(':email', $email);
      $existingUser->execute();

      if ($existingUser->fetchColumn() === false) {
        // El usuario no existe, procede a crearlo
        $sql = "INSERT INTO usuarios (email, password) VALUES (:email, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $passwordHash);

        if ($stmt->execute()) {
          $message = 'Usuario creado exitosamente';
        } else {
          $message = 'Lo sentimos, hubo un problema al crear tu cuenta';
        }
      } else {
        $message = 'Este correo electrónico ya está registrado.';
        
      }
    } catch (PDOException $e) {
      $message = 'Error: ' . $e->getMessage(); // Maneja cualquier error de conexión a la base de datos
    }
  } else {
    $message = 'Por favor, completa todos los campos.';
  }
}
?>





<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="estilos3.css">
  </head>
  <body>
   

    <?php if(!empty($message)): ?>
      <p> <?= $message ?></p>
    <?php endif; ?>

    <h1>Login</h1>
    <span>or <a href="signup.php">SignUp</a></span>

    <form action="login.php" method="POST">
      <input name="email" type="text" placeholder="Entrar tu email">
      <input name="password" type="password" placeholder="Enter your Password">
      <input type="submit" value="Submit">
      <a href="index.php">Inicio</a>
    </form>
  </body>
</html>