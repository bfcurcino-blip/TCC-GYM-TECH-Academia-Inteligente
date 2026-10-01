<?php
session_start();
require_once 'conexao.php';

$mensagem_sucesso = '';
$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha_inicial = 'aluno@123';
    $senha = password_hash($senha_inicial, PASSWORD_DEFAULT);
    $celular = trim($_POST['celular'] ?? '');
    $data_nascimento = $_POST['data_nascimento'] ?? null;
    $sexo = trim($_POST['sexo'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $modalidade = $_POST['modalidade'] ?? 'Musculação';
    $objetivo = trim($_POST['objetivo'] ?? '');

    if (empty($nome) || empty($cpf) || empty($email)) {
        $mensagem_erro = "Preencha todos os campos obrigatórios (*).";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO alunos (nome, cpf, email, senha, celular, data_nascimento, sexo, endereco, modalidade, objetivo, plano, status) 
                                   VALUES (:nome, :cpf, :email, :senha, :celular, :data_nascimento, :endereco, :modalidade, :objetivo, 'Plano Essencial', 'Ativo')");
            $stmt->execute([
                ':nome' => $nome,
                ':cpf' => $cpf,
                ':email' => $email,
                ':senha' => $senha,
                ':celular' => $celular,
                ':data_nascimento' => $data_nascimento ?: null,
                ':sexo' => $sexo,
                ':endereco' => $endereco,
                ':modalidade' => $modalidade,
                ':objetivo' => $objetivo
            ]);
            $mensagem_sucesso = "Cadastro realizado com sucesso! Você já pode fazer login.";
        } catch (PDOException $e) {
            $mensagem_erro = "Erro ao cadastrar: CPF ou E-mail já estão cadastrados.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gym Tech - Auto Cadastro do Aluno</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem 1rem;">

  <div class="login-card" style="max-width: 580px; width: 100%;">
    <div class="login-header">
      <img src="logo.png" alt="GYM Tech Logo" class="brand-logo-lg">
      <span>CRIE SUA CONTA DE ALUNO</span>
    </div>

    <?php if ($mensagem_erro): ?>
      <div class="alert-error">
        <i data-lucide="alert-circle" style="width: 18px; height: 18px;"></i>
        <span><?php echo htmlspecialchars($mensagem_erro); ?></span>
      </div>
    <?php endif; ?>

    <?php if ($mensagem_sucesso): ?>
      <div class="alert-error" style="background-color: rgba(74, 222, 128, 0.15); border-color: rgba(74, 222, 128, 0.4); color: #4ade80;">
        <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
        <span><?php echo htmlspecialchars($mensagem_sucesso); ?></span>
      </div>
    <?php endif; ?>

    <form action="cadastro_aluno.php" method="POST" class="form-body">
      <div class="input-row">
        <div class="input-group">
          <label for="nome">Nome completo *</label>
          <input type="text" id="nome" name="nome" placeholder="Digite seu nome" required>
        </div>
        <div class="input-group">
          <label for="cpf">CPF *</label>
          <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>
        </div>
      </div>

      <div class="input-row">
        <div class="input-group">
          <label for="email">E-mail *</label>
          <input type="email" id="email" name="email" placeholder="seuemail@email.com" required>
        </div>
        <div class="input-group">
          <label for="senha">Crie uma Senha *</label>
          <input type="password" id="senha" name="senha" placeholder="••••••••" required>
        </div>
      </div>

      <div class="input-row">
        <div class="input-group">
          <label for="celular">Celular / WhatsApp</label>
          <input type="text" id="celular" name="celular" placeholder="(00) 00000-0000">
        </div>
        <div class="input-group">
          <label for="data_nascimento">Data de Nascimento</label>
          <input type="date" id="data_nascimento" name="data_nascimento" placeholder="DD/MM/AAAA">
        </div>
      </div>

      <div class="input-group">
        <label for="endereco">Endereço Residencial</label>
        <input type="text" id="endereco" name="endereco" placeholder="Rua, número, bairro e cidade">
      </div>

      <div class="input-group">
        <label for="modalidade">Modalidade Preferida</label>
        <select id="modalidade" name="modalidade">
          <option value="Musculação">Musculação</option>
          <option value="Hipertrofia">Hipertrofia</option>
          <option value="Mobilidade">Mobilidade</option>
          <option value="Força">Força</option>
          <option value="Condicionamento">Condicionamento</option>
          <option value="Funcional">Funcional</option>
        </select>
      </div>

      <div class="input-group">
        <label for="objetivo">Objetivo Principal</label>
        <input type="text" id="objetivo" name="objetivo" placeholder="Ex: Ganho de massa, perda de peso">
      </div>

      <button type="submit" class="btn btn-primary btn-full" style="margin-top: 1rem;">
        Finalizar Cadastro
      </button>

      <div style="text-align: center; margin-top: 1rem;">
        <a href="index.php" style="color: var(--accent-lilac); text-decoration: none; font-size: 0.85rem;">Já tem conta? Voltar para o Login</a>
      </div>
    </form>
  </div>

  <script>
    if (window.lucide) lucide.createIcons();
    
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
      cpfInput.addEventListener('input', (e) => {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
      });
    }
  </script>
</body>
</html>