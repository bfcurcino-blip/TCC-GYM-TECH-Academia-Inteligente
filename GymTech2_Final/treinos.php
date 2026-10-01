<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['aluno_id'])) {
    header('Location: index.php');
    exit;
}

$acao = $_REQUEST['acao'] ?? '';
$redirect_url = (isset($_SESSION['usuario_perfil']) && $_SESSION['usuario_perfil'] === 'admin') ? 'admin.php#treinos' : 'index.php#treinos';

switch ($acao) {
    case 'concluir':
        $id = $_GET['id'] ?? '';
        if (!empty($id)) {
            $stmt = $pdo->prepare("UPDATE treinos SET status = 'Concluído' WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }

        if (isset($_SESSION['aluno_id'])) {
            header('Location: portal_aluno.php');
        } else {
            header('Location: ' . $redirect_url);
        }
        exit;

    case 'excluir':
        $id = $_GET['id'] ?? '';
        if (!empty($id)) {
            $stmt = $pdo->prepare("DELETE FROM treinos WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        header('Location: ' . $redirect_url);
        exit;

    default:
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $aluno_id = $_POST['aluno_id'] ?? '';
            $nome = trim($_POST['nome'] ?? '');
            $categoria = $_POST['categoria'] ?? 'Musculação';
            $observacoes = trim($_POST['observacoes'] ?? '');

            if (!empty($aluno_id) && !empty($nome)) {
                $stmt = $pdo->prepare("INSERT INTO treinos (aluno_id, nome, categoria, observacoes, status) VALUES (:aluno_id, :nome, :categoria, :observacoes, 'Em Andamento')");
                $stmt->execute([
                    ':aluno_id' => $aluno_id,
                    ':nome' => $nome,
                    ':categoria' => $categoria,
                    ':observacoes' => $observacoes
                ]);
            }
        }
        header('Location: ' . $redirect_url);
        exit;
}
?>