// Script simples de alunos para ajudar no uso dos filtros da página de pets.
document.addEventListener('DOMContentLoaded', () => {
    const feedback = document.querySelector('.filter-feedback');
    const grid = document.querySelector('[data-js="gridPets"]');
    const limparBtn = document.getElementById('limparFiltros');
    const form = document.querySelector('.filter-form');

    if (feedback && grid) {
        const quantidade = grid.querySelectorAll('.pet-card').length;
        feedback.innerHTML = `<strong>${quantidade}</strong> pet(s) encontrado(s)`;
    }

    if (limparBtn && form) {
        limparBtn.addEventListener('click', () => {
            form.reset();
            form.submit();
        });
    }
});
