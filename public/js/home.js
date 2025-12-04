/* =========================================
   ARQUIVO: public/js/home.js
   CONTROLA: Vitrine, Tema, Animações e Fundo
   ========================================= */

document.addEventListener('DOMContentLoaded', () => {
    console.log("Script home.js iniciado com sucesso!"); 
    
    // 1. Inicia as funções principais
    carregarPets();
    configurarTema();
    criarFundoAnimado();

    // 2. Ativa o rastro de patinhas no mouse
    document.addEventListener('mousemove', function(e) {
        criarPatinha(e);
    });
});

/* =========================================
   1. BUSCAR E RENDERIZAR PETS (API)
   ========================================= */
async function carregarPets() {
    const container = document.getElementById('pets-container');
    
    if (!container) {
        console.error("ERRO: Div 'pets-container' não encontrada.");
        return;
    }

    container.innerHTML = '<p style="text-align:center;">Buscando novos amigos...</p>';

    try {
        const response = await fetch('api_pets.php');
        if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);

        const pets = await response.json();
        container.innerHTML = '';

        if (!pets || pets.length === 0) {
            container.innerHTML = '<p style="text-align:center">Nenhum pet disponível.</p>';
            return;
        }

        pets.forEach(pet => {
            // --- Variáveis para não dar erro se algo for nulo ---
            let imgUrl = pet.imagem_final || pet.foto || 'https://placehold.co/400x300?text=Sem+Foto';
            const nome = pet.nome || 'Pet sem nome';
            const raca = pet.raca || 'SRD';
            const especie = pet.especie || 'Pet';
            const badgeClass = especie.toLowerCase().includes('gato') ? 'cat' : 'dog';
            
            
            const idade = pet.idade_anos ? `${pet.idade_anos} anos` : '?';
            const porte = pet.porte || 'Não informado';
            const local = pet.localizacao || 'Não informado';
            const descricao = pet.descricao ? `${pet.descricao.substring(0, 80)}...` : 'Um amigo esperando por você!';
            const taxa = pet.taxa_adocao ? `R$ ${pet.taxa_adocao}` : 'Grátis';

            // --- HTML DO CARD COMPLETO ---
            const cardHTML = `
                <article class="pet-card">
                    <div style="height: 250px; overflow: hidden; background: #eee;">
                        <img src="${imgUrl}" alt="${nome}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://placehold.co/400x300?text=Erro'">
                    </div>
                    <div class="card-content">
                        <div class="pet-header">
                            <h2>${nome}</h2>
                            <span class="badge ${badgeClass}">${especie}</span>
                        </div>
                        <p class="breed" style="color:var(--primary-color); font-size:0.9rem; margin-bottom:10px;">${raca}</p>
                        
                        <!-- SEÇÃO DE DETALHES QUE SUMIU -->
                        <div class="pet-details">
                            <span><i class="fa-regular fa-calendar-alt"></i> ${idade}</span>
                            <span><i class="fa-solid fa-ruler-vertical"></i> ${porte}</span>
                        </div>
                        <div class="location">
                            <i class="fa-solid fa-map-marker-alt"></i> ${local}
                        </div>
                        <p style="font-size: 0.8rem; color: #888; margin-top: 10px; min-height: 40px;">${descricao}</p>
                        
                        <div class="card-footer">
                            <div>
                                <span style="font-size:0.7rem; display:block; opacity:0.7;">Taxa</span>
                                <span class="price">${taxa}</span>
                            </div>
                            <a href="public/detalhe_pet.php?id=${pet.id}" class="btn-details">Ver Detalhes</a>
                        </div>
                    </div>
                </article>
            `;
            container.innerHTML += cardHTML;
        });

    } catch (error) {
        console.error("ERRO:", error);
        container.innerHTML = `<p style="text-align:center; color:red;">Erro ao carregar os pets.</p>`;
    }
}

