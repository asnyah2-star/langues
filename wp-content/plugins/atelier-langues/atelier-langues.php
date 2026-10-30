<?php
/**
 * Plugin Name: Atelier Langues
 * Description: Gestion des contenus en français et en anglais.
 * Version: 0.2.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/content.php';
require_once plugin_dir_path(__FILE__) . 'includes/languages.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/site-settings.php';

// Une seule actualisation des règles lors de cette mise à niveau, jamais à chaque visite.
add_action('wp_loaded', function () {
    if ('0.2.0-root' !== get_option('atelier_langues_rules_version')) {
        flush_rewrite_rules(false);
        update_option('atelier_langues_rules_version', '0.2.0-root');
    }
});
register_deactivation_hook(__FILE__, function () {
    remove_filter('rewrite_rules_array', 'atelier_langues_rewrite_rules');
    flush_rewrite_rules(false);
    delete_option('atelier_langues_rules_version');
});
