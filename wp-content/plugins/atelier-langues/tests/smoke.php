<?php
/** Vérifications locales en lecture seule : php wp-content/plugins/atelier-langues/tests/smoke.php */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
$_SERVER['SERVER_NAME'] = 'localhost';
require dirname(__DIR__, 4) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/template.php';
$checks = 0;
function atelier_test($condition, $message) {
    global $checks;
    if (!$condition) {
        fwrite(STDERR, 'ECHEC : ' . $message . PHP_EOL);
        exit(1);
    }
    $checks++;
}

atelier_test('en' === atelier_langues_browser_language('de,fr;q=0.2,en-GB;q=0.9'), 'Pondérations navigateur');
atelier_test('fr' === atelier_langues_browser_language('es, en;q=0'), 'Langue inconnue et q=0');
atelier_test('fr' === atelier_langues_browser_language(''), 'En-tête absent');
$home = untrailingslashit(get_option('home'));
atelier_test($home . '/en/contact/?test=1#texte' === atelier_langues_url($home . '/fr/contact/?test=1#texte', 'en'), 'Préfixe, sous-dossier, paramètres et fragment');
atelier_test('https://example.org/contact/' === atelier_langues_url('https://example.org/contact/', 'en'), 'Lien externe intact');
atelier_test($home . '/wp-admin/' === atelier_langues_url($home . '/wp-admin/', 'en'), 'Administration exclue');
$original = array('oeuvre/([^/]+)/?$' => 'index.php?oeuvre=$matches[1]');
$rules = atelier_langues_rewrite_rules($original);
atelier_test($rules['oeuvre/([^/]+)/?$'] === $original['oeuvre/([^/]+)/?$'], 'Anciennes règles conservées');
atelier_test('index.php?oeuvre=$matches[1]&atelier_lang=en' === $rules['en/oeuvre/([^/]+)/?$'], 'Groupes de capture inchangés');
atelier_test(false !== strpos($rules['en/?$'], 'page_id=' . get_option('page_on_front')), 'Racine vers accueil statique');
atelier_test(false === strpos(atelier_langues_clean_value('<script>alert(1)</script><p>Texte</p>', 'wysiwyg'), '<script>'), 'Suppression script');
atelier_test('' === atelier_langues_clean_value('javascript:alert(1)', 'url'), 'URL dangereuse refusée');

