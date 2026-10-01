<?php
session_start();
require_once 'conexao.php';

// Verificação de segurança dividida em duas partes para evitar erros
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?erro=acesso_negado');
    exit;
}

if ($_SESSION['usuario_perfil'] !== 'admin') {
    header('Location: index.php?erro=acesso_negado');
    exit;
}

$nome_usuario =$_SESSION['usuario_nome'] ?? 'Administrador';

// Consultas aos dados do sistema
$stmt_alunos =$pdo->query("SELECT * FROM alunos ORDER BY id DESC");
$alunos =$stmt_alunos->fetchAll();

$stmt_usuarios =$pdo->query("SELECT * FROM usuarios ORDER BY id DESC");
$usuarios =$stmt_usuarios->fetchAll();

$stmt_treinos =$pdo->query("
    SELECT t.*, COALESCE(a.nome, 'Sem aluno') as alunoNome, a.cpf as alunoCpf 
    FROM treinos t 
    LEFT JOIN alunos a ON t.aluno_id = a.id 
    ORDER BY t.id DESC");
$treinos =$stmt_treinos->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><title>GYM TECH - Academia Inteligente - Painel do Administrador</title></title>
  <link rel="stylesheet" href="style.css">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

  <div id="sistema-interno" style="width: 100%;">
    
    <!-- CABEÇALHO DO PAINEL -->
    <header class="header">
      <div class="logo-area">
        <img src="img/logo_barra.png" alt="Logo GYM TECH" class="brand-logo" style="max-width: 150px; height: auto;">
      </div>

      <nav class="nav-menu">
        <a href="#inicio" class="nav-link active" onclick="mudarAbaAdmin('inicio')">
          <i data-lucide="home"></i> Visão Geral
        </a>
        <a href="#alunos" class="nav-link" onclick="mudarAbaAdmin('alunos')">
          <i data-lucide="users"></i> Alunos
        </a>
        <a href="#equipe" class="nav-link" onclick="mudarAbaAdmin('equipe')">
          <i data-lucide="shield"></i> Gestão de Equipe
        </a>
        <a href="#treinos" class="nav-link" onclick="mudarAbaAdmin('treinos')">
          <i data-lucide="dumbbell"></i> Treinos
        </a>
      </nav>

      <div class="user-profile">
        <div class="user-info">
          <span class="user-name">Olá, <?php echo htmlspecialchars($nome_usuario); ?>!</span>
          <span class="user-role" style="color: #ef4444;">Administrador Master</span>
        </div>
        <a href="logout.php" class="btn-logout" title="Sair do Sistema" style="text-decoration: none;">
          <i data-lucide="log-out"></i>
        </a>
      </div>
    </header>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="main-content">
      
      <!-- ABA 1: VISÃO GERAL -->
      <section id="aba-inicio" class="hero-grid">
        <article class="hero-card primary-card">
          <span class="tag">GYM TECH • ACADEMIA INTELIGENTE</span>
          <h2>Controle total da infraestrutura GYM Tech.</h2>
          <p>Gerencie cadastros globais, atualize informações e monitore o fluxo completo da academia.</p>
        </article>

        <article class="hero-card metrics-card">
          <div class="card-header">
            <span class="tag">MÉTRICAS DO SISTEMA</span>
            <i data-lucide="shield-alert" class="icon-sparkle"></i>
          </div>
          <h3>Resumo Geral</h3>
          <div class="metrics-grid" style="grid-template-columns: repeat(3, 1fr); margin-top: 1rem;">
            <div class="metric-item">
              <span class="metric-value"><?php echo count($alunos); ?></span>
              <span class="metric-label">Alunos</span>
            </div>
            <div class="metric-item">
              <span class="metric-value"><?php echo count($usuarios); ?></span>
              <span class="metric-label">Equipe</span>
            </div>
            <div class="metric-item">
              <span class="metric-value"><?php echo count($treinos); ?></span>
              <span class="metric-label">Treinos</span>
            </div>
          </div>
        </article>
      </section>

      <!-- ABA 2: GESTÃO DE ALUNOS -->
      <section id="aba-alunos" class="alunos-container" style="display: none;">
        <div class="alunos-header">
          <div>
            <span class="tag">ADMINISTRAÇÃO</span>
            <h2>Gestão de Alunos</h2>
            <p class="section-subtitle">Visualize, atualize ou exclua qualquer aluno cadastrado no sistema.</p>
          </div>
          <div class="badge-count">
            <span><?php echo count($alunos); ?> alunos cadastrados</span>
          </div>
        </div>

        <div class="search-bar-container" style="margin-bottom: 1.5rem;">
          <div class="search-input-wrapper">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" id="search-admin-alunos" placeholder="Buscar aluno por CPF ou nome..." oninput="filtrarAlunosAdmin()">
          </div>
        </div>

        <div class="alunos-grid" id="grid-admin-alunos"></div>

        <div style="width: 100%; margin-top: 1.5rem;">
    <button type="button"
            class="btn btn-primary btn-full"
            style="width: 100%; color: white;"
            onclick="mudarAbaAdmin('inicio')">
        ← Voltar
    </button>
</div>

      </section>

      <!-- ABA 3: GESTÃO DE EQUIPE -->
      <section id="aba-equipe" class="alunos-container" style="display: none;">
        <div class="alunos-header">
          <div>
            <span class="tag">CONTROLE DE ACESSOS</span>
            <h2>Gestão de Funcionários</h2>
            <p class="section-subtitle">Adicione novos colaboradores, atualize dados ou remova acessos.</p>
          </div>
        </div>

        <div class="form-card" style="margin-bottom: 2rem;">
          <h3 id="form-titulo-usuario" style="margin-bottom: 1rem;">Cadastrar Novo Membro da Equipe</h3>
          <form action="usuarios.php?acao=salvar" method="POST" class="form-body">
            <input type="hidden" id="usuario-id" name="usuario_id">
            <div class="input-row">
              <div class="input-group">
                <label for="nome_func">Nome Completo</label>
                <input type="text" id="nome_func" name="nome" placeholder="Nome do funcionário" required>
              </div>
              <div class="input-group">
                <label for="email_func">E-mail / Usuário de Acesso</label>
                <input type="text" id="email_func" name="email" placeholder="usuario ou email@gymtech.com" required>
              </div>
            </div>

            <div class="input-row">
              <div class="input-group">
                <label for="senha_func">Senha de Acesso</label>
                <input type="password" id="senha_func" name="senha" placeholder="••••••••">
                <span class="section-subtitle" style="font-size: 0.75rem;">Deixe em branco para manter a senha atual (ao editar)</span>
              </div>
              <div class="input-group">
                <label for="perfil_func">Perfil / Cargo</label>
                <select id="perfil_func" name="perfil">
                  <option value="recepcao">Recepção</option>
                  <option value="professor">Professor</option>
                  <option value="admin">Administrador</option>
                </select>
              </div>
            </div>

            <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
              <button type="submit" class="btn btn-primary" style="flex: 1;">Salvar Funcionário</button>
              <button type="button" class="btn btn-secondary" id="btn-cancelar-usuario" style="display: none;" onclick="resetarFormularioUsuario()">Cancelar</button>
            </div>
          </form>
        </div>

        <div class="search-bar-container" style="margin-bottom: 1.5rem;">
          <div class="search-input-wrapper">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" id="search-admin-equipe" placeholder="Buscar funcionário por nome ou e-mail/usuário..." oninput="filtrarEquipeAdmin()">
          </div>
        </div>

        <div class="alunos-grid" id="grid-admin-equipe"></div>

        <div style="width: 100%; margin-top: 1.5rem;">
      <button type="button"
            class="btn btn-primary btn-full"
            style="width: 100%; color: white;"
            onclick="mudarAbaAdmin('inicio')">
        ← Voltar
    </button>
  </div>

      </section>

      <!-- ABA 4: GESTÃO DE TREINOS -->
      <section id="aba-treinos" class="form-container" style="display: none;">
        <div class="form-card">
          <div class="form-card-header">
            <span class="tag">MONTAR PROGRAMA</span>
            <h3>Cadastrar Novo Treino</h3>
            <p class="section-subtitle">Selecione o aluno e registre a prescrição.</p>
          </div>

          <form action="treinos.php" method="POST" class="form-body">
            <input type="hidden" name="redirect_aba" value="treinos">
            
            <div class="input-group">
              <label for="admin-treino-aluno">Selecione o Aluno</label>
              <select id="admin-treino-aluno" name="aluno_id" required class="select-aluno-treino" style="width: 100%; padding: 0.75rem; border-radius: 8px;"></select>
            </div>

            <div class="input-group">
              <label for="admin-treino-nome">Nome do Treino</label>
              <input type="text" id="admin-treino-nome" name="nome" placeholder="Ex: Treino A - Hipertrofia" required>
            </div>

            <div class="input-group">
              <label for="admin-treino-categoria">Categoria</label>
              <select id="admin-treino-categoria" name="categoria">
                <option value="Musculação">Musculação</option>
                <option value="Cardio">Cardio</option>
                <option value="Mobilidade">Mobilidade</option>
                <option value="Funcional">Funcional</option>
              </select>
            </div>

            <div class="input-group">
              <label for="admin-treino-obs">Séries e Observações</label>
              <textarea id="admin-treino-obs" name="observacoes" rows="4" placeholder="Ex: Supino reto 4x10..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Salvar Treino no Sistema</button>
          </form>
        </div>

        <div class="community-section" style="margin-top: 2rem;">
          <div class="section-header">
            <div>
              <span class="tag">MONITORAMENTO</span>
              <h2>Treinos Cadastrados</h2>
            </div>
            <span class="badge-count"><span><?php echo count($treinos); ?> treinos</span></span>
          </div>

          <div class="search-bar-container" style="margin-bottom: 1.5rem;">
            <div class="search-input-wrapper">
              <i data-lucide="search" class="search-icon"></i>
              <input type="text" id="search-admin-treinos" placeholder="Buscar treino por nome do aluno ou CPF..." oninput="filtrarTreinosAdmin()">
            </div>
          </div>

          <div class="alunos-grid" id="grid-admin-treinos"
     style="grid-template-columns: 1fr;"></div>

</div>

</section>

<div id="voltar-admin-treinos"
     style="display: none; width: 100%; margin-top: 1.5rem; padding: 0 1rem;">
    <button type="button"
            class="btn btn-primary btn-full"
            style="width: 100%; color: white;"
            onclick="mudarAbaAdmin('inicio')">
        ← Voltar
    </button>
</div>

    </main>

    <footer class="footer">
      <p></p><p>© 2026 GYM TECH - <em>Academia Inteligente</em> - Painel do Administrador</p>
    </footer>
  </div>

  <script>
    const alunosAdmin = <?php echo json_encode($alunos); ?>;
    const usuariosAdmin = <?php echo json_encode($usuarios); ?>;
    const treinosAdmin = <?php echo json_encode($treinos); ?>;
    const adminLogadoId = <?php echo $_SESSION['usuario_id']; ?>;

    function mudarAbaAdmin(aba) {
      const secoes = ['aba-inicio', 'aba-alunos', 'aba-equipe', 'aba-treinos'];
      secoes.forEach(s => {
        const el = document.getElementById(s);
        if (el) el.style.display = 'none';
      });

      document.querySelectorAll('.nav-menu .nav-link').forEach(link => link.classList.remove('active'));

      const alvo = document.getElementById(`aba-${aba}`);
      if (alvo) {
        alvo.style.display = (aba === 'inicio') ? 'grid' : 'flex';
      }

      const linkAtivo = document.querySelector(`.nav-menu .nav-link[href="#${aba}"]`);
      if (linkAtivo) linkAtivo.classList.add('active');

      if (aba === 'alunos') renderizarAlunosAdmin(alunosAdmin);
      if (aba === 'equipe') renderizarEquipeAdmin(usuariosAdmin);
      if (aba === 'treinos') {
        popularSelectAlunosAdmin();
        renderizarTreinosAdmin(treinosAdmin);
      }

      const botaoVoltarTreinos = document.getElementById('voltar-admin-treinos');

if (botaoVoltarTreinos) {
    botaoVoltarTreinos.style.display = (aba === 'treinos') ? 'block' : 'none';
}

      window.location.hash = aba;
      if (window.lucide) lucide.createIcons();
    }

    function filtrarAlunosAdmin() {
      const termo = document.getElementById('search-admin-alunos').value.toLowerCase();
      const filtrados = alunosAdmin.filter(a => 
        a.nome.toLowerCase().includes(termo) || (a.cpf && a.cpf.includes(termo))
      );
      renderizarAlunosAdmin(filtrados);
    }

    function renderizarAlunosAdmin(lista) {
      const grid = document.getElementById('grid-admin-alunos');
      if (!grid) return;

      if (lista.length === 0) {
        grid.innerHTML = `<p class="section-subtitle" style="grid-column: 1/-1;">Nenhum aluno encontrado.</p>`;
        return;
      }

      grid.innerHTML = lista.map(aluno => {
        const foto = (aluno.avatar && aluno.avatar !== 'null') ? aluno.avatar : 'img/avatar-padrao.jpg';
        return `
          <article class="aluno-card">
            <div class="aluno-header">
              <div class="aluno-profile">
                <img src="${foto}" alt="Avatar" class="aluno-avatar">
                <div class="aluno-info">
                  <h3>${aluno.nome}</h3>
                  <span class="aluno-plano">${aluno.plano}</span>
                  <span class="aluno-cpf">CPF: ${aluno.cpf || 'Não informado'}</span>
                  <span class="aluno-cpf">E-mail: ${aluno.email || 'Não informado'}</span>
                </div>
              </div>
              <span class="status-badge ${aluno.status === 'Ativo' ? 'status-ativo' : 'status-inativo'}">${aluno.status}</span>
            </div>
            <div class="aluno-footer">
              <span class="modalidade-tag">${aluno.modalidade}</span>
              <div class="card-actions">
                <button class="btn-icon" title="Atualizar Aluno" onclick="redirecionarEdicaoAluno(${aluno.id})">
                  <i data-lucide="edit-3"></i>
                </button>
                <a href="alunos.php?acao=excluir&id=${aluno.id}&redirect=admin" class="btn-icon delete" title="Excluir Aluno" onclick="return confirm('Deseja realmente excluir este aluno?')">
                  <i data-lucide="trash-2"></i>
                </a>
              </div>
            </div>
          </article>
        `;
      }).join('');
      if (window.lucide) lucide.createIcons();
    }

    function redirecionarEdicaoAluno(id) {
      window.location.href = `index.php?editar_aluno=${id}#cadastro`;
    }

    function filtrarEquipeAdmin() {
      const termo = document.getElementById('search-admin-equipe').value.toLowerCase();
      const filtrados = usuariosAdmin.filter(u => 
        u.nome.toLowerCase().includes(termo) || (u.email && u.email.toLowerCase().includes(termo)) || (u.usuario && u.usuario.toLowerCase().includes(termo))
      );
      renderizarEquipeAdmin(filtrados);
    }

    function renderizarEquipeAdmin(lista) {
      const grid = document.getElementById('grid-admin-equipe');
      if (!grid) return;

      if (lista.length === 0) {
        grid.innerHTML = `<p class="section-subtitle" style="grid-column: 1/-1;">Nenhum funcionário encontrado.</p>`;
        return;
      }

      grid.innerHTML = lista.map(u => `
        <article class="aluno-card">
          <div class="aluno-header">
            <div class="aluno-profile">
              <div class="aluno-info">
                <h3>${u.nome}</h3>
                <span class="aluno-plano">Acesso: ${u.email || u.usuario || 'N/I'}</span>
              </div>
            </div>
            <span class="status-badge" style="background-color: rgba(124, 58, 237, 0.2); color: var(--accent-lilac); border: 1px solid var(--purple-primary);">
              ${u.perfil.charAt(0).toUpperCase() + u.perfil.slice(1)}
            </span>
          </div>
          <div class="aluno-footer">
            <span class="modalidade-tag">ID: #${u.id}</span>
            <div class="card-actions">
              <button class="btn-icon" title="Atualizar Funcionário" onclick='carregarEdicaoUsuario(${JSON.stringify(u)})'>
                <i data-lucide="edit-3"></i>
              </button>
              ${u.id != adminLogadoId ? `
                <a href="usuarios.php?acao=excluir&id=${u.id}" class="btn-icon delete" title="Excluir Usuário" onclick="return confirm('Deseja realmente remover este funcionário?')">
                  <i data-lucide="trash-2"></i>
                </a>
              ` : ''}
            </div>
          </div>
        </article>
      `).join('');
      if (window.lucide) lucide.createIcons();
    }

    function carregarEdicaoUsuario(u) {
      document.getElementById('usuario-id').value = u.id;
      document.getElementById('nome_func').value = u.nome;
      document.getElementById('email_func').value = u.email || u.usuario;
      document.getElementById('senha_func').value = '';
      document.getElementById('perfil_func').value = u.perfil;
      document.getElementById('form-titulo-usuario').innerText = "Atualizar Membro da Equipe";
      document.getElementById('btn-cancelar-usuario').style.display = 'block';
    }

    function resetarFormularioUsuario() {
      document.getElementById('usuario-id').value = '';
      document.getElementById('nome_func').value = '';
      document.getElementById('email_func').value = '';
      document.getElementById('senha_func').value = '';
      document.getElementById('perfil_func').value = 'recepcao';
      document.getElementById('form-titulo-usuario').innerText = "Cadastrar Novo Membro da Equipe";
      document.getElementById('btn-cancelar-usuario').style.display = 'none';
    }

    function filtrarTreinosAdmin() {
      const termo = document.getElementById('search-admin-treinos').value.toLowerCase();
      const filtrados = treinosAdmin.filter(t => 
        (t.alunoNome && t.alunoNome.toLowerCase().includes(termo)) || 
        (t.alunoCpf && t.alunoCpf.includes(termo)) ||
        (t.nome && t.nome.toLowerCase().includes(termo))
      );
      renderizarTreinosAdmin(filtrados);
    }

    function renderizarTreinosAdmin(lista) {
      const grid = document.getElementById('grid-admin-treinos');
      if (!grid) return;

      if (lista.length === 0) {
        grid.innerHTML = `<p class="section-subtitle" style="grid-column: 1/-1;">Nenhum treino encontrado.</p>`;
        return;
      }

      grid.innerHTML = lista.map(t => `
        <div class="treino-card" style="border-left: 4px solid ${(t.status || '') === 'Concluído' ? '#4ade80' : 'var(--accent-lilac)'};">
          <div class="treino-card-header">
            <h4>${t.nome}</h4>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
              <span class="status-badge ${(t.status || '') === 'Concluído' ? 'status-ativo' : 'status-inativo'}">${t.status || 'Em Andamento'}</span>
              <span class="modalidade-tag">${t.categoria}</span>
            </div>
          </div>
          <p><strong>Aluno:</strong> ${t.alunoNome} (CPF: ${t.alunoCpf || 'N/I'})</p>
          <div class="treino-obs">${t.observacoes || 'Sem observações'}</div>
          <div style="display: flex; justify-content: flex-end; margin-top: 0.8rem;">
            <a href="treinos.php?acao=excluir&id=${t.id}&redirect=admin" class="btn btn-secondary" style="padding: 0.3rem 0.8rem; font-size: 0.8rem; color: #ef4444; border-color: #ef4444;" onclick="return confirm('Deseja excluir este treino?')">
              <i data-lucide="trash-2"></i> Excluir Treino
            </a>
          </div>
        </div>
      `).join('');
      if (window.lucide) lucide.createIcons();
    }

    function popularSelectAlunosAdmin() {
      const select = document.getElementById('admin-treino-aluno');
      if (!select) return;
      select.innerHTML = '<option value="">Selecione o aluno...</option>' + 
        alunosAdmin.map(a => `<option value="${a.id}">${a.nome} — CPF: ${a.cpf || 'Não informado'}</option>`).join('');
    }

    window.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) lucide.createIcons();
      const hash = window.location.hash.replace('#', '');
      if (['alunos', 'equipe', 'treinos'].includes(hash)) {
        mudarAbaAdmin(hash);
      } else {
        mudarAbaAdmin('inicio');
      }
    });
  </script>
</body>
</html>