<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header('Location: index.php');
    exit;
}

$aluno_id = $_SESSION['aluno_id'];
$mensagem_sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_perfil'])) {
    $nome = trim($_POST['nome']);
    $celular = trim($_POST['celular']);
    $endereco = trim($_POST['endereco']);
    $objetivo = trim($_POST['objetivo']);
    $senha = trim($_POST['senha']);

    if (!empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE alunos SET nome = :nome, celular = :celular, endereco = :endereco, objetivo = :objetivo, senha = :senha WHERE id = :id");
        $stmt->execute([':nome' => $nome, ':celular' => $celular, ':endereco' => $endereco, ':objetivo' => $objetivo, ':senha' => $senha_hash, ':id' => $aluno_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE alunos SET nome = :nome, celular = :celular, endereco = :endereco, objetivo = :objetivo WHERE id = :id");
        $stmt->execute([':nome' => $nome, ':celular' => $celular, ':endereco' => $endereco, ':objetivo' => $objetivo, ':id' => $aluno_id]);
    }
    $mensagem_sucesso = "Seus dados foram atualizados com sucesso!";
}

$stmt_aluno = $pdo->prepare("SELECT * FROM alunos WHERE id = :id LIMIT 1");
$stmt_aluno->execute([':id' => $aluno_id]);
$aluno = $stmt_aluno->fetch();

$stmt_treinos = $pdo->prepare("SELECT * FROM treinos WHERE aluno_id = :id ORDER BY id DESC");
$stmt_treinos->execute([':id' => $aluno_id]);
$treinos = $stmt_treinos->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gym Tech - Área do Aluno</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

  <div id="sistema-interno" style="width: 100%;">
    <header class="header">
      <div class="logo-area">
        <img src="img/logo_barra.png" alt="GYM Tech Logo" class="brand-logo" style="max-width: 150px; height: auto;">
      </div>

      <nav class="nav-menu">
        <a href="#perfil" class="nav-link active" id="link-perfil" onclick="mudarAbaAluno('perfil')">
          <i data-lucide="user"></i> Meu Perfil
        </a>
        <a href="#treinos" class="nav-link" id="link-treinos" onclick="mudarAbaAluno('treinos')">
          <i data-lucide="dumbbell"></i> Meus Treinos
        </a>
      </nav>

      <div class="user-profile">
        <div class="user-info">
          <span class="user-name">Olá, <?php echo htmlspecialchars($aluno['nome']); ?>!</span>
          <span class="user-role">Aluno <?php echo htmlspecialchars($aluno['plano']); ?></span>
        </div>
        <a href="logout.php" class="btn-logout" title="Sair do Portal" style="text-decoration: none;">
          <i data-lucide="log-out"></i>
        </a>
      </div>
    </header>

    <main class="main-content">

      <?php if ($mensagem_sucesso): ?>
        <div class="alert-error" style="background-color: rgba(74, 222, 128, 0.15); border-color: rgba(74, 222, 128, 0.4); color: #4ade80;">
          <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
          <span><?php echo htmlspecialchars($mensagem_sucesso); ?></span>
        </div>
      <?php endif; ?>

      <div id="secao-perfil" class="form-container">
        <div class="form-card" style="grid-column: 1 / -1; max-width: 800px; margin: 0 auto; width: 100%;">
          <div class="form-card-header">
            <span class="tag">MEU PERFIL</span>
            <h3>Meus Dados Cadastrais</h3>
            <p class="section-subtitle">Mantenha os seus dados de contato e objetivo sempre atualizados.</p>
          </div>

          <form action="portal_aluno.php" method="POST" class="form-body">
            <input type="hidden" name="atualizar_perfil" value="1">

            <div class="input-group">
              <label for="nome">Nome Completo</label>
              <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($aluno['nome']); ?>" required>
            </div>

            <div class="input-row">
              <div class="input-group">
                <label>CPF (Não editável)</label>
                <input type="text" value="<?php echo htmlspecialchars($aluno['cpf']); ?>" disabled style="opacity: 0.6;">
              </div>
              <div class="input-group">
                <label for="celular">Celular / WhatsApp</label>
                <input type="text" id="celular" name="celular" value="<?php echo htmlspecialchars($aluno['celular'] ?? ''); ?>">
              </div>
            </div>

            <div class="input-group">
              <label for="endereco">Endereço Residencial</label>
              <input type="text" id="endereco" name="endereco" value="<?php echo htmlspecialchars($aluno['endereco'] ?? ''); ?>">
            </div>

            <div class="input-group">
              <label for="objetivo">Objetivo Principal</label>
              <input type="text" id="objetivo" name="objetivo" value="<?php echo htmlspecialchars($aluno['objetivo'] ?? ''); ?>">
            </div>

            <div class="input-group">
              <label for="senha">Alterar Senha (deixe em branco se não quiser alterar)</label>
            </div>

            <div class="senha-wrapper">
    <input type="password"
           id="senha"
           name="senha"
           placeholder="••••••••">

    <button type="button"
            class="btn-olhinho"
            onclick="mostrarSenhaAluno()"
            aria-label="Mostrar ou ocultar senha">
        <i data-lucide="eye" id="icone-senha-aluno"></i>
    </button>
</div>

            <button type="submit" class="btn btn-primary btn-full">
              Salvar Minhas Alterações
            </button>
          </form>
        </div>
      </div>

      <div id="secao-treinos" class="community-section" style="display: none;">
        <div class="section-header">
          <div>
            <span class="tag">ROTINA DE TREINOS</span>
            <h2>Meus Treinos Prescritos</h2>
          </div>
          <span class="badge-count"><span><?php echo count($treinos); ?> treinos</span></span>
        </div>

        <?php if (count($treinos) > 0): ?>
          <div class="alunos-grid" style="grid-template-columns: 1fr;">
            <?php foreach ($treinos as $t): ?>
              <div class="treino-card" style="border-left: 4px solid <?php echo ($t['status'] ?? '') === 'Concluído' ? '#4ade80' : 'var(--accent-lilac)'; ?>;">
                <div class="treino-card-header">
                  <h4><?php echo htmlspecialchars($t['nome']); ?></h4>
                  <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <span class="status-badge <?php echo ($t['status'] ?? '') === 'Concluído' ? 'status-ativo' : 'status-inativo'; ?>">
                      <?php echo htmlspecialchars($t['status'] ?? 'Em Andamento'); ?>
                    </span>
                    <span class="modalidade-tag"><?php echo htmlspecialchars($t['categoria']); ?></span>
                  </div>
                </div>
                <div class="treino-obs"><?php echo nl2br(htmlspecialchars($t['observacoes'] ?? 'Sem observações.')); ?></div>
                
                <?php if (($t['status'] ?? '') !== 'Concluído'): ?>
                  <div style="display: flex; justify-content: flex-end; margin-top: 0.8rem;">
                    <a href="treinos.php?acao=concluir&id=<?php echo $t['id']; ?>" class="btn btn-secondary" style="padding: 0.3rem 0.8rem; font-size: 0.8rem; color: #4ade80; border-color: #4ade80;">
                      <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Marcar como Concluído
                    </a>
                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="section-subtitle">Você ainda não tem treinos prescritos pelos professores.</p>
        <?php endif; ?>

<div style="grid-column: 1 / -1; width: 100%; margin-top: 1.5rem;">
    <button type="button"
            class="btn btn-primary btn-full"
            style="width: 100%; color: white;"
            onclick="mudarAbaAluno('perfil')">
        ← Voltar
    </button>
</div>


      </div>

    </main>

    <footer class="footer">
    <p>© 2026 GYM TECH - <em>Academia Inteligente</em> - Área do Aluno</p>
</footer>
  </div>

  <script>
    function mudarAbaAluno(aba) {
        const secaoPerfil = document.getElementById('secao-perfil');
        const secaoTreinos = document.getElementById('secao-treinos');
        const linkPerfil = document.getElementById('link-perfil');
        const linkTreinos = document.getElementById('link-treinos');

        if (aba === 'perfil') {
            secaoPerfil.style.display = 'block';
            secaoTreinos.style.display = 'none';
            linkPerfil.classList.add('active');
            linkTreinos.classList.remove('active');
        } else {
            secaoPerfil.style.display = 'none';
            secaoTreinos.style.display = 'block';
            linkTreinos.classList.add('active');
            linkPerfil.classList.remove('active');
        }

        if (window.lucide) lucide.createIcons();
    }

    function mostrarSenhaAluno() {
        const campo = document.getElementById('senha');
        const icone = document.getElementById('icone-senha-aluno');

        if (campo.type === 'password') {
            campo.type = 'text';
            icone.setAttribute('data-lucide', 'eye-off');
        } else {
            campo.type = 'password';
            icone.setAttribute('data-lucide', 'eye');
        }

        lucide.createIcons();
    }

    if (window.lucide) lucide.createIcons();
</script>
</body>
</html>