// Une traduction fictive en mémoire, sans modifier les contenus de la cliente.
$id = (int) get_option('page_on_front');
$fixture = array('title' => 'TEST TITLE', 'content' => '<p>TEST CONTENT</p>', 'ready' => true, 'acf' => array('field_atelier_accueil_intro' => 'TEST INTRO'));
$override = function ($value, $object_id, $key) use ($id, $fixture) {
    return $id === (int) $object_id && '_atelier_langues_en' === $key ? array($fixture) : $value;
};
add_filter('get_post_metadata', $override, 10, 3);
set_query_var('atelier_lang', 'en');
atelier_test(atelier_langues_has_translation($id), 'Association au même identifiant');
atelier_test('TEST TITLE' === get_the_title($id), 'Titre anglais');
global $wp_query, $post;
$wp_query = new WP_Query(array('page_id' => $id));
$wp_query->set('atelier_lang', 'en');
$post = get_post($id);
setup_postdata($post);
atelier_test(false !== strpos(apply_filters('the_content', $post->post_content), 'TEST CONTENT'), 'Contenu anglais avant formatage');
ob_start();
atelier_langues_switcher();
$switcher = ob_get_clean();
atelier_test(false !== strpos($switcher, 'English') && false !== strpos($switcher, 'aria-current="page"'), 'Sélecteur et langue active');
ob_start();
do_action('wp_head');
$head = ob_get_clean();
atelier_test(false !== strpos($head, 'hreflang="en"') && false !== strpos($head, 'hreflang="fr"'), 'Alternates réciproques avec traduction');
$field = acf_get_field('field_atelier_accueil_intro');
atelier_test('TEST INTRO' === apply_filters('acf/load_value', 'Français', $id, $field), 'Valeur ACF anglaise');
$field['key'] = 'missing';
atelier_test('Français' === apply_filters('acf/load_value', 'Français', $id, $field), 'Repli ACF');
set_query_var('atelier_lang', 'fr');
atelier_test('TEST TITLE' !== get_the_title($id), 'Français conservé');
remove_filter('get_post_metadata', $override, 10);
$admins = get_users(array('role' => 'administrator', 'number' => 1));
if ($admins) {
    wp_set_current_user($admins[0]->ID);
    $_GET['content_id'] = $id;
    ob_start();
    atelier_langues_render_admin_page();
    $html = ob_get_clean();
    atelier_test(false !== strpos($html, 'name="english[acf][field_atelier_accueil_intro]"'), 'Champs imbriqués du formulaire');
    atelier_test(false !== strpos($html, 'name="_wpnonce"'), 'Nonce présent');
}
// Traduction de menu simulée : pas d'écriture dans les menus existants.
$menu_item = (object) array('ID' => 987654, 'post_type' => 'nav_menu_item', 'title' => 'Accueil');
$menu_override = function ($value, $object_id, $key) {
    return 987654 === (int) $object_id && '_atelier_langues_menu_en' === $key ? array('Home "test"') : $value;
};
add_filter('get_post_metadata', $menu_override, 10, 3);
set_query_var('atelier_lang', 'en');
atelier_test('Home "test"' === atelier_langues_menu_label($menu_item)->title, 'Libellé anglais du menu');
atelier_test('Accueil' === $menu_item->title, 'Objet de menu original conservé');
set_query_var('atelier_lang', 'fr');
atelier_test('Accueil' === atelier_langues_menu_label($menu_item)->title, 'Menu français conservé');
set_query_var('atelier_lang', 'en');
$missing_item = clone $menu_item;
$missing_item->ID = 987655;
atelier_test('Accueil' === atelier_langues_menu_label($missing_item)->title, 'Repli du menu sans anglais');
ob_start();
atelier_langues_menu_field(987654);
$menu_field = ob_get_clean();
atelier_test(false !== strpos($menu_field, 'Home &quot;test&quot;'), 'Échappement du champ de menu');
atelier_test(false !== strpos($menu_field, 'atelier_menu_nonce[987654]'), 'Nonce propre à chaque entrée');
remove_filter('get_post_metadata', $menu_override, 10);
$theme_override = function () {
    return array(md5('L’artiste') => array('fr' => 'Présentation artiste', 'en' => 'Artist profile'), md5('Technique') => array('fr' => 'Méthode', 'en' => ''));
};
add_filter('pre_option_atelier_langues_theme_strings', $theme_override);
set_query_var('atelier_lang', 'en');
atelier_test('Artist profile' === __('L’artiste', 'atelier-artiste'), 'Chaîne native du thème en anglais');
atelier_test('Méthode' === __('Technique', 'atelier-artiste'), 'Repli sur le français personnalisé');
atelier_test('L’artiste' === __('L’artiste', 'autre-extension'), 'Autres domaines non modifiés');
set_query_var('atelier_lang', 'fr');
atelier_test('Présentation artiste' === __('L’artiste', 'atelier-artiste'), 'Libellé français personnalisable');
ob_start();
atelier_langues_theme_form();
$theme_form = ob_get_clean();
atelier_test(false !== strpos($theme_form, 'french[theme][' . md5('L’artiste') . ']'), 'Formulaire de textes séparé');
remove_filter('pre_option_atelier_langues_theme_strings', $theme_override);
$term_override = function ($value, $id, $key) {
    return 987654 === (int) $id && '_atelier_langues_name_en' === $key ? array('Visual Arts') : $value;
};
add_filter('get_term_metadata', $term_override, 10, 3);
$wp_query->queried_object = new WP_Term((object) array('term_id' => 987654, 'taxonomy' => 'discipline', 'name' => 'Arts plastiques'));
set_query_var('atelier_lang', 'en');
atelier_test('Visual Arts' === apply_filters('single_term_title', 'Arts plastiques'), 'Titre anglais de discipline');
$wp_query->is_singular = false;
$wp_query->is_tax = true;
$wp_query->queried_object_id = 987654;
$_SERVER['REQUEST_URI'] = wp_parse_url($home, PHP_URL_PATH) . '/en/discipline/arts-plastiques/?test=1';
ob_start();
atelier_langues_switcher();
$term_switcher = ob_get_clean();
atelier_test(false !== strpos($term_switcher, '>EN</a>') && false !== strpos($term_switcher, '/fr/discipline/arts-plastiques/'), 'Sélecteur sur discipline traduite');
atelier_test(false !== strpos($term_switcher, 'test=1'), 'Paramètre conservé sur discipline');
set_query_var('atelier_lang', 'fr');
atelier_test('Arts plastiques' === apply_filters('single_term_title', 'Arts plastiques'), 'Nom français conservé');
set_query_var('atelier_lang', 'en');
$wp_query->queried_object->term_id = 987655;
$wp_query->queried_object_id = 987655;
atelier_test('Arts plastiques' === apply_filters('single_term_title', 'Arts plastiques'), 'Repli discipline sans anglais');
ob_start();
atelier_langues_switcher();
$missing_switcher = ob_get_clean();
atelier_test(false === strpos($missing_switcher, '>EN</a>'), 'Lien anglais masqué si discipline non traduite');
$wp_query->queried_object->term_id = 987654;
$wp_query->queried_object->taxonomy = 'category';
atelier_test('Catégorie' === apply_filters('single_term_title', 'Catégorie'), 'Autres taxonomies intactes');
remove_filter('get_term_metadata', $term_override, 10);
echo $checks . ' vérifications réussies (aucune traduction enregistrée).' . PHP_EOL;
