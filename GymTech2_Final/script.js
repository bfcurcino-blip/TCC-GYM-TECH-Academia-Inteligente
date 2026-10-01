let perfilAtual = 'recepcao';

function previewImagem(event) {
  const leitor = new FileReader();
  leitor.onload = function() {
    const preview = document.getElementById('avatar-preview');
    if (preview) {
      preview.src = leitor.result;
    }
  };
  if (event.target.files[0]) {
    leitor.readAsDataURL(event.target.files[0]);
  }
}

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

// Máscara automática para Celular / WhatsApp
document.addEventListener('input', function (e) {
  if (e.target && e.target.id === 'celular') {
    let numero = e.target.value.replace(/\D/g, '');

    numero = numero.substring(0, 11);

    if (numero.length > 10) {
      numero = numero.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
    } else if (numero.length > 6) {
      numero = numero.replace(/^(\d{2})(\d{4})(\d+)$/, '($1) $2-$3');
    } else if (numero.length > 2) {
      numero = numero.replace(/^(\d{2})(\d+)/, '($1) $2');
    } else if (numero.length > 0) {
      numero = numero.replace(/^(\d{2})/, '($1) ');
    }

    e.target.value = numero;
  }
});

function alternarPerfil(novoPerfil) {
  perfilAtual = novoPerfil;
  const envRecepcao = document.getElementById('ambiente-recepcao');
  const envProfessor = document.getElementById('ambiente-professor');
  const navRecepcao = document.querySelector('.nav-recepcao');
  const navProfessor = document.querySelector('.nav-professor');
  const roleTitle = document.getElementById('role-display-title');
  const roleSubtitle = document.getElementById('role-subtitle');

  if (novoPerfil === 'professor') {
    if (envRecepcao) envRecepcao.style.display = 'none';
    if (envProfessor) envProfessor.style.display = 'block';
    if (navRecepcao) navRecepcao.style.display = 'none';
    if (navProfessor) navProfessor.style.display = 'flex';
    if (roleTitle) roleTitle.innerText = "Equipe Gym Tech";
    if (roleSubtitle) roleSubtitle.innerText = "Professor Responsável";
    
    navegarParaProf('prof-inicio');
  } else {
    if (envRecepcao) envRecepcao.style.display = 'block';
    if (envProfessor) envProfessor.style.display = 'none';
    if (navRecepcao) navRecepcao.style.display = 'flex';
    if (navProfessor) navProfessor.style.display = 'none';
    if (roleSubtitle) roleSubtitle.innerText = "Gestão da Academia";
    
    navegarPara('inicio');
  }
}

function navegarPara(destino) {
  const secoes = ['inicio-section', 'home-community-section', 'alunos-section', 'cadastro-section', 'treinos-section'];
  secoes.forEach(s => {
    const el = document.getElementById(s);
    if (el) el.style.display = 'none';
  });

  document.querySelectorAll('.nav-recepcao .nav-link').forEach(link => link.classList.remove('active'));

  if (destino === 'inicio') {
    const s1 = document.getElementById('inicio-section');
    const s2 = document.getElementById('home-community-section');
    if (s1) s1.style.display = 'grid';
    if (s2) s2.style.display = 'block';
  } else {
    const destinoEl = document.getElementById(`${destino}-section`);
    if (destinoEl) {
      destinoEl.style.display = (destino === 'cadastro' || destino === 'treinos') ? 'grid' : 'flex';
    }
  }

  const activeLink = document.querySelector(`.nav-recepcao .nav-link[href="#${destino}"]`);
  if (activeLink) activeLink.classList.add('active');

  const voltarCadastro = document.getElementById('voltar-recepcao-cadastro');

if (voltarCadastro) {
    voltarCadastro.style.display =
        destino === 'cadastro' ? 'block' : 'none';
}

  atualizarInterface();

}

