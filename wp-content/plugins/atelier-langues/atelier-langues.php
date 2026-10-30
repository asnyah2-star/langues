<?php
/**
 * Plugin Name: Langues
 * Description: Gestion des contenus en français et en anglais.
 * Version: 0.4.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

// Ajouter une langue ici suffit pour les onglets, les URL et la détection.
// La première langue correspond aux contenus natifs et sert de secours.
function atelier_langues_languages() {
    $languages = array(
        'fr' => 'Français',
        'en' => 'English',
        // 'nl' => 'Nederlands',
        // 'es' => 'Español',
        // 'ru' => 'Русский',
    );
    $configured = apply_filters('atelier_langues_languages', $languages);
    $valid = array();
    foreach (is_array($configured) ? $configured : array() as $code => $label) {
        if (is_string($code) && preg_match('/^[a-z]{2,3}(?:-[a-z0-9]{2,8})*$/D', $code) && is_string($label) && '' !== trim($label)) { $valid[$code] = $label; }
    }
    return $valid ?: $languages;
}

function atelier_langues_source() { return array_key_first(atelier_langues_languages()); }

// Textes ACF multilingues de ce site ; médias et coordonnées sont communs.
function atelier_langues_multi_fields() {
    return array('accroche', 'accueil_intro', 'artiste_intro', 'artiste_demarche', 'artiste_parcours', 'contact_intro', 'contact_signature', 'footer_texte', 'oeuvre_technique');
}

require_once plugin_dir_path(__FILE__) . 'includes/content.php';
require_once plugin_dir_path(__FILE__) . 'includes/languages.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/site-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/multi.php';

// Une seule actualisation des règles lors de cette mise à niveau, jamais à chaque visite.
add_action('wp_loaded', function () {
    $version = '0.4.0-' . md5(implode('|', array_keys(atelier_langues_languages())));
    if ($version !== get_option('atelier_langues_rules_version')) {
        flush_rewrite_rules(false);
        update_option('atelier_langues_rules_version', $version);
    }
});
register_deactivation_hook(__FILE__, function () {
    remove_filter('rewrite_rules_array', 'atelier_langues_rewrite_rules');
    flush_rewrite_rules(false);
    delete_option('atelier_langues_rules_version');
});
