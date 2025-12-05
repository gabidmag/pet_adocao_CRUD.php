# PetAdopt CRUD

Projeto desenvolvido para a disciplina de **Programação Web** da UNIPÊ (prof. Daniel Brandão). A proposta era construir um CRUD completo utilizando apenas PHP puro, HTML, CSS e MySQL. Nosso tema escolhido foi um portal simples para cadastro e adoção de pets, permitindo que abrigos registrem os animais e que visitantes conheçam os bichinhos disponíveis.

[Documentação do Projeto](https://docs.google.com/document/d/1AfqTI9f5nhlBlI-hn2pSIA3kfYnmmxI0RAkhf8WvFU8/edit?usp=sharing)

## ✨ Objetivos do trabalho
- Praticar os pilares do CRUD (Create, Read, Update, Delete) em PHP.
- Entender a integração entre backend em PHP com MySQL utilizando sqli.
- Organizar o front-end sem frameworks, focando em HTML, CSS e um pouco de JavaScript.
- Produzir documentação e código limpos, pensando em colaboração entre os integrantes.

## 🐾 Principais recursos
- Listagem de pets com filtros básicos por espécie, porte e gênero.
- Página de detalhes com resumo do animal e botão para iniciar o processo de adoção.
- Formulário simples simulando o envio do pedido.
- Área administrativa (em construção) para gerenciar os registros dos animais e adoções.

## 🧱 Tecnologias utilizadas
- PHP 8+ (sem frameworks)
- MySQL / MariaDB
- HTML5 e CSS3
- JavaScript vanilla
- Font Awesome para ícones

## 📂 Estrutura do projeto
```
pet_adocao_CRUD.php/
├── public/
│   ├── index.php                # Home com destaque de pets
│   ├── animais.php              # Listagem com filtros
│   ├── detalhe-animal.php       # Página de detalhes
│   ├── adotar.php               # Formulário de adoção
│   ├── css/style.css
│   ├── js/
│   │   ├── filtro.js
│   │   ├── preview.js
│   │   └── validacao.js
│   └── templates/
│       ├── header.php
│       └── footer.php
├── admin/                       # Área administrativa (CRUD completo)
├── sql/schema.sql               # Script inicial do banco
├── conexao.php / public/conexao.php
└── README.md
```

## 📚 Referências
- [Documentação do PHP](https://www.php.net/manual/pt_BR/)
- Materiais fornecidos pelo professor Daniel Brandão
- Inspirações de sites reais de adoção de pets

---
Qualquer dúvida ou sugestão, fique à vontade para registrar um issue ou falar com a equipe durante as aulas!