function navegarParaProf(destino) {
  const secoes = ['prof-inicio-section', 'prof-alunos-section', 'prof-treinos-section', 'prof-cadastrar-treino-section'];
  secoes.forEach(s => {
    const el = document.getElementById(s);
    if (el) el.style.display = 'none';
  });

  document.querySelectorAll('.nav-professor .nav-link').forEach(link => link.classList.remove('active'));

  const destinoEl = document.getElementById(`${destino}-section`);
  if (destinoEl) {
    destinoEl.style.display = (destino === 'prof-cadastrar-treino') ? 'grid' : 'flex';
  }

  const activeLink = document.querySelector(`.nav-professor .nav-link[href="#${destino}"]`);
  if (activeLink) activeLink.classList.add('active');

  atualizarInterface();
}

function filtrarAlunosRecepcao() {
  const inputSearch = document.getElementById('search-cpf-recepcao');
  const termo = inputSearch ? inputSearch.value.toLowerCase() : '';
  const alunosFiltrados = alunos.filter(a => 
    a.nome.toLowerCase().includes(termo) || (a.cpf && a.cpf.includes(termo))
  );
  renderizarGridAlunos('recepcao-alunos-grid', alunosFiltrados, true);
}

function filtrarAlunosProf() {
  const inputSearch = document.getElementById('search-cpf-prof');
  const termo = inputSearch ? inputSearch.value.toLowerCase() : '';
  const alunosFiltrados = alunos.filter(a => 
    a.nome.toLowerCase().includes(termo) || (a.cpf && a.cpf.includes(termo))
  );
  renderizarGridAlunos('prof-alunos-grid', alunosFiltrados, false);
}

function renderizarGridAlunos(gridId, lista, comAcoes) {
  const grid = document.getElementById(gridId);
  if (!grid) return;

  if (lista.length === 0) {
    grid.innerHTML = `<p class="section-subtitle" style="grid-column: 1/-1;">Nenhum aluno encontrado.</p>`;
    return;
  }

  grid.innerHTML = lista.map(aluno => {
    const fotoPath = (aluno.avatar && aluno.avatar.trim() !== '' && aluno.avatar !== 'null') ? aluno.avatar : 'img/avatar-padrao.jpg';
    return `
      <article class="aluno-card">
        <div class="aluno-header">
          <div class="aluno-profile" onclick="abrirFichaAluno(${aluno.id})">
            <img src="${fotoPath}" alt="${aluno.nome}" class="aluno-avatar" onerror="this.onerror=null; this.src='img/avatar-padrao.jpg';">
            <div class="aluno-info">
              <h3>${aluno.nome}</h3>
              <span class="aluno-plano">${aluno.plano}</span>
              <span class="aluno-cpf">CPF: ${aluno.cpf || 'Não informado'}</span>
            </div>
          </div>
          <span class="status-badge ${aluno.status === 'Ativo' ? 'status-ativo' : 'status-inativo'}">${aluno.status}</span>
        </div>
        <div class="aluno-footer">
          <span class="modalidade-tag">${aluno.modalidade}</span>
          ${comAcoes ? `
            <div class="card-actions">
              <button class="btn-icon" title="Editar" onclick="carregarEdicaoAluno(${aluno.id})">
                <i data-lucide="edit-3"></i>
              </button>
              <a href="alunos.php?acao=excluir&id=${aluno.id}" class="btn-icon delete" title="Excluir" onclick="return confirm('Deseja realmente remover este aluno?')">
                <i data-lucide="trash-2"></i>
              </a>
            </div>
          ` : ''}
        </div>
      </article>
    `;
  }).join('');

  if (window.lucide) lucide.createIcons();
}

function popularSelectsAlunos() {
  document.querySelectorAll('.select-aluno-treino').forEach(select => {
    // Exibe apenas Nome e CPF (sem e-mail)
    select.innerHTML = '<option value="">Pesquise e selecione o aluno por nome ou CPF...</option>' + 
      alunos.map(a => `<option value="${a.id}">${a.nome} — CPF: ${a.cpf || 'Não informado'}</option>`).join('');
  });
}

