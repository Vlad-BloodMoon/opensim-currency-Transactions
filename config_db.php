<?php
// Configuration de la base de données
$host = "localhost";
$dbname = "currency";
$username = "userDBname";
$password = "passwordDBname";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Informations d'utilisateur pour l'authentification sur transactions global
$auth_users = array(
    "user" => "password"
);
?>