<?php
/** Essai d'intégration sur un brouillon temporaire, supprimé à la fin. */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$_SERVER['SERVER_NAME'] = 'localhost';
require dirname(__DIR__, 4) . '/wp-load.php';
$id = 0;
$checks = 0;
$extra_languages = function ($languages) { return $languages + array('nl' => 'Nederlands', 'es' => 'Español', 'ru' => 'Русский', 'pt-br' => 'Português (Brasil)'); };
function langues_multi_check($condition, $message) {
    global $checks;
    if (!$condition) { throw new RuntimeException($message); }
    $checks++;
}
try {
    $field = acf_get_field('artiste_intro');
    langues_multi_check($field && 'atelier_multi' === $field['type'], 'Type ACF pilote chargé');
    $id = wp_insert_post(array('post_type' => 'page', 'post_status' => 'draft', 'post_title' => 'Essai temporaire Langues multi'), true);
    if (is_wp_error($id)) { throw new RuntimeException($id->get_error_message()); }
    $legacy = array('title' => 'Test', 'ready' => true, 'acf' => array($field['key'] => 'Legacy English'));
    update_post_meta($id, '_atelier_langues_en', $legacy);
    update_post_meta($id, 'artiste_intro', 'Ancien français');
    update_post_meta($id, '_artiste_intro', $field['key']);
    $type = acf_get_field_type('atelier_multi');
    acf_set_form_data('post_id', $id);
    $render = $field;
    $render['value'] = 'Ancien français';
    $render['id'] = 'test-multi';
    $render['name'] = 'acf[' . $field['key'] . ']';
    ob_start(); $type->render_field($render); $html = ob_get_clean();
    langues_multi_check(substr_count($html, 'role="tab"') === 2 && strpos($html, 'role="tablist"') !== false, 'Deux onglets reliés aux zones de texte');
    langues_multi_check(strpos($html, '[fr]') !== false && strpos($html, '[en]') !== false && strpos($html, ' disabled') === false, 'Les deux langues sont envoyées même si un onglet est masqué');
    langues_multi_check(strpos($html, 'Ancien français') !== false && strpos($html, 'Legacy English') !== false, 'Reprise des deux valeurs sans écriture');
    langues_multi_check(get_post_meta($id, 'artiste_intro', true) === 'Ancien français', 'Affichage sans migration automatique');
    $fr = "Biographie & détails\nTexte <multi_en> littéral";
    $en = "Biography & details\nEnglish text";
    langues_multi_check(true === $type->validate_value(true, array('fr' => $fr, 'en' => $en), $field, ''), 'Valeur complète acceptée');
    langues_multi_check(is_string($type->validate_value(true, array('fr' => $fr), $field, '')), 'Formulaire incomplet refusé');
    update_field($field['key'], array('fr' => $fr, 'en' => $en), $id);
    $raw = get_post_meta($id, 'artiste_intro', true);
    $clean_fr = sanitize_textarea_field($fr);
    langues_multi_check(atelier_langues_multi_decode($raw) === array('fr' => $clean_fr, 'en' => $en), 'Deux langues dans une valeur et décodage fidèle');
    langues_multi_check(get_post_meta($id, '_atelier_langues_en', true) === $legacy, 'Ancien stockage conservé');
    set_query_var('atelier_lang', 'fr');
    langues_multi_check(apply_filters('acf/load_value', $raw, $id, $field) === $clean_fr, 'Affichage français sans enveloppe multi');
    set_query_var('atelier_lang', 'en');
    langues_multi_check(apply_filters('acf/load_value', $raw, $id, $field) === $en, 'Affichage anglais');
    $empty_en = atelier_langues_multi_encode(array('fr' => $clean_fr, 'en' => ''));
    langues_multi_check(apply_filters('acf/load_value', $empty_en, $id, $field) === $clean_fr, 'Repli français si anglais vide');
    langues_multi_check($type->update_value('Valeur isolée', $id, $field) === $raw, 'Une écriture incompatible préserve les données');
    foreach (atelier_langues_multi_fields() as $name) {
        $candidate = acf_get_field($name);
        langues_multi_check($candidate && 'atelier_multi' === $candidate['type'], 'Onglets disponibles : ' . $name);
        $rich = 'wysiwyg' === $candidate['langues_type'];
        $values = array('fr' => $rich ? '<p>Texte <strong>français</strong></p>' : 'Texte français', 'en' => $rich ? '<p>English <em>text</em></p>' : 'English text');
        update_field($candidate['key'], $values, $id);
        $stored = get_post_meta($id, $name, true);
        langues_multi_check(atelier_langues_multi_decode($stored) === $values, 'Stockage fidèle : ' . $name);
        set_query_var('atelier_lang', 'en');
        langues_multi_check(apply_filters('acf/load_value', $stored, $id, $candidate) === $values['en'], 'Affichage anglais : ' . $name);
        acf_flush_value_cache($id, $name);
        $public_value = get_field($candidate['key'], $id);
        langues_multi_check(strpos($public_value, $rich ? '<em>text</em>' : 'English text') !== false && strpos($public_value, '<multi>') === false, 'Lecture réelle ACF et formatage : ' . $name);
        if ($rich) {
            $formatted = $type->format_value($values['en'], $id, $candidate);
            langues_multi_check(strpos($formatted, '<em>text</em>') !== false && strpos($formatted, '<multi>') === false, 'Mise en forme riche conservée : ' . $name);
            $unsafe = $type->update_value(array('fr' => '<script>alert(1)</script><p>Texte</p>', 'en' => ''), $id, $candidate);
            langues_multi_check(strpos(atelier_langues_multi_decode($unsafe)['fr'], '<script>') === false, 'HTML dangereux filtré : ' . $name);
        }
    }
    foreach (array('contact_email', 'oeuvre_dimensions', 'oeuvre_annee', 'accueil_logo', 'accueil_carrousel') as $name) {
        langues_multi_check('atelier_multi' !== acf_get_field($name)['type'], 'Champ partagé inchangé : ' . $name);
    }
    $packed = atelier_langues_multi_encode(array('fr' => 'Arts plastiques', 'en' => 'Visual Arts'));
    langues_multi_check(atelier_langues_native_text($packed, '', 'en') === 'Visual Arts', 'Lecture de la traduction d’un libellé migré');
    langues_multi_check(atelier_langues_native_text('Legacy', '', 'en') === 'Legacy', 'Ancien libellé encore lisible');
    // Extension réelle du même code à six langues, sans changer la configuration du site.
    add_filter('atelier_langues_languages', $extra_languages);
    $many = array('fr' => 'Texte français & lumière', 'en' => 'English text', 'nl' => 'Nederlandse tekst', 'es' => 'Texto español', 'ru' => 'Русский текст', 'pt-br' => 'Texto português');
    langues_multi_check(atelier_langues_multi_decode(atelier_langues_multi_encode($many)) === $many, 'Stockage de six langues et caractères cyrilliques');
    langues_multi_check(null === atelier_langues_multi_decode('<multi><multi_fr>A</multi_fr><multi_fr>B</multi_fr></multi>'), 'Balises dupliquées refusées');
    langues_multi_check(null === atelier_langues_multi_decode('<multi><multi_fr>A</multi_en></multi>'), 'Balises incohérentes refusées');
    langues_multi_check(!atelier_langues_multi_valid(array('fr' => 'Texte', 'en' => 'Text')), 'Ancien formulaire refusé si une langue a été ajoutée entre-temps');
    $encoded = atelier_langues_multi_encode($many + array('de' => 'Texte conservé hors configuration'));
    update_post_meta($id, 'artiste_intro', $encoded);
    update_field($field['key'], $many, $id);
    $stored_many = atelier_langues_multi_decode(get_post_meta($id, 'artiste_intro', true));
    langues_multi_check($stored_many['de'] === 'Texte conservé hors configuration', 'Langue désactivée conservée à l’enregistrement');
    $render['value'] = get_post_meta($id, 'artiste_intro', true);
    ob_start(); $type->render_field($render); $many_editor = ob_get_clean();
    langues_multi_check(substr_count($many_editor, 'role="tab"') === 6 && strpos($many_editor, '[ru]') !== false, 'Six onglets et leurs noms de formulaire générés depuis la configuration');
    $submitted = array();
    foreach (atelier_langues_languages() as $code => $label) { $submitted[$code] = array('title' => 'Title ' . $code, 'content' => '<p>Content ' . $code . '</p>', 'excerpt' => 'Excerpt ' . $code, 'ready' => 1); }
    $before_title = get_post_field('post_title', $id);
    $incomplete = $submitted; unset($incomplete['ru']['content']);
    langues_multi_check(is_wp_error(atelier_langues_save_post_translations($id, $incomplete)) && $before_title === get_post_field('post_title', $id), 'Formulaire incomplet : aucun titre partiellement enregistré');
    langues_multi_check(true === atelier_langues_save_post_translations($id, $submitted), 'Enregistrement des contenus natifs dans toutes les langues');
    langues_multi_check(atelier_langues_translation($id, 'en')['acf'] === $legacy['acf'], 'Anciennes traductions ACF anglaises préservées');
    foreach (array_keys(atelier_langues_languages()) as $code) {
        set_query_var('atelier_lang', $code);
        acf_flush_value_cache($id, 'artiste_intro');
        langues_multi_check(get_field($field['key'], $id) === $many[$code], 'Texte ACF public : ' . $code);
        langues_multi_check(get_the_title($id) === 'Title ' . $code, 'Titre WordPress public : ' . $code);
    }
    langues_multi_check(atelier_langues_browser_language('nl-BE,nl;q=0.9') === 'nl', 'Détection néerlandaise régionale');
    langues_multi_check(atelier_langues_browser_language('ru-RU,es;q=0.8') === 'ru', 'Détection russe');
    langues_multi_check(atelier_langues_browser_language('es;q=0.4,nl;q=0.9') === 'nl', 'Pondérations entre nouvelles langues');
    langues_multi_check(atelier_langues_browser_language('pt-BR,fr;q=0.9') === 'pt-br', 'Variante régionale configurée');
    $home = untrailingslashit(get_option('home'));
    langues_multi_check(atelier_langues_url($home . '/en/contact/?test=1#texte', 'nl') === $home . '/nl/contact/?test=1#texte', 'Changement de préfixe en néerlandais');
    $rules = atelier_langues_rewrite_rules(array('oeuvre/([^/]+)/?$' => 'index.php?oeuvre=$matches[1]'));
    langues_multi_check($rules['ru/oeuvre/([^/]+)/?$'] === 'index.php?oeuvre=$matches[1]&atelier_lang=ru', 'Règles russes et captures inchangées');
    set_query_var('atelier_lang', 'ru');
    langues_multi_check(atelier_langues_native_text(atelier_langues_multi_encode($many), 'Source') === $many['ru'], 'Libellé natif russe');
    langues_multi_check(atelier_langues_native_text('Legacy English', 'Source') === 'Source', 'Ancien anglais non présenté comme du russe');
    $saved_native = atelier_langues_native_save('Source', atelier_langues_multi_encode($many), array_diff_key($many, array('fr' => true)), 'text');
    langues_multi_check(atelier_langues_multi_decode($saved_native)['nl'] === $many['nl'], 'Sauvegarde multilingue des menus et disciplines');
    $GLOBALS['wp_query']->is_singular = true;
    $GLOBALS['wp_query']->queried_object_id = $id;
    $GLOBALS['wp_query']->queried_object = get_post($id);
    ob_start(); atelier_langues_switcher(); $switcher = ob_get_clean();
    langues_multi_check(strpos($switcher, '>NL</a>') !== false && strpos($switcher, '>RU</a>') !== false, 'Sélecteur public étendu aux langues prêtes');
    $unready = atelier_langues_translation($id, 'ru'); $unready['ready'] = false;
    update_post_meta($id, '_atelier_langues_ru', $unready);
    ob_start(); atelier_langues_switcher(); $switcher = ob_get_clean();
    langues_multi_check(strpos($switcher, '>RU</a>') === false, 'Langue non relue masquée sur le site');
    remove_filter('atelier_langues_languages', $extra_languages);
    update_field($field['key'], array('fr' => 'Nouvelle source', 'en' => 'New English'), $id);
    $preserved = atelier_langues_multi_decode(get_post_meta($id, 'artiste_intro', true));
    langues_multi_check($preserved['ru'] === $many['ru'] && $preserved['nl'] === $many['nl'], 'Désactivation de langues sans suppression des textes');
    $remaining = array_intersect_key($submitted, atelier_langues_languages());
    langues_multi_check(true === atelier_langues_save_post_translations($id, $remaining) && get_post_meta($id, '_atelier_langues_nl', true)['title'] === 'Title nl', 'Traduction WordPress désactivée conservée à l’enregistrement');
    echo $checks . " contrôles multi réussis.\n";
} finally {
    remove_filter('atelier_langues_languages', $extra_languages);
    if (is_int($id) && $id > 0) { wp_delete_post($id, true); }
}