/* =========================================
   2. CONFIGURAÇÃO DE TEMA (CLARO/ESCURO)
   ========================================= */
function configurarTema() {
    const themeBtn = document.getElementById('theme-toggle');
    const body = document.body;

    // Verifica se já existe preferência salva
    if(localStorage.getItem('tema') === 'escuro') {
        body.classList.add('dark-theme');
    }

    if(themeBtn) {
        themeBtn.addEventListener('click', () => {
            body.classList.toggle('dark-theme');
            
            // Salva a escolha no navegador
            if(body.classList.contains('dark-theme')) {
                localStorage.setItem('tema', 'escuro');
            } else {
                localStorage.setItem('tema', 'claro');
            }
        });
    }
}

/* =========================================
   3. RASTRO DE PATINHAS (MOUSE)
   ========================================= */
let ultimaPatinha = 0;

function criarPatinha(e) {
    // Controla a velocidade (1 patinha a cada 100ms)
    const agora = Date.now();
    if (agora - ultimaPatinha < 100) return;
    ultimaPatinha = agora;

    const patinha = document.createElement('i');
    patinha.classList.add('fa-solid', 'fa-paw', 'paw-print');
    
    // Posição do mouse
    patinha.style.left = e.clientX + 'px';
    patinha.style.top = e.clientY + 'px';
    
    // Estilo aleatório
    const tamanho = Math.random() * (25 - 15) + 15;
    patinha.style.fontSize = tamanho + 'px';
    
    const rotacao = Math.random() * (30 - (-30)) + (-30);
    patinha.style.transform = `translate(-50%, -50%) rotate(${rotacao}deg)`;

    // Ajusta cor baseada no tema
    if (document.body.classList.contains('dark-theme')) {
        patinha.style.color = 'rgba(255, 255, 255, 0.2)';
    } else {
        patinha.style.color = 'rgba(54, 162, 192, 0.3)';
    }

    document.body.appendChild(patinha);

    // Remove após animação
    setTimeout(() => { patinha.remove(); }, 1000);
}

/* =========================================
   4. FUNDO ANIMADO (ÍCONES FLUTUANTES)
   ========================================= */
function criarFundoAnimado() {
    // Cria o container do fundo se não existir
    let bgContainer = document.querySelector('.floating-bg');
    if(!bgContainer) {
        bgContainer = document.createElement('div');
        bgContainer.classList.add('floating-bg');
        document.body.appendChild(bgContainer);
    }

    const icones = ['fa-bone', 'fa-paw', 'fa-heart', 'fa-star', 'fa-dog', 'fa-cat'];
    
    // Cria 15 elementos flutuantes
    for (let i = 0; i < 15; i++) {
        const icone = document.createElement('i');
        const randomIcon = icones[Math.floor(Math.random() * icones.length)];
        icone.classList.add('fa-solid', randomIcon, 'float-icon');

        
        icone.style.left = Math.random() * 100 + '%';
        
        const tamanho = Math.random() * (60 - 20) + 20;
        icone.style.fontSize = tamanho + 'px';
        
        const duracao = Math.random() * (25 - 10) + 10;
        icone.style.animationDuration = duracao + 's';
        
        const delay = Math.random() * 20;
        icone.style.animationDelay = delay + 's';

        bgContainer.appendChild(icone);
    }
}

/* =========================================
   LÓGICA DO MODAL/POP-UP DE DOAÇÃO
   ========================================= */
document.addEventListener('DOMContentLoaded', () => {
    
    const modal = document.getElementById('doacao-modal');
    const closeModalBtn = document.getElementById('close-modal');

    // Função para ABRIR o modal
    const openModal = () => {
        if(modal) modal.classList.add('active');
    };

    // Função para FECHAR o modal
    const closeModal = () => {
        if(modal) modal.classList.remove('active');
    };

    
    setTimeout(openModal, 3000);

    
    if(closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }

    
    if(modal) {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });
    }
});