function atualizarInterface() {
  document.querySelectorAll('.metric-alunos').forEach(el => el.innerText = alunos.length);
  document.querySelectorAll('.metric-treinos').forEach(el => el.innerText = treinos.length);

  const total = treinos.length;
  const concluidosCount = treinos.filter(t => t.status === 'Concluído').length;
  const emAndamentoCount = total - concluidosCount;

  const profTotal = document.getElementById('prof-total-treinos');
  const profAndamento = document.getElementById('prof-em-andamento');
  const profConcluidos = document.getElementById('prof-concluidos');

  if (profTotal) profTotal.innerText = total;
  if (profAndamento) profAndamento.innerText = emAndamentoCount;
  if (profConcluidos) profConcluidos.innerText = concluidosCount;

  const profMetricTotal = document.getElementById('prof-metric-total');
  const profMetricAndamento = document.getElementById('prof-metric-andamento');
  const profMetricConcluidos = document.getElementById('prof-metric-concluidos');
  if (profMetricTotal) profMetricTotal.innerText = total;
  if (profMetricAndamento) profMetricAndamento.innerText = emAndamentoCount;
  if (profMetricConcluidos) profMetricConcluidos.innerText = concluidosCount;

  const recMetricTotal = document.getElementById('recepcao-metric-total');
  const recMetricAndamento = document.getElementById('recepcao-metric-andamento');
  const recMetricConcluidos = document.getElementById('recepcao-metric-concluidos');
  if (recMetricTotal) recMetricTotal.innerText = total;
  if (recMetricAndamento) recMetricAndamento.innerText = emAndamentoCount;
  if (recMetricConcluidos) recMetricConcluidos.innerText = concluidosCount;

  const recBadgeTotal = document.getElementById('recepcao-badge-total');
  if (recBadgeTotal) recBadgeTotal.innerText = total;

  const profBadgeTotal = document.getElementById('prof-badge-total-treinos');
  if (profBadgeTotal) profBadgeTotal.innerText = `${total} treinos`;

  document.querySelectorAll('.total-treinos-badge').forEach(el => el.innerText = `${total} treinos`);

  const ativosCount = alunos.filter(a => a.status === 'Ativo').length;
  document.querySelectorAll('.badge-ativos').forEach(el => el.innerText = `${ativosCount} alunos ativos`);

  const homeGrid = document.getElementById('home-alunos-summary');
  if (homeGrid) {
    homeGrid.innerHTML = alunos.slice(0, 4).map(aluno => {
      const fotoPath = (aluno.avatar && aluno.avatar.trim() !== '' && aluno.avatar !== 'null') ? aluno.avatar : 'img/avatar-padrao.jpg';
      return `
        <article class="aluno-card">
          <div class="aluno-header">
            <div class="aluno-profile" onclick="abrirFichaAluno(${aluno.id})">
              <img src="${fotoPath}" alt="${aluno.nome}" class="aluno-avatar" onerror="this.onerror=null; this.src='img/avatar-padrao.jpg';">
              <div class="aluno-info">
                <h3>${aluno.nome}</h3>
                <span class="aluno-plano">${aluno.plano}</span>
              </div>
            </div>
            <span class="status-badge ${aluno.status === 'Ativo' ? 'status-ativo' : 'status-inativo'}">${aluno.status}</span>
          </div>
          <div class="aluno-footer">
            <span class="modalidade-tag">${aluno.modalidade}</span>
          </div>
        </article>
      `;
    }).join('');
  }

  const profHomeGrid = document.getElementById('prof-home-alunos-summary');
  if (profHomeGrid) {
    profHomeGrid.innerHTML = alunos.slice(0, 4).map(aluno => {
      const fotoPath = (aluno.avatar && aluno.avatar.trim() !== '' && aluno.avatar !== 'null') ? aluno.avatar : 'img/avatar-padrao.jpg';
      return `
        <article class="aluno-card">
          <div class="aluno-header">
            <div class="aluno-profile" onclick="abrirFichaAluno(${aluno.id})">
              <img src="${fotoPath}" alt="${aluno.nome}" class="aluno-avatar" onerror="this.onerror=null; this.src='img/avatar-padrao.jpg';">
              <div class="aluno-info">
                <h3>${aluno.nome}</h3>
                <span class="aluno-plano">${aluno.plano}</span>
              </div>
            </div>
            <span class="status-badge ${aluno.status === 'Ativo' ? 'status-ativo' : 'status-inativo'}">${aluno.status}</span>
          </div>
          <div class="aluno-footer">
            <span class="modalidade-tag">${aluno.modalidade}</span>
          </div>
        </article>
      `;
    }).join('');
  }

  filtrarAlunosRecepcao();
  filtrarAlunosProf();
  filtrarTreinosRecepcao();
  filtrarTreinosProf();
  popularSelectsAlunos();
}

