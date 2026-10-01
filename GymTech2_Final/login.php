<?php
session_start();
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entrada = trim($_POST['usuario'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($entrada) || empty($senha)) {
        header('Location: index.php?erro=1');
        exit;
    }

    // 1. Tenta autenticar na tabela de funcionários/usuários (admin, recepção, professor)
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :entrada OR email = :entrada OR nome = :entrada LIMIT 1");
    $stmt->execute([':entrada' => $entrada]);
    $user = $stmt->fetch();

    if ($user && ($senha === $user['senha'] || password_verify($senha, $user['senha']))) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_perfil'] = $user['perfil'];

        if ($user['perfil'] === 'admin') {
            header('Location: admin.php');
        } else {
            header('Location: index.php');
        }
        exit;
    }

    // 2. Tenta autenticar na tabela de alunos (por e-mail ou CPF)
    $stmt_aluno = $pdo->prepare("SELECT * FROM alunos WHERE email = :entrada OR cpf = :entrada LIMIT 1");
    $stmt_aluno->execute([':entrada' => $entrada]);
    $aluno = $stmt_aluno->fetch();

    if ($aluno && ($senha === $aluno['senha'] || password_verify($senha, $aluno['senha']))) {
        $_SESSION['aluno_id'] = $aluno['id'];
        $_SESSION['aluno_nome'] = $aluno['nome'];
        header('Location: portal_aluno.php');
        exit;
    }

    header('Location: index.php?erro=1');
    exit;
}