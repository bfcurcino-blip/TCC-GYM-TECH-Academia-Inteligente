<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil'] !== 'admin') {
    header('Location: index.php?erro=acesso_negado');
    exit;
}

$acao = $_REQUEST['acao'] ?? '';

switch ($acao) {
    case 'salvar':
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');
        $perfil = $_POST['perfil'] ?? 'recepcao';

        if (!empty($nome) && !empty($email) && !empty($senha)) {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, usuario, senha, perfil) VALUES (:nome, :email, :email, :senha, :perfil)");
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':senha' => $senha,
                ':perfil' => $perfil
            ]);
        }
        header('Location: admin.php#equipe');
        exit;

    case 'excluir':
        $id = $_GET['id'] ?? '';
        if (!empty($id) && $id != $_SESSION['usuario_id']) {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        header('Location: admin.php#equipe');
        exit;

    default:
        header('Location: admin.php#equipe');
        exit;
}
?>