// Défilement manuel : aucun mouvement automatique derrière le texte.
document.querySelectorAll('.home-hero').forEach((hero) => {
    const slides = hero.querySelectorAll('.hero-slide');
    const controls = hero.querySelector('.hero-controls');
    if (!controls || slides.length < 2) return;
    let current = 0;
    controls.hidden = false;
    controls.querySelectorAll('button').forEach((button) => {
        button.addEventListener('click', () => {
            slides[current].hidden = true;
            current = (current + Number(button.dataset.direction) + slides.length) % slides.length;
            slides[current].hidden = false;
            controls.querySelector('.hero-counter').textContent = `${current + 1} / ${slides.length}`;
        });
    });
});
