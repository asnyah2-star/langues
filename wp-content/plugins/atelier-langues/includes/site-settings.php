<?php
/** Un point d'entrée ; les données restent dans leurs emplacements existants. */
if (!defined('ABSPATH')) { exit; }

function atelier_langues_social_items() {
    $locations = get_nav_menu_locations();
    return empty($locations['social']) ? array() : (array) wp_get_nav_menu_items($locations['social']);
}

add_action('admin_menu', function () {
    add_menu_page('Réglages du site', 'Réglages du site', 'manage_options', 'atelier-site', 'atelier_langues_site_page', 'dashicons-admin-home', 61);
});

function atelier_langues_site_page() {
    if (!current_user_can('manage_options') || !current_user_can('edit_theme_options')) { return; }
    $front = (int) get_option('page_on_front');
    $logo = (int) get_theme_mod('custom_logo');
    $email = (string) get_post_meta($front, 'contact_email', true);
    $social = atelier_langues_social_items();
    foreach ($social as $item) {
        if (in_array('icon-email', $item->classes, true) && 0 === strpos($item->url, 'mailto:')) {
            $email = substr($item->url, 7);
            break;
        }
    }
    echo '<div class="wrap"><h1>Réglages du site</h1><p>Logo du menu, coordonnées et pied de page. Les textes ACF se traduisent dans les fiches des pages et créations. Les titres et contenus principaux se traduisent dans Langues.</p>';
    if (isset($_GET['saved'])) { echo '<div class="notice notice-success"><p>Réglages enregistrés.</p></div>'; }
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="atelier_site_save">';
    wp_nonce_field('atelier_site_save');
    echo '<table class="form-table"><tbody><tr><th>Logo du menu</th><td><div id="atelier-logo-preview">';
    if ($logo) { echo wp_get_attachment_image($logo, 'thumbnail'); }
    echo '</div><input type="hidden" id="atelier-logo-id" name="logo" value="' . esc_attr($logo) . '"><button type="button" class="button" id="atelier-logo-select">Choisir / remplacer</button> <button type="button" class="button" id="atelier-logo-remove">Retirer</button><p class="description">Les logos de l’accueil et de Contact se règlent sur leurs pages.</p></td></tr>';
    echo '<tr><th><label for="atelier-email">E-mail de contact</label></th><td><input class="regular-text" type="email" id="atelier-email" name="email" value="' . esc_attr($email) . '"><p class="description">À l’enregistrement, met à jour le champ e-mail existant et les icônes e-mail du menu social.</p></td></tr>';
    foreach ($social as $item) {
        if ('custom' !== $item->type || in_array('icon-email', $item->classes, true)) { continue; }
        echo '<tr><th><label for="atelier-social-' . esc_attr($item->ID) . '">' . esc_html($item->title) . '</label></th><td><input class="regular-text" type="url" id="atelier-social-' . esc_attr($item->ID) . '" name="social[' . esc_attr($item->ID) . ']" value="' . esc_attr($item->url) . '"><p class="description">Laisser vide pour masquer cette icône. Pour ajouter ou réordonner : Apparence → Menus → Liens sociaux.</p></td></tr>';
    }
    $footer_field = function_exists('acf_get_field') ? acf_get_field('footer_texte') : false;
    if ($front && $footer_field) {
        echo '<tr><th>Pied de page</th><td>';
        $stored = get_post_meta($front, 'footer_texte', true);
        $parts = atelier_langues_multi_decode($stored);
        $names = array(); $legacy = array();
        foreach (atelier_langues_languages() as $lang => $label) {
            $names[$lang] = 'footer[' . $lang . ']';
            if (null === $parts) { $translation = atelier_langues_translation($front, $lang); $legacy[$lang] = atelier_langues_source() === $lang ? $stored : ($translation['acf'][$footer_field['key']] ?? ''); }
        }
        atelier_langues_multi_editor(atelier_langues_multi_parts($parts ?? $legacy), $names, 'textarea', 'Pied de page');
        echo '<p class="description">Partagé par toutes les pages. Déclarez les traductions de l’accueil disponibles dans Langues.</p></td></tr>';
    }
    echo '</tbody></table>';
    submit_button('Enregistrer les réglages du site');
    echo '</form><h2>Autres contenus</h2><ul>';
    foreach (array('accueil' => 'Accueil : logo, titre, introduction et carrousel', 'contact' => 'Contact : introduction, logo et signature', 'a-propos' => 'À propos : présentation et portrait') as $slug => $label) {
        $page = get_page_by_path($slug);
        if ($page) { echo '<li><a href="' . esc_url(get_edit_post_link($page->ID)) . '">' . esc_html($label) . '</a></li>'; }
    }
    echo '<li><a href="' . esc_url(admin_url('options-general.php?page=atelier-langues')) . '">Traductions et textes du thème</a></li></ul></div>';
}

