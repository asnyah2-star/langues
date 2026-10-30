(() => {
    const initialize = () => {
        document.querySelectorAll('.langues-multi-editors').forEach((editor) => {
            if (editor.dataset.tabsReady) return;
            const nativeWrapper = editor.closest('[data-langues-native]');
            if (nativeWrapper) {
                const native = document.querySelector(nativeWrapper.dataset.languesNative);
                if (native) {
                    const panel = editor.querySelector('.langues-multi-panel');
                    const oldContainer = native.closest('.field-title, .term-name-wrap, .term-description-wrap');
                    if (oldContainer) oldContainer.hidden = true;
                    panel.querySelector('input, textarea').replaceWith(native);
                    panel.querySelector('label').htmlFor = native.id;
                }
            }
            editor.dataset.tabsReady = 'true';
            const list = editor.querySelector('[role="tablist"]');
            const tabs = [...list.querySelectorAll('[role="tab"]')];
            const activate = (selected, focus = false) => {
                tabs.forEach((tab) => {
                    const active = tab === selected;
                    tab.setAttribute('aria-selected', String(active));
                    tab.tabIndex = active ? 0 : -1;
                    const panel = document.getElementById(tab.getAttribute('aria-controls'));
                    panel.hidden = !active;
                    panel.setAttribute('role', 'tabpanel');
                    panel.setAttribute('aria-labelledby', tab.id);
                });
                if (focus) selected.focus();
            };
            tabs.forEach((tab, index) => {
                tab.addEventListener('click', () => activate(tab));
                tab.addEventListener('keydown', (event) => {
                    let next;
                    if (event.key === 'ArrowRight') next = (index + 1) % tabs.length;
                    if (event.key === 'ArrowLeft') next = (index + tabs.length - 1) % tabs.length;
                    if (event.key === 'Home') next = 0;
                    if (event.key === 'End') next = tabs.length - 1;
                    if (next === undefined) return;
                    event.preventDefault();
                    activate(tabs[next], true);
                });
            });
            // Choisir parmi les langues configurées, y compris les variantes régionales.
            const preferences = navigator.languages?.length ? navigator.languages : [navigator.language];
            let initial = tabs[0];
            for (const preference of preferences) {
                let code = preference.toLowerCase();
                let match;
                while (code && !match) {
                    match = tabs.find((tab) => tab.getAttribute('data-language') === code);
                    code = code.includes('-') ? code.slice(0, code.lastIndexOf('-')) : '';
                }
                if (match) { initial = match; break; }
            }
            activate(initial);
            list.hidden = false;
        });
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize);
    else initialize();
    if (window.acf) window.acf.addAction('append', initialize);
    if (window.tinymce) {
        const synchronize = (richEditor) => {
            if (richEditor.languesSynced || !richEditor.targetElm?.closest('.langues-multi-editors')) return;
            richEditor.languesSynced = true;
            richEditor.on('change input undo redo', () => richEditor.save());
        };
        window.tinymce.on('AddEditor', (event) => synchronize(event.editor));
        Object.values(window.tinymce.editors || {}).forEach(synchronize);
    }
    // Les entrées de menu peuvent être ajoutées sans recharger l'écran.
    new MutationObserver(initialize).observe(document.body, { childList: true, subtree: true });
})();
