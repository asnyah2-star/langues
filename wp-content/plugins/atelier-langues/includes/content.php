<?php
/** Les traductions sont attachées au contenu d'origine, sans dupliquer ses médias. */
if (!defined('ABSPATH')) {
    exit;
}

function atelier_langues_post_types() {
    return array('page', 'post', 'oeuvre');
}

function atelier_langues_translation($post_id, $language = null) {
    $language = $language ?? atelier_langues_current();
    if (!isset(atelier_langues_languages()[$language])) { return array(); }
    $value = get_post_meta($post_id, '_atelier_langues_' . $language, true);
    return is_array($value) ? $value : array();
}

function atelier_langues_has_translation($post_id, $language = null) {
    $language = $language ?? atelier_langues_current();
    if (atelier_langues_source() === $language) { return true; }
    $value = atelier_langues_translation($post_id, $language);
    return !empty($value['ready']) && !empty($value['title']);
}

function atelier_langues_frontend() {
    return !is_admin() && !(defined('REST_REQUEST') && REST_REQUEST) && !wp_doing_ajax() && !is_feed();
}

add_filter('the_title', function ($title, $post_id) {
    if (atelier_langues_frontend() && atelier_langues_source() !== atelier_langues_current() && atelier_langues_has_translation($post_id)) {
        return atelier_langues_translation($post_id)['title'];
    }
    return $title;
}, 10, 2);

// Avant les filtres de blocs, de paragraphes et de liens du thème.
add_filter('the_content', function ($content) {
    $id = get_the_ID();
    $translation = atelier_langues_translation($id);
    if (atelier_langues_frontend() && atelier_langues_source() !== atelier_langues_current() && atelier_langues_has_translation($id) && !empty($translation['content'])) {
        return $translation['content'];
    }
    return $content;
}, 8);
add_filter('get_the_excerpt', function ($excerpt, $post) {
    $translation = atelier_langues_translation($post->ID);
    if (atelier_langues_frontend() && atelier_langues_source() !== atelier_langues_current() && atelier_langues_has_translation($post->ID) && !empty($translation['excerpt'])) {
        return $translation['excerpt'];
    }
    return $excerpt;
}, 8, 2);

// Remplacer la valeur brute : ACF applique ensuite son formatage habituel.
add_filter('acf/load_value', function ($value, $post_id, $field) {
    if ('atelier_multi' !== $field['type'] || !atelier_langues_frontend() || !is_numeric($post_id)) {
        return $value;
    }
    $language = atelier_langues_current();
    $translated = atelier_langues_source() !== $language && atelier_langues_has_translation($post_id);
    $multi = atelier_langues_multi_decode($value);
    if (null !== $multi) { return $translated && isset($multi[$language]) && '' !== trim($multi[$language]) ? $multi[$language] : ($multi[atelier_langues_source()] ?? ''); }
    if (!$translated) { return $value; }
    $translation = atelier_langues_translation($post_id);
    $english = $translation['acf'][$field['key']] ?? '';
    return is_string($english) && '' !== trim($english) ? $english : $value;
}, 10, 3);

add_filter('document_title_parts', function ($parts) {
    if (is_singular() && atelier_langues_source() !== atelier_langues_current() && atelier_langues_has_translation(get_queried_object_id())) {
        $parts['title'] = atelier_langues_translation(get_queried_object_id())['title'];
    }
    return $parts;
});

/** Avant le rendu du thème : le bouton Créations et les noms des icônes en bénéficient aussi. */
function atelier_langues_menu_label($item) {
    if (atelier_langues_frontend() && atelier_langues_source() !== atelier_langues_current() && 'nav_menu_item' === $item->post_type) {
        $english = atelier_langues_native_text(get_post_meta($item->ID, '_atelier_langues_menu_en', true));
        if (is_string($english) && '' !== trim($english)) {
            // Cloner évite de modifier un objet partagé avec un autre rendu.
            $item = clone $item;
            $item->title = $english;
        }
    }
    return $item;
}
add_filter('wp_setup_nav_menu_item', 'atelier_langues_menu_label');

/** Catalogue limité aux chaînes explicitement localisées dans notre thème. */
function atelier_langues_theme_strings() {
    return array(
        'L’artiste' => 'À propos — surtitre',
        'Démarche artistique' => 'À propos — section',
        'Parcours et expositions' => 'À propos — section',
        'Pour une question sur une œuvre, une exposition ou une collaboration.' => 'Contact — texte de repli',
        'Toutes les créations' => 'Fiche création — lien de retour',
        'Année' => 'Fiche création — information',
        'Technique' => 'Fiche création — information',
        'Dimensions' => 'Fiche création — information',
        'Artiste multidisciplinaire' => 'Accueil — surtitre',
        'Tendre Chaos' => 'Accueil — titre de repli',
        'Image précédente' => 'Carrousel — nom accessible',
        'Image suivante' => 'Carrousel — nom accessible',
        'Aller au contenu' => 'En-tête — lien d’évitement',
        'Navigation principale' => 'En-tête — nom accessible',
        'Accueil' => 'En-tête — lien sans menu',
        'Navigation du pied de page' => 'Pied de page — nom accessible',
        'Visuel à venir' => 'Grille — image absente',
        'Les créations seront présentées prochainement.' => 'Grille — liste vide',
        'Aucun contenu pour le moment.' => 'Boucle générale — liste vide',
        'Ouvrir le sous-menu' => 'Menu — nom accessible',
        'Agrandir l’image' => 'Images — nom accessible du bouton',
        'Fermer l’image' => 'Images — fermeture de l’agrandissement',
    );
}

function atelier_langues_theme_text($translated, $original, $domain) {
    if ('atelier-artiste' !== $domain || !atelier_langues_frontend()) {
        return $translated;
    }
    $catalog = atelier_langues_theme_strings();
    if (!isset($catalog[$original])) {
        return $translated;
    }
    $saved = get_option('atelier_langues_theme_strings', array());
    $entry = is_array($saved) ? ($saved[md5($original)] ?? array()) : array();
    if (is_string($entry)) { $entry = atelier_langues_multi_parts($entry); }
    $language = atelier_langues_current();
    $source = atelier_langues_source();
    $fallback = isset($entry[$source]) && is_string($entry[$source]) && '' !== trim($entry[$source]) ? $entry[$source] : $translated;
    return !empty($entry[$language]) && is_string($entry[$language]) ? $entry[$language] : $fallback;
}
add_filter('gettext', 'atelier_langues_theme_text', 10, 3);

add_filter('single_term_title', function ($name) {
    $term = get_queried_object();
    if (atelier_langues_frontend() && atelier_langues_source() !== atelier_langues_current() && $term instanceof WP_Term && 'discipline' === $term->taxonomy) {
        $english = atelier_langues_native_text(get_term_meta($term->term_id, '_atelier_langues_name_en', true));
        if (is_string($english) && '' !== trim($english)) {
            return $english;
        }
    }
    return $name;
});

add_filter('term_description', function ($description, $term_id, $taxonomy) {
    if ('discipline' === $taxonomy && atelier_langues_frontend() && atelier_langues_source() !== atelier_langues_current()) {
        $english = atelier_langues_native_text(get_term_meta($term_id, '_atelier_langues_description_en', true));
        return '' !== trim($english) ? wpautop(wp_kses_post($english)) : $description;
    }
    return $description;
}, 10, 3);

