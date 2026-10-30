// Le menu reste visible si JavaScript est désactivé.
const menuToggle = document.querySelector('.nav-toggle');
const navigation = document.getElementById('site-navigation');
if (menuToggle && navigation) {
    const compact = window.matchMedia('(max-width: 1100px)');
    const setMenu = (open) => {
        menuToggle.hidden = !compact.matches;
        menuToggle.setAttribute('aria-expanded', String(compact.matches && open));
        navigation.hidden = compact.matches && !open;
    };
    menuToggle.addEventListener('click', () => setMenu(menuToggle.getAttribute('aria-expanded') !== 'true'));
    compact.addEventListener('change', () => {
        if (compact.matches && navigation.contains(document.activeElement)) menuToggle.focus();
        if (!compact.matches && document.activeElement === menuToggle) navigation.querySelector('a, button')?.focus();
        setMenu(false);
    });
    const header = menuToggle.closest('header');
    header.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && compact.matches && !navigation.hidden) {
            setMenu(false);
            menuToggle.focus();
        }
    });
    document.addEventListener('click', (event) => {
        if (!header.contains(event.target)) setMenu(false);
    });
    header.addEventListener('focusout', (event) => {
        if (!header.contains(event.relatedTarget)) setMenu(false);
    });
    setMenu(false);
}

document.querySelectorAll('.submenu-toggle').forEach((button, index) => {
    const submenu = button.parentElement.querySelector('.sub-menu');
    if (!submenu) return;
    submenu.id = `submenu-${index}`;
    button.setAttribute('aria-controls', submenu.id);
    button.parentElement.classList.add('has-submenu-toggle');
    const setOpen = (open) => {
        button.setAttribute('aria-expanded', String(open));
        button.parentElement.classList.toggle('submenu-open', open);
    };
    const close = () => {
        setOpen(false);
    };
    button.parentElement.addEventListener('pointerenter', (event) => {
        if (event.pointerType === 'mouse') setOpen(true);
    });
    button.parentElement.addEventListener('pointerleave', (event) => {
        if (event.pointerType === 'mouse' && !button.parentElement.contains(document.activeElement)) close();
    });
    button.addEventListener('click', () => {
        const open = button.getAttribute('aria-expanded') !== 'true';
        setOpen(open);
    });
    button.parentElement.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            event.stopPropagation();
            close();
            button.focus();
        }
    });
    button.parentElement.addEventListener('focusout', (event) => {
        if (!button.parentElement.contains(event.relatedTarget)) close();
    });
    document.addEventListener('click', (event) => {
        if (!button.parentElement.contains(event.target)) close();
    });
});