function filtrarTreinosRecepcao() {
  const termo = document.getElementById('search-recepcao-treinos') ? document.getElementById('search-recepcao-treinos').value.toLowerCase() : '';
  const filtrados = treinos.filter(t => 
    (t.alunoNome && t.alunoNome.toLowerCase().includes(termo)) || 
    (t.alunoCpf && t.alunoCpf.includes(termo)) ||
    (t.nome && t.nome.toLowerCase().includes(termo))
  );
  renderizarListaTreinos('recepcao-treinos-lista', filtrados, true);
}

function filtrarTreinosProf() {
  const termo = document.getElementById('search-prof-treinos') ? document.getElementById('search-prof-treinos').value.toLowerCase() : '';
  const filtrados = treinos.filter(t => 
    (t.alunoNome && t.alunoNome.toLowerCase().includes(termo)) || 
    (t.alunoCpf && t.alunoCpf.includes(termo)) ||
    (t.nome && t.nome.toLowerCase().includes(termo))
  );
  renderizarListaTreinos('prof-treinos-grid', filtrados, true);
  renderizarListaTreinos('prof-cadastrar-treinos-lista', filtrados, false);
}

function renderizarListaTreinos(elementId, lista, comAcoes) {
  const container = document.getElementById(elementId);
  if (!container) return;

  if (lista.length === 0) {
    container.innerHTML = `<p class="section-subtitle">Nenhum treino encontrado.</p>`;
    return;
  }

  container.innerHTML = lista.map(t => `
    <div class="treino-card" style="border-left: 4px solid ${(t.status || '') === 'Concluído' ? '#4ade80' : 'var(--accent-lilac)'};">
      <div class="treino-card-header">
        <h4>${t.nome}</h4>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
          <span class="status-badge ${(t.status || '') === 'Concluído' ? 'status-ativo' : 'status-inativo'}">${t.status || 'Em Andamento'}</span>
          <span class="modalidade-tag">${t.categoria}</span>
        </div>
      </div>
      <p><strong>Aluno:</strong> ${t.alunoNome || 'Não especificado'} (CPF: ${t.alunoCpf || 'N/I'})</p>
      <div class="treino-obs">${t.observacoes || 'Sem observações'}</div>
      
      <div style="display: flex; gap: 0.5rem; margin-top: 0.8rem; justify-content: space-between; align-items: center;">
        <a href="mailto:${t.alunoEmail || ''}?subject=Seu%20Treino%20-%20GYM%20Tech&body=Olá%20${encodeURIComponent(t.alunoNome)},%0A%0ASeu%20treino%20'${encodeURIComponent(t.nome)}'%20foi%20cadastrado/atualizado.%0A%0ADetalhes:%0A${encodeURIComponent(t.observacoes)}%0A%0AAtenciosamente,%0AEquipe%20GYM%20Tech" class="btn btn-secondary" style="padding: 0.3rem 0.8rem; font-size: 0.8rem; color: var(--accent-lilac); border-color: var(--accent-lilac);" title="Enviar Treino por E-mail">
          <i data-lucide="mail"></i> Enviar por E-mail
        </a>

        ${comAcoes ? `
          <div style="display: flex; gap: 0.5rem;">
            ${(t.status || '') !== 'Concluído' ? `
              <a href="treinos.php?acao=concluir&id=${t.id}" class="btn btn-secondary" style="padding: 0.3rem 0.8rem; font-size: 0.8rem; color: #4ade80; border-color: #4ade80;" title="Marcar como Concluído">
                <i data-lucide="check-circle"></i> Concluir
              </a>
            ` : ''}
            <a href="treinos.php?acao=excluir&id=${t.id}" class="btn btn-secondary" style="padding: 0.3rem 0.8rem; font-size: 0.8rem; color: #ef4444; border-color: #ef4444;" onclick="return confirm('Deseja excluir este treino?')" title="Excluir Treino">
              <i data-lucide="trash-2"></i> Excluir Treino
            </a>
          </div>
        ` : ''}
      </div>
    </div>
  `).join('');

  if (window.lucide) lucide.createIcons();
}

