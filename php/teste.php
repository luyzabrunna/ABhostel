<?php

require_once "bd.php";

try {
    $con = bd::getConexao();
    echo "Conectou!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}