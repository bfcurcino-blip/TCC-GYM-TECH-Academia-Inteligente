<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'conexao.php';
$logado = isset($_SESSION['usuario_id']);
$perfil_usuario = $_SESSION['usuario_perfil'] ?? '';
$nome_usuario = $_SESSION['usuario_nome'] ?? '';
$erro_login = isset($_GET['erro']) && $_GET['erro'] == '1';

// Busca de alunos
$stmt_alunos = $pdo->query("SELECT * FROM alunos ORDER BY id DESC");
$alunos = $stmt_alunos->fetchAll();

// Busca de treinos incluindo o e-mail do aluno para envio
$stmt_treinos = $pdo->query("
    SELECT t.*, COALESCE(a.nome, 'Sem aluno') as alunoNome, a.cpf as alunoCpf, a.email as alunoEmail 
    FROM treinos t 
    LEFT JOIN alunos a ON t.aluno_id = a.id 
    ORDER BY t.id DESC");
$treinos = $stmt_treinos->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gym Tech - Portal da Academia</title>
  <link rel="stylesheet" href="style.css?v=7">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

  <!-- TELA DE LOGIN -->
  <div id="login-screen" class="login-overlay" style="display: <?php echo $logado ? 'none' : 'flex'; ?>;">
    <div class="login-card">
      <div class="login-header">
        <img src="img/logo.png" alt="Logo GYM TECH" class="brand-logo-lg"
     style="max-width: 180px; height: auto; display: block; margin: 0 auto 1rem auto;">
        <span>PORTAL DA ACADEMIA</span>
      </div>

      <?php if ($erro_login): ?>
        <div class="alert-error">
          <i data-lucide="alert-circle" style="width: 18px; height: 18px;"></i>
          <span>Usuário ou senha incorretos. Tente novamente.</span>
        </div>
      <?php endif; ?>

      <form id="login-form" action="login.php" method="POST" class="form-body">
        <div class="input-group">
          <label for="login-usuario">Usuário, E-mail ou CPF</label>
          <input type="text" id="login-usuario" name="usuario" placeholder="Digite seu usuário, e-mail ou CPF" required>
        </div>

        <div class="input-group">
    <label for="login-senha">Senha</label>

    <div class="senha-container">
        <input type="password"
               id="login-senha"
               name="senha"
               placeholder="••••••••"
               required>

        <button type="button"
                class="btn-ver-senha"
                onclick="toggleSenha()"
                aria-label="Mostrar ou ocultar senha">
            <i data-lucide="eye"></i>
        </button>

        </div>
      </div>
      
        <button type="submit" class="btn btn-primary btn-full" style="margin-top: 1rem;">
          Entrar no Portal
        </button>
      </form>
    </div>
  </div>

  <!-- SISTEMA INTERNO -->
  <div id="sistema-interno" style="display: <?php echo $logado ? 'block' : 'none'; ?>; width: 100%;">

    <header class="header">
      <div class="logo-area">
        <img src="img/logo_barra.png" alt="Logo GYM TECH" class="brand-logo" style="max-width: 150px; height: auto;">
      </div>

      <!-- NAV RECEPÇÃO -->
      <nav class="nav-menu nav-recepcao" style="display: none;">
        <a href="#inicio" class="nav-link active" onclick="navegarPara('inicio')">
          <i data-lucide="home"></i> Início
        </a>
        <a href="#alunos" class="nav-link" onclick="navegarPara('alunos')">
          <i data-lucide="users"></i> Alunos
        </a>
        <a href="#cadastro" class="nav-link" onclick="navegarPara('cadastro')">
          <i data-lucide="user-plus"></i> Cadastro
        </a>
        <a href="#treinos" class="nav-link" onclick="navegarPara('treinos')">
          <i data-lucide="dumbbell"></i> Treinos
        </a>
      </nav>

      <!-- NAV PROFESSOR -->
      <nav class="nav-menu nav-professor" style="display: none;">
        <a href="#prof-inicio" class="nav-link active" onclick="navegarParaProf('prof-inicio')">
          <i data-lucide="home"></i> Início
        </a>
        <a href="#prof-alunos" class="nav-link" onclick="navegarParaProf('prof-alunos')">
          <i data-lucide="users"></i> Alunos
        </a>
        <a href="#prof-treinos" class="nav-link" onclick="navegarParaProf('prof-treinos')">
          <i data-lucide="dumbbell"></i> Treinos
        </a>
        <a href="#prof-cadastrar-treino" class="nav-link" onclick="navegarParaProf('prof-cadastrar-treino')">
          <i data-lucide="plus-circle"></i> Cadastrar Treino
        </a>
      </nav>

      <div class="user-profile">
        <div class="user-info">
          <span class="user-name" id="role-display-title">Olá, <?php echo htmlspecialchars($nome_usuario); ?>!</span>
          <span class="user-role" id="role-subtitle"><?php echo ucfirst($perfil_usuario); ?></span>
        </div>
        <a href="logout.php" class="btn-logout" title="Sair do Sistema" style="text-decoration: none;">
          <i data-lucide="log-out"></i>
        </a>
      </div>
    </header>

    <main class="main-content">
      
      <!-- AMBIENTE RECEPÇÃO -->
      <div id="ambiente-recepcao" style="display: none;">
        <section id="inicio-section" class="hero-grid">
          <article class="hero-card primary-card">
            <span class="tag">GYM TECH EM MOVIMENTO</span>
            <h2>A academia conectada à evolução de cada aluno.</h2>
            <p>Cadastre alunos, organize prescrições e mantenha a rotina da GYM Tech sempre em movimento.</p>
            <button class="btn btn-primary" onclick="navegarPara('cadastro')">
              <i data-lucide="user-plus"></i> Cadastrar novo aluno
            </button>
          </article>

          <article class="hero-card metrics-card">
            <div class="card-header">
              <span class="tag">VISÃO GERAL</span>
              <i data-lucide="sparkles" class="icon-sparkle"></i>
            </div>
            <h3>Tudo pronto para crescer.</h3>
            <p>Acompanhe a comunidade, atualize cadastros e registre novos treinos em um só lugar.</p>
            
            <div class="metrics-grid">
              <div class="metric-item">
                <span class="metric-value metric-alunos">0</span>
                <span class="metric-label">alunos cadastrados</span>
              </div>
              <div class="metric-item">
                <span class="metric-value metric-treinos">0</span>
                <span class="metric-label">treinos cadastrados</span>
              </div>
            </div>
          </article>
        </section>

        <section id="home-community-section" class="community-section">
          <div class="section-header">
            <div>
              <span class="tag">COMUNIDADE GYM TECH</span>
              <h2>Alunos cadastrados</h2>
            </div>
            <button class="btn btn-secondary" onclick="navegarPara('alunos')">Ver todos os alunos</button>
          </div>
          <div class="alunos-grid" id="home-alunos-summary"></div>
        </section>

        <section id="alunos-section" class="alunos-container" style="display: none;">
          <div class="alunos-header">
            <div>
              <span class="tag">COMUNIDADE GYM TECH</span>
              <h2>Alunos cadastrados</h2>
              <p class="section-subtitle">Consulte os alunos cadastrados, acompanhe seus objetivos e mantenha cada plano atualizado.</p>
            </div>
            <div class="badge-count">
              <span class="badge-ativos">0 alunos ativos</span>
            </div>
          </div>

          <div class="search-bar-container">
            <div class="search-input-wrapper">
              <i data-lucide="search" class="search-icon"></i>
              <input type="text" id="search-cpf-recepcao" placeholder="Buscar aluno por CPF ou nome..." oninput="filtrarAlunosRecepcao()">
            </div>
          </div>

          <div class="alunos-grid" id="recepcao-alunos-grid"></div>

          <div style="width: 100%; margin-top: 1.5rem;">
    <button type="button"
            class="btn btn-primary btn-full"
            style="width: 100%; color: white;"
            onclick="navegarPara('inicio')">
        ← Voltar
    </button>
</div>
        </section>

        <section id="cadastro-section" class="form-container" style="display: none;">
          <div class="form-info">
            <span class="tag" id="form-tag">NOVA JORNADA</span>
            <h2 id="form-title">Cadastre quem vai evoluir com a gente.</h2>
            <p>Preencha os dados cadastrais obrigatórios e registre as métricas iniciais do aluno.</p>
          </div>

          <div class="form-card">
            <div class="form-card-header">
              <span class="tag">FICHA COMPLETA DO ALUNO</span>
              <h3 id="form-subtitle">Preencha os dados cadastrais</h3>
            </div>

            <form id="aluno-form" action="alunos.php?acao=salvar" method="POST" enctype="multipart/form-data" class="form-body">
              <input type="hidden" id="aluno-id" name="aluno_id">

              <div class="avatar-upload-container">
                <label>Foto de Avatar</label>
                <div class="avatar-preview-wrapper">
                  <img id="avatar-preview" src="img/avatar-padrao.jpg" alt="Pré-visualização do Avatar" class="avatar-preview-img">
                  <div class="avatar-input-group">
                    <input type="file" id="foto" name="foto" accept="image/*" onchange="previewImagem(event)">
                    <span class="section-subtitle" style="font-size: 0.75rem;">Opcional (Usa avatar padrão se omitido)</span>
                  </div>
                </div>
              </div>

              <div class="input-row">
                <div class="input-group">
                  <label for="nome">Nome completo *</label>
                  <input type="text" id="nome" name="nome" placeholder="Digite o nome do aluno" required>
                </div>
                <div class="input-group">
                  <label for="cpf">CPF *</label>
                  <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>
                </div>
              </div>

              <div class="input-row">
                <div class="input-group">
                  <label for="email">E-mail *</label>
                  <input type="email" id="email" name="email" placeholder="aluno@email.com" required>
                </div>
                <div class="input-group">
                  <label for="data_nascimento">Data de Nascimento *</label>
                  <input type="date" id="data_nascimento" name="data_nascimento" required>
                </div>
              </div>

              <div class="input-row">

<div class="input-row">
    <div class="input-group">
        <label for="sexo">Sexo *</label>
        <select id="sexo" name="sexo" required>
            <option value="">Selecione...</option>
            <option value="Feminino">Feminino</option>
            <option value="Masculino">Masculino</option>
        </select>
    </div>
</div>

                <div class="input-group">
                  <label for="celular">Celular / WhatsApp *</label>
                    <input type="tel" id="celular" name="celular" placeholder="(00) 00000-0000" maxlength="15" required>
                    
                </div>
                <div class="input-group">
                  <label for="endereco">Endereço Residencial</label>
                  <input type="text" id="endereco" name="endereco" placeholder="Rua, número, bairro e cidade">
                </div>
              </div>

              <div class="input-row">
                <div class="input-group">
                  <label for="plano">Plano *</label>
                  <select id="plano" name="plano">
                    <option value="Plano Essencial">Plano Essencial</option>
                    <option value="Plano Performance">Plano Performance</option>
                    <option value="Plano Elite">Plano Elite</option>
                    <option value="Plano Cardio+">Plano Cardio+</option>
                  </select>
                </div>

                <div class="input-group">
                  <label for="status">Status do plano</label>
                  <select id="status" name="status">
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                  </select>
                </div>
              </div>

              <div class="input-row">
                <div class="input-group">
                  <label for="peso_inicial">Peso Inicial (kg) *</label>
                  <input type="text"
                  id="peso_inicial"
                  name="peso_inicial"
                  inputmode="numeric"
                  maxlength="5"
                  placeholder="Ex: 75,5"
                  oninput="
           let v = this.value.replace(/\D/g, '').slice(0,4);
           if (v.length > 1) {
               v = v.slice(0, -1) + ',' + v.slice(-1);
           }
           this.value = v;
       ">

                </div>
                <div class="input-group">
                  <label for="altura">Altura (m) *</label>
                  <input type="text"
                  id="altura"
                  name="altura"
                  inputmode="numeric"
                  maxlength="4"
                  placeholder="Ex: 1,65"
                  oninput="
           let v = this.value.replace(/\D/g, '').slice(0,3);
           if (v.length > 1) {
               v = v.charAt(0) + ',' + v.slice(1);
           }
           this.value = v;
       ">
                </div>
              </div>

              <div class="input-group">
                <label for="modalidade">Modalidade de treino</label>
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
                <label for="objetivo">Objetivo principal *</label>
                <input type="text" id="objetivo" name="objetivo" placeholder="Ex: Perda de peso, ganho de massa">
              </div>

              <button type="submit" class="btn btn-primary btn-full" id="btn-salvar-aluno">
                Salvar aluno no portal
              </button>
              <button type="button" class="btn btn-secondary btn-full" id="btn-cancelar-edicao" style="display: none; margin-top: 0.5rem;" onclick="resetarFormulario()">
                Cancelar edição
              </button>

            </form>
          </div>
        </section>

 <div id="voltar-recepcao-cadastro"
     style="display: none; width: 100%; margin-top: 1.5rem;
     padding: 0 1.5rem; box-sizing: border-box;">

    <button type="button"
            class="btn btn-primary btn-full"
            style="width: 100%; color: white;"
            onclick="navegarPara('inicio')">
        ← Voltar
    </button>

</div>

        <section id="treinos-section" class="form-container" style="display: none;">
        
          <div class="form-card">
            <div class="form-card-header">
              <span class="tag">MONTAR PROGRAMA</span>
              <h3>Monte o treino</h3>
              <p class="section-subtitle">Pesquise o aluno pelo nome ou CPF e registre a prescrição.</p>
            </div>

            <form id="treino-form" action="treinos.php" method="POST" class="form-body">
              <div class="input-group">
                <label for="treino-aluno-select">Pesquisar e Selecionar Aluno (Nome ou CPF)</label>
                <select id="treino-aluno-select" name="aluno_id" class="select-aluno-treino" required style="width: 100%; padding: 0.75rem; border-radius: 8px;"></select>
              </div>

              <div class="input-group">
                <label for="treino-nome">Nome do treino</label>
                <input type="text" id="treino-nome" name="nome" placeholder="Ex: Treino A - Hipertrofia Superior" required>
              </div>

              <div class="input-group">
                <label for="treino-categoria">Categoria</label>
                <select id="treino-categoria" name="categoria">
                  <option value="Musculação">Musculação</option>
                  <option value="Cardio">Cardio</option>
                  <option value="Mobilidade">Mobilidade</option>
                  <option value="Funcional">Funcional</option>
                </select>
              </div>

              <div class="input-group">
                <label for="treino-observacoes">Séries e observações</label>
                <textarea id="treino-observacoes" name="observacoes" rows="4" placeholder="Ex: Supino reto 4x10..."></textarea>
              </div>

              <button type="submit" class="btn btn-primary btn-full">
                Salvar treino no portal
              </button>
            </form>
          </div>

          <div class="community-section">
            <div class="section-header">
              <div>
                <span class="tag">ACOMPANHAMENTO</span>
                <h2>Treinos dos alunos</h2>
              </div>
              <span class="badge-count"><span id="recepcao-badge-total">0</span> treinos</span>
            </div>

            <div class="hero-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 1.5rem;">
              <div class="hero-card">
                <span class="tag">TOTAL DE TREINOS</span>
                <h2 id="recepcao-metric-total">0</h2>
              </div>
              <div class="hero-card">
                <span class="tag">EM ANDAMENTO</span>
                <h2 id="recepcao-metric-andamento" style="color: var(--accent-lilac);">0</h2>
              </div>
              <div class="hero-card">
                <span class="tag">CONCLUÍDOS</span>
                <h2 id="recepcao-metric-concluidos" style="color: #4ade80;">0</h2>
              </div>
            </div>

            <div class="search-bar-container" style="margin-bottom: 1.5rem;">
              <div class="search-input-wrapper">
                <i data-lucide="search" class="search-icon"></i>
                <input type="text" id="search-recepcao-treinos" placeholder="Buscar treino por nome do aluno ou CPF..." oninput="filtrarTreinosRecepcao()">
              </div>
            </div>

            <div class="alunos-grid" id="recepcao-treinos-lista"></div>

</div>

<div style="grid-column: 1 / -1; width: 100%; margin-top: 1.5rem;">
    <button type="button"
            class="btn btn-primary btn-full"
            style="width: 100%; color: white;"
            onclick="navegarPara('inicio')">
        ← Voltar
    </button>
</div>

        </section>
      </div>

      <!-- AMBIENTE PROFESSOR -->
      <div id="ambiente-professor" style="display: none;">
        <section id="prof-inicio-section" class="alunos-container">
          <div class="alunos-header">
            <div>
              <span class="tag">PÁGINA DO PROFESSOR</span>
              <h2>Acompanhe o ritmo da turma.</h2>
              <p class="section-subtitle">Acompanhe métricas em tempo real e consulte as fichas ativas da academia.</p>
            </div>
          </div>

          <div class="hero-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="hero-card">
              <span class="tag">TOTAL DE TREINOS</span>
              <h2 id="prof-total-treinos">0</h2>
            </div>
            <div class="hero-card">
              <span class="tag">EM ANDAMENTO</span>
              <h2 id="prof-em-andamento" style="color: var(--accent-lilac);">0</h2>
            </div>
            <div class="hero-card">
              <span class="tag">CONCLUÍDOS</span>
              <h2 id="prof-concluidos" style="color: #4ade80;">0</h2>
            </div>
          </div>

          <div class="community-section" style="margin-top: 1rem;">
            <div class="section-header">
              <div>
                <span class="tag">ALUNOS ATIVOS</span>
                <h2>Visualização rápida da turma</h2>
              </div>
              <button class="btn btn-secondary" onclick="navegarParaProf('prof-alunos')">Ver todos os alunos</button>
            </div>
            <div class="alunos-grid" id="prof-home-alunos-summary"></div>
          </div>
        </section>

        <section id="prof-alunos-section" class="alunos-container" style="display: none;">
          <div class="alunos-header">
            <div>
              <span class="tag">PÁGINA DO PROFESSOR</span>
              <h2>Alunos Cadastrados</h2>
              <p class="section-subtitle">Consulte a lista de alunos ativos e suas respectivas modalidades.</p>
            </div>
          </div>

          <div class="search-bar-container">
            <div class="search-input-wrapper">
              <i data-lucide="search" class="search-icon"></i>
              <input type="text" id="search-cpf-prof" placeholder="Buscar aluno por CPF ou nome..." oninput="filtrarAlunosProf()">
            </div>
          </div>

          <div class="alunos-grid" id="prof-alunos-grid"></div>
          <div style="width: 100%; margin-top: 1.5rem;">
    <button type="button"
            class="btn btn-primary btn-full"
            style="width: 100%; color: white;"
            onclick="navegarParaProf('prof-inicio')">
        ← Voltar
    </button>
</div>
        </section>

        <section id="prof-treinos-section" class="alunos-container" style="display: none;">
          <div class="alunos-header">
            <div>
              <span class="tag">PÁGINA DO PROFESSOR</span>
              <h2>Treinos da Turma</h2>
              <p class="section-subtitle">Consulte e gerencie o estado dos treinos dos alunos na sala de musculação.</p>
            </div>
            <div class="badge-count">
              <span id="prof-badge-total-treinos">0 treinos</span>
            </div>
          </div>

          <div class="community-section">
            <div class="hero-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 1.5rem;">
              <div class="hero-card">
                <span class="tag">TOTAL DE TREINOS</span>
                <h2 id="prof-metric-total">0</h2>
              </div>
              <div class="hero-card">
                <span class="tag">EM ANDAMENTO</span>
                <h2 id="prof-metric-andamento" style="color: var(--accent-lilac);">0</h2>
              </div>
              <div class="hero-card">
                <span class="tag">CONCLUÍDOS</span>
                <h2 id="prof-metric-concluidos" style="color: #4ade80;">0</h2>
              </div>
            </div>

            <div class="search-bar-container" style="margin-bottom: 1.5rem;">
              <div class="search-input-wrapper">
                <i data-lucide="search" class="search-icon"></i>
                <input type="text" id="search-prof-treinos" placeholder="Buscar treino por nome do aluno ou CPF..." oninput="filtrarTreinosProf()">
              </div>
            </div>

            <h3>Fila de acompanhamento</h3>
            <div id="prof-treinos-grid" class="alunos-grid" style="grid-template-columns: 1fr;"></div>
          </div>

<button type="button"
        class="btn btn-primary btn-full"
        style="margin-top: 1.5rem; width: 100%; color: white;"
        onclick="navegarParaProf('prof-inicio')">
    ← Voltar
</button>

        </section>

        <section id="prof-cadastrar-treino-section" class="form-container" style="display: none;">
          <div class="form-card">
            <div class="form-card-header">
              <span class="tag">MONTAR PROGRAMA</span>
              <h3>Monte o treino</h3>
              <p class="section-subtitle">Pesquise o aluno pelo nome ou CPF e registre a prescrição.</p>
            </div>

            <form id="prof-cadastrar-treino-form" action="treinos.php" method="POST" class="form-body">
              <div class="input-group">
                <label for="prof-cadastrar-aluno-select">Pesquisar e Selecionar Aluno (Nome ou CPF)</label>
                <select id="prof-cadastrar-aluno-select" name="aluno_id" class="select-aluno-treino" required style="width: 100%; padding: 0.75rem; border-radius: 8px;"></select>
              </div>

              <div class="input-group">
                <label for="prof-cadastrar-nome">Nome do treino</label>
                <input type="text" id="prof-cadastrar-nome" name="nome" placeholder="Ex: Treino A - Hipertrofia Superior" required>
              </div>

              <div class="input-group">
                <label for="prof-cadastrar-categoria">Categoria</label>
                <select id="prof-cadastrar-categoria" name="categoria">
                  <option value="Musculação">Musculação</option>
                  <option value="Cardio">Cardio</option>
                  <option value="Mobilidade">Mobilidade</option>
                  <option value="Funcional">Funcional</option>
                </select>
              </div>

              <div class="input-group">
                <label for="prof-cadastrar-observacoes">Séries e observações</label>
                <textarea id="prof-cadastrar-observacoes" name="observacoes" rows="4" placeholder="Ex: Supino reto 4x10..."></textarea>
              </div>

              <button type="submit" class="btn btn-primary btn-full">
                Salvar treino no portal
              </button>
            </form>
          </div>

          <div class="community-section">
            <div class="section-header">
              <div>
                <span class="tag">ACOMPANHAMENTO</span>
                <h2>Treinos dos alunos</h2>
              </div>
              <span class="badge-count"><span class="total-treinos-badge">0 treinos</span></span>
            </div>

              <div id="prof-cadastrar-treinos-lista"
                 class="alunos-grid"
                 style="grid-template-columns: 1fr;">
            </div>

        </div>
 
         <div style="grid-column: 1 / -1; width: 100%; margin-top: 1.5rem;">

          <button type="button"
                  class="btn btn-primary btn-full"
                  style="width: 100%; color: white;"
                  onclick="navegarParaProf('prof-inicio')">

            ← Voltar

          </button>

        </div>
 </section>
    </main>

  <footer class="footer">
    <p>
        © 2026 GYM TECH - <em>Academia Inteligente</em> -
        <?php
        if ($perfil_usuario === 'professor') {
            echo 'Painel do Personal';
        } elseif ($perfil_usuario === 'recepcao') {
            echo 'Painel da Recepção';
        } else {
            echo 'Portal da Academia';
        }
        ?>
    </p>
</footer>

  </div>

  <!-- MODAL FICHA DO ALUNO -->
  <div id="aluno-modal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
      <button class="modal-close" onclick="fecharModalAluno()">&times;</button>
      
      <div class="modal-header">
        <img id="modal-avatar" src="" class="aluno-avatar-lg" alt="Foto do Aluno">
        <div>
          <span class="status-badge" id="modal-status"></span>
          <h2 id="modal-nome"></h2>
          <span class="aluno-plano" id="modal-plano"></span>
        </div>
      </div>

      <div class="modal-body">
        <div class="info-block">
          <strong>CPF:</strong>
          <span id="modal-cpf" class="section-subtitle" style="color: var(--text-main); font-weight: 500;"></span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; background: rgba(124, 58, 237, 0.15); padding: 0.75rem; border-radius: 8px; margin: 1rem 0; text-align: center; border: 1px solid var(--purple-primary);">
          <div>
            <span style="display: block; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Idade</span>
            <strong id="modal-idade-val" style="font-size: 0.95rem; color: var(--text-main);">--</strong>
          </div>
          <div>
            <span style="display: block; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Entrada</span>
            <strong id="modal-entrada-val" style="font-size: 0.95rem; color: var(--text-main);">--</strong>
          </div>
          <div>
            <span style="display: block; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">IMC</span>
            <strong id="modal-imc-val" style="font-size: 0.95rem; color: var(--text-main);">--</strong>
          </div>
        </div>

        <div class="info-block" style="margin-top: 0.5rem;">
          <strong>Modalidade:</strong>
          <span id="modal-modalidade" class="modalidade-tag" style="width: fit-content; margin-top: 0.3rem;"></span>
        </div>
        <div class="info-block" style="margin-top: 0.8rem;">
          <strong>Objetivo Principal:</strong>
          <p id="modal-objetivo" class="section-subtitle"></p>
        </div>

        <!-- ANÁLISE CORPORAL -->
<div class="analise-corporal" style="margin-top: 1.2rem;">
    <h3 style="font-size: 1.1rem; margin-bottom: 0.8rem;">
        Análise Corporal
    </h3>

    <div style="
        display: flex;
        align-items: center;
        gap: 1.2rem;
        padding: 1rem;
        background: rgba(124, 58, 237, 0.08);
        border: 1px solid var(--purple-primary);
        border-radius: 10px;
    ">

        <img id="holograma-corporal"
             src="img/holograma_masc.jpg"
             alt="Representação da análise corporal"
             style="
                width: 120px;
                height: 160px;
                object-fit: contain;
                border-radius: 8px;
             ">

        <div style="display: flex; flex-direction: column; gap: 0.8rem;">

            <div>
                <span style="color: var(--text-muted);">Altura</span><br>
                <strong id="modal-altura">--</strong>
            </div>

            <div>
                <span style="color: var(--text-muted);">Peso Atual</span><br>
                <strong id="modal-peso">--</strong>
            </div>

            <div>
                <span style="color: var(--text-muted);">Objetivo</span><br>
                <strong id="modal-objetivo-corporal">--</strong>
            </div>

        </div>
    </div>
</div>

        <hr class="divider">

        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">Treinos Prescritos</h3>
        <div id="modal-treinos-lista" style="display: flex; flex-direction: column; gap: 0.75rem;"></div>
      </div>
    </div>
  </div>

  <script>
    let alunos = <?php echo json_encode($alunos); ?>;
    let treinos = <?php echo json_encode($treinos); ?>;
    let perfilUsuarioLogado = '<?php echo $perfil_usuario; ?>';
  </script>
  <script src="script.js?v=7"></script>
</body>
</html>