function carregarEdicaoAluno(id) {
  const aluno = alunos.find(a => a.id == id);
  if (!aluno) return;
  console.log(aluno);

  document.getElementById('aluno-id').value = aluno.id;
  document.getElementById('nome').value = aluno.nome;
  document.getElementById('cpf').value = aluno.cpf || '';
  if (document.getElementById('email')) document.getElementById('email').value = aluno.email || '';
  if (document.getElementById('endereco')) document.getElementById('endereco').value = aluno.endereco || '';
  if (document.getElementById('data_nascimento')) document.getElementById('data_nascimento').value = aluno.data_nascimento || '';
  if (document.getElementById('celular')) document.getElementById('celular').value = aluno.celular || '';
  document.getElementById('plano').value = aluno.plano;
  document.getElementById('status').value = aluno.status;
  if (document.getElementById('peso_inicial')) document.getElementById('peso_inicial').value = aluno.peso_inicial || '';
  if (document.getElementById('altura')) document.getElementById('altura').value = aluno.altura || '';
  document.getElementById('modalidade').value = aluno.modalidade;
  document.getElementById('objetivo').value = aluno.objetivo || '';

  if (document.getElementById('avatar-preview')) {
    document.getElementById('avatar-preview').src = (aluno.avatar && aluno.avatar.trim() !== '' && aluno.avatar !== 'null') ? aluno.avatar : 'img/avatar-padrao.jpg';
  }

  const form = document.getElementById('aluno-form');
  if (form) form.action = 'alunos.php?acao=salvar';

  document.getElementById('form-tag').innerText = "ATUALIZAÇÃO";
  document.getElementById('form-title').innerText = "Atualizar ficha do aluno";
  document.getElementById('btn-salvar-aluno').innerText = "Salvar alterações";
  document.getElementById('btn-cancelar-edicao').style.display = 'block';

navegarPara('cadastro');

// Carrega o sexo salvo depois de abrir o formulário
const campoSexo = document.getElementById('sexo');

if (campoSexo && aluno.sexo) {
    campoSexo.value = aluno.sexo.trim();
}

}

function resetarFormulario() {
  const form = document.getElementById('aluno-form');
  if (form) form.reset();
  document.getElementById('aluno-id').value = '';
  if (document.getElementById('avatar-preview')) {
    document.getElementById('avatar-preview').src = 'img/avatar-padrao.jpg';
  }
  document.getElementById('form-tag').innerText = "NOVA JORNADA";
  document.getElementById('form-title').innerText = "Cadastre quem vai evoluir com a gente.";
  document.getElementById('btn-salvar-aluno').innerText = "Salvar aluno no portal";
  document.getElementById('btn-cancelar-edicao').style.display = 'none';
}