add_action('admin_enqueue_scripts', function ($hook) {
    if ('toplevel_page_atelier-site' !== $hook) { return; }
    wp_enqueue_media();
    wp_add_inline_script('media-editor', "document.getElementById('atelier-logo-select').addEventListener('click',()=>{const picker=wp.media({title:'Choisir le logo',library:{type:'image'},multiple:false});picker.on('select',()=>{const file=picker.state().get('selection').first().toJSON();document.getElementById('atelier-logo-id').value=file.id;const img=document.createElement('img');img.src=file.sizes?.thumbnail?.url||file.url;img.style.maxWidth='150px';img.alt='';document.getElementById('atelier-logo-preview').replaceChildren(img);});picker.open();});document.getElementById('atelier-logo-remove').addEventListener('click',()=>{document.getElementById('atelier-logo-id').value='0';document.getElementById('atelier-logo-preview').replaceChildren();});");
});

add_action('admin_post_atelier_site_save', function () {
    if (!current_user_can('manage_options') || !current_user_can('edit_theme_options')) { wp_die('Accès refusé.', '', array('response' => 403)); }
    check_admin_referer('atelier_site_save');
    $logo = absint($_POST['logo'] ?? 0);
    $raw_email = $_POST['email'] ?? null;
    if (!is_string($raw_email) || ($raw_email !== '' && !is_email(wp_unslash($raw_email))) || ($logo && !wp_attachment_is_image($logo))) {
        wp_die('Logo ou e-mail invalide. Aucune modification enregistrée.', '', array('response' => 400));
    }
    $items = atelier_langues_social_items();
    $urls = array();
    foreach ($items as $item) {
        $raw = $_POST['social'][$item->ID] ?? null;
        if ('custom' !== $item->type || !is_string($raw) || in_array('icon-email', $item->classes, true)) { continue; }
        $raw = trim(wp_unslash($raw));
        if ('' !== $raw && (!filter_var($raw, FILTER_VALIDATE_URL) || !in_array(wp_parse_url($raw, PHP_URL_SCHEME), array('http', 'https'), true))) {
            wp_die('Lien de réseau social invalide. Aucune modification enregistrée.', '', array('response' => 400));
        }
        $urls[$item->ID] = esc_url_raw($raw);
    }
    set_theme_mod('custom_logo', $logo);
    $front = (int) get_option('page_on_front');
    $email = sanitize_email(wp_unslash($raw_email));
    if ($front && function_exists('update_field')) { update_field('contact_email', $email, $front); }
    foreach ($items as $item) {
        if (in_array('icon-email', $item->classes, true) && 'custom' === $item->type) { $urls[$item->ID] = $email ? 'mailto:' . $email : ''; }
        if (array_key_exists($item->ID, $urls)) { update_post_meta($item->ID, '_menu_item_url', wp_slash($urls[$item->ID])); }
    }
    if ($front && function_exists('acf_get_field') && isset($_POST['footer']) && atelier_langues_multi_valid($_POST['footer'])) {
        $field = acf_get_field('footer_texte');
        if ($field) {
            update_field($field['key'], wp_unslash($_POST['footer']), $front);
        }
    }
    wp_safe_redirect(admin_url('admin.php?page=atelier-site&saved=1'));
    exit;
});

// Masquer les destinations volontairement vidées, sans supprimer les entrées de menu.
add_filter('wp_nav_menu_objects', function ($items, $args) {
    if ('social' === ($args->theme_location ?? '')) {
        $items = array_filter($items, function ($item) { return '' !== trim((string) $item->url); });
    }
    return $items;
}, 10, 2);
