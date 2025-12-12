<?php
namespace App\Core;

class Controller
{
    protected $twig;

    public function __construct()
    {
        // Inicializa Twig
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../Views');
        $this->twig = new \Twig\Environment($loader, [
            'cache' => false, // Desabilita cache em desenvolvimento
            'debug' => true
        ]);
        
        // Adiciona extensão de debug
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        
        // Inicia sessão se não estiver iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    //Renderiza uma view Twig
    protected function render($view, $data = [])
    {
        // Adiciona dados globais disponíveis em todas as views
        $data['session'] = $_SESSION ?? [];
        $data['get'] = $_GET ?? [];
        $data['post'] = $_POST ?? [];
        
        echo $this->twig->render($view, $data);
    }

    //Redireciona para uma URL
    protected function redirect($url)
    {
        header("Location: " . $url);
        exit;
    }

    //Retorna JSON
    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