function abrirFichaAluno(id) {
  const aluno = alunos.find(a => a.id == id);
  if (!aluno) return;

const holograma = document.getElementById('holograma-corporal');

if (holograma) {
    if ((aluno.sexo || '').trim().toLowerCase() === 'feminino') {
        holograma.src = 'img/holograma_fem.png';
    } else {
        holograma.src = 'img/holograma_masc.jpg';
    }
}

  const fotoPath = (aluno.avatar && aluno.avatar.trim() !== '' && aluno.avatar !== 'null') ? aluno.avatar : 'img/avatar-padrao.jpg';
  document.getElementById('modal-avatar').src = fotoPath;
  document.getElementById('modal-nome').innerText = aluno.nome;
  document.getElementById('modal-cpf').innerText = aluno.cpf || 'Não informado';
  document.getElementById('modal-plano').innerText = aluno.plano;
  document.getElementById('modal-status').innerText = aluno.status;
  document.getElementById('modal-status').className = `status-badge ${aluno.status === 'Ativo' ? 'status-ativo' : 'status-inativo'}`;
  document.getElementById('modal-modalidade').innerText = aluno.modalidade;
  document.getElementById('modal-objetivo').innerText = aluno.objetivo || 'Nenhum objetivo cadastrado.';

  document.getElementById('modal-altura').innerText =
    aluno.altura ? aluno.altura + ' m' : 'Não informado';

  document.getElementById('modal-peso').innerText =
    aluno.peso_inicial ? aluno.peso_inicial + ' kg' : 'Não informado';

  document.getElementById('modal-objetivo-corporal').innerText =
    aluno.objetivo || 'Não informado'; 

  const treinosAluno = treinos.filter(t => t.aluno_id == id);

  if (aluno.altura && aluno.peso_inicial) {
    const imc = aluno.peso_inicial / (aluno.altura * aluno.altura);
    document.getElementById('modal-imc-val').innerText = imc.toFixed(1);
  } else {
    document.getElementById('modal-imc-val').innerText = 'Não informado';
  }

  if (aluno.data_nascimento) {
    const nascimento = new Date(aluno.data_nascimento + 'T00:00:00');
    const hoje = new Date();

    let idade = hoje.getFullYear() - nascimento.getFullYear();
    const mes = hoje.getMonth() - nascimento.getMonth();

    if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
        idade--;
    }

    document.getElementById('modal-idade-val').innerText = idade + ' anos';
} else {
    document.getElementById('modal-idade-val').innerText = 'Não informado';
}

  const modalLista = document.getElementById('modal-treinos-lista');

  // Exibe a data de entrada/cadastro do aluno
if (aluno.data_cadastro) {
    const dataEntrada = aluno.data_cadastro.split(' ')[0];
    const partes = dataEntrada.split('-');

    document.getElementById('modal-entrada-val').innerText =
        partes[2] + '/' + partes[1] + '/' + partes[0];
} else {
    document.getElementById('modal-entrada-val').innerText = 'Não informado';
}

  if (treinosAluno.length > 0) {
    modalLista.innerHTML = treinosAluno.map(t => `
      <div class="treino-card">
        <div class="treino-card-header">
          <strong style="color: var(--accent-lilac);">${t.nome}</strong>
          <span class="modalidade-tag">${t.categoria}</span>
        </div>
        <div class="treino-obs">${t.observacoes || 'Sem observações'}</div>
      </div>
    `).join('');
  } else {
    modalLista.innerHTML = `<p class="section-subtitle">Nenhum treino cadastrado para este aluno.</p>`;
  }

  document.getElementById('aluno-modal').style.display = 'flex';
  if (window.lucide) lucide.createIcons();
}

function fecharModalAluno() {
  document.getElementById('aluno-modal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) lucide.createIcons();
  if (typeof perfilUsuarioLogado !== 'undefined' && perfilUsuarioLogado) {
    alternarPerfil(perfilUsuarioLogado);
  } else {
    alternarPerfil('recepcao');
  }

  // Verifica se há pedido de edição via URL vindos do admin
  
  const urlParams = new URLSearchParams(window.location.search);
  const editarId = urlParams.get('editar_aluno');
  if (editarId) {
    carregarEdicaoAluno(editarId);
  }
});

function toggleSenha() {
    const senha = document.getElementById('login-senha');

    if (senha.type === 'password') {
        senha.type = 'text';
    } else {
        senha.type = 'password';
    }
}