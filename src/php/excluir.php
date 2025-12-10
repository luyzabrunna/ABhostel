<?php
require_once "/php/bd.php";
session_start();

// VERIFICA LOGIN
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$conn = bd::getConexao();

$id = $_GET['id'];

$sql_select = $conn->prepare("SELECT * FROM imoveis WHERE id = :id");
$sql_select->bindValue(":id", $id);
$sql_select->execute();

$imovel = $sql_select->fetch(PDO::FETCH_OBJ);

if(!$imovel){
    die("Imovel não encontrado");
}

$sql = $conn->prepare("DELETE FROM imoveis WHERE id = :id");
$sql->bindValue(":id", $id);
$sql->execute();

header("Location: /php/painel.php");