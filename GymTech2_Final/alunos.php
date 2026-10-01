<?php
session_start();
require_once 'conexao.php';

// Validação de segurança: apenas recepcionistas ou administradores podem gerir alunos
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_perfil'] !== 'recepcao' && $_SESSION['usuario_perfil'] !== 'admin')) {
    header('Location: index.php?erro=acesso_negado');
    exit;
}

$acao = $_REQUEST['acao'] ?? '';
$redirect_url = (isset($_SESSION['usuario_perfil']) && $_SESSION['usuario_perfil'] === 'admin') ? 'admin.php#alunos' : 'index.php#alunos';

switch ($acao) {
    case 'salvar':
        $id = $_POST['aluno_id'] ?? '';
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $data_nascimento = !empty($_POST['data_nascimento']) ? $_POST['data_nascimento'] : null;
        $sexo = trim($_POST['sexo'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $celular = trim($_POST['celular'] ?? '');
        $plano = $_POST['plano'] ?? 'Plano Essencial';
        $status = $_POST['status'] ?? 'Ativo';
        $modalidade = $_POST['modalidade'] ?? 'Musculação';
        $objetivo = trim($_POST['objetivo'] ?? '');
        $peso_inicial = !empty($_POST['peso_inicial']) ? $_POST['peso_inicial'] : null;
        $altura = !empty($_POST['altura']) ? $_POST['altura'] : null;

        // Validação dos campos obrigatórios NOT NULL
        if (empty($nome) || empty($cpf) || empty($email) || empty($data_nascimento) || empty($sexo)) {
            header('Location: ' . $redirect_url . '&erro=campos_obrigatorios');
            exit;
        }

        // Gestão opcional do upload de foto/avatar
        $caminho_foto = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($extensao, $extensoes_permitidas)) {
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }
                $nome_arquivo = 'aluno_' . time() . '_' . uniqid() . '.' . $extensao;
                $caminho_completo = 'uploads/' . $nome_arquivo;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_completo)) {
                    $caminho_foto = $caminho_completo;
                }
            }
        }

        if (!empty($id)) {
            // Atualização de aluno existente
            if ($caminho_foto) {
                $sql = "UPDATE alunos SET 
                        nome = :nome, cpf = :cpf, email = :email, endereco = :endereco, data_nascimento = :data_nascimento, 
                        sexo = :sexo,
                        celular = :celular, plano = :plano, status = :status, modalidade = :modalidade, 
                        objetivo = :objetivo, peso_inicial = :peso_inicial, altura = :altura, avatar = :avatar 
                        WHERE id = :id";
                $params = [
                    ':nome' => $nome, ':cpf' => $cpf, ':email' => $email, ':endereco' => $endereco,
                    ':data_nascimento' => $data_nascimento, 
                    ':sexo' => $sexo,':celular' => $celular,
                    ':plano' => $plano, ':status' => $status, ':modalidade' => $modalidade,
                    ':objetivo' => $objetivo, ':peso_inicial' => $peso_inicial,
                    ':altura' => $altura, ':avatar' => $caminho_foto, ':id' => $id
                ];
            } else {
                $sql = "UPDATE alunos SET 
                        nome = :nome, cpf = :cpf, email = :email, endereco = :endereco, data_nascimento = :data_nascimento, 
                        celular = :celular, sexo = :sexo, 
                        plano = :plano, status = :status, modalidade = :modalidade, 
                        objetivo = :objetivo, peso_inicial = :peso_inicial, altura = :altura 
                        WHERE id = :id";
                $params = [
                    ':nome' => $nome, ':cpf' => $cpf, ':email' => $email, ':endereco' => $endereco,
                    ':data_nascimento' => $data_nascimento, 
                    ':sexo' => $sexo,
                    ':celular' => $celular,
                    ':plano' => $plano, ':status' => $status, ':modalidade' => $modalidade,
                    ':objetivo' => $objetivo, ':peso_inicial' => $peso_inicial,
                    ':altura' => $altura, ':id' => $id
                ];
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        } else {
            // Inserção de novo aluno (com avatar padrão se não houver upload)
            if (!empty($caminho_foto)) {
            // Se o aluno enviou uma foto, mantém a foto enviada
            $avatar_final = $caminho_foto;
        } else {
            // Sem foto: escolhe automaticamente o holograma pelo sexo
            if ($sexo === 'Feminino') {
            $avatar_final = 'img/holograma_fem.png';
        } else {
            $avatar_final = 'img/holograma_masc.jpg';
            }
        }
            
            $sql = "INSERT INTO alunos (nome, cpf, email, endereco, data_nascimento, sexo, celular, plano, status, modalidade, objetivo, peso_inicial, altura, avatar, data_cadastro) 
                    VALUES (:nome, :cpf, :email, :endereco, :data_nascimento, :sexo, :celular, :plano, :status, :modalidade, :objetivo, :peso_inicial, :altura, :avatar, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome, 
                ':cpf' => $cpf, 
                ':email' => $email, 
                ':endereco' => $endereco,
                ':data_nascimento' => $data_nascimento, 
                ':sexo' => $sexo,
                ':celular' => $celular,
                ':plano' => $plano, 
                ':status' => $status, 
                ':modalidade' => $modalidade,
                ':objetivo' => $objetivo, 
                ':peso_inicial' => $peso_inicial,
                ':altura' => $altura, 
                ':avatar' => $avatar_final
            ]);
        }

        header('Location: ' . $redirect_url);
        exit;

    case 'excluir':
        $id = $_GET['id'] ?? '';
        if (!empty($id)) {
            $stmt = $pdo->prepare("DELETE FROM alunos WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        header('Location: ' . $redirect_url);
        exit;

    default:
        header('Location: ' . $redirect_url);
        exit;
}
?>