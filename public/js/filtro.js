document.addEventListener('DOMContentLoaded', function() {
    
    const urlParams = new URLSearchParams(window.location.search);
    const activeEspecie = urlParams.get('especie');
    const activePorte = urlParams.get('porte');

    
    const filterButtons = document.querySelectorAll('.filter-btn');

    
    filterButtons.forEach(btn => btn.classList.remove('active'));

    let hasActiveFilter = false;

    
    filterButtons.forEach(btn => {
        const filterType = btn.dataset.filter;
        const filterValue = btn.dataset.value;

        
        if ((filterType === 'especie' && filterValue === activeEspecie) || 
            (filterType === 'porte' && filterValue === activePorte)) {
            btn.classList.add('active');
            hasActiveFilter = true;
        }
    });

    
    if (!hasActiveFilter) {
        const todosBtn = document.querySelector('.filter-btn[data-filter="todos"]');
        if (todosBtn) {
            todosBtn.classList.add('active');
        }
    }
});