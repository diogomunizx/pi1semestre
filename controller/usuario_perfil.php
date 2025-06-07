<?php
session_start();
require_once '../model/Usuario.php';

header('Content-Type: application/json');

// Apenas usuários logados podem acessar

$id_usuario = $_SESSION['id_Docente'];

$usuarioModel = new Usuario();
$dados = $usuarioModel->buscarPorId($id_usuario);

if ($dados) {
    echo json_encode($dados);
} else {
    exit;}

    header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// seu código que busca dados
exit;