// Le dialogue natif gère le focus, le clavier et la touche Échap.
const viewer = document.querySelector('.image-viewer');
if (viewer && typeof viewer.showModal === 'function') {
    const largeImage = viewer.querySelector('img');
    document.querySelectorAll('.single-oeuvre main img').forEach((image) => {
        if (image.closest('a, button')) return;
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'image-zoom';
        button.setAttribute('aria-label', `${viewer.dataset.openLabel} : ${image.alt || document.querySelector('main h1').textContent}`);
        button.setAttribute('aria-haspopup', 'dialog');
        image.before(button);
        button.append(image);
        image.closest('figure')?.removeAttribute('tabindex');
        button.addEventListener('click', () => {
            largeImage.src = image.currentSrc || image.src;
            largeImage.alt = image.alt;
            viewer.showModal();
        });
    });
    viewer.querySelector('button').addEventListener('click', () => viewer.close());
    viewer.addEventListener('click', (event) => {
        if (event.target === viewer) viewer.close();
    });
    viewer.addEventListener('close', () => largeImage.removeAttribute('src'));
}
