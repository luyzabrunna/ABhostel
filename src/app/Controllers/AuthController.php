<?php
// src/app/Controllers/AuthController.php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use PDO;

class AuthController
{
    private function getDbConnection()
    {
        // Se você usa bd.php, pode chamar bd::getConexao()
        require_once __DIR__ . '/../../php/bd.php'; // ← Ajuste o caminho
        return bd::getConexao();
    }

    public function login()
    {
        session_start();
        if (!empty($_SESSION['logged_in'])) {
            header('Location: /imovel/list');
            exit;
        }

        $loader = new FilesystemLoader(__DIR__ . '/../../Views');
        $twig = new Environment($loader);

        $error = isset($_GET['erro']) ? 'E-mail ou senha inválidos.' : '';

        echo $twig->render('auth/login.html.twig', [
            'title' => 'Login - ABHostel',
            'error' => $error
        ]);
    }

    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['senha'] ?? '';

        try {
            $pdo = $this->getDbConnection();
            $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['senha'])) {
                session_start();
                $_SESSION['logged_in'] = true;
                $_SESSION['user_email'] = $email;
                header('Location: /painel');
                exit;
            } else {
                header('Location: /login?erro=1');
                exit;
            }
        } catch (Exception $e) {
            error_log("Erro no login: " . $e->getMessage());
            header('Location: /login?erro=1');
            exit;
        }
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: /login');
        exit;
    }
}