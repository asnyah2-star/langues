<?php
function atelier_artiste_setup() {
    load_theme_textdomain('atelier-artiste', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    // Le logo se choisit dans Apparence > Personnaliser > Identité du site.
    add_theme_support('custom-logo', array(
        'height' => 100,
        'width' => 240,
        'flex-height' => true,
        'flex-width' => true,
    ));
    add_theme_support('post-thumbnails');
    register_nav_menus(array('principal' => 'Navigation principale', 'social' => 'Icônes après Contact', 'footer' => 'Navigation du pied de page'));
}
add_action('after_setup_theme', 'atelier_artiste_setup');
function atelier_artiste_enqueue_styles() {
    wp_enqueue_style('atelier-artiste', get_stylesheet_uri(), array(), (string) filemtime(get_stylesheet_directory() . '/style.css'));
}
add_action('wp_enqueue_scripts', 'atelier_artiste_enqueue_styles');

// Une fiche par œuvre : titre, description, image et informations ACF.
function atelier_artiste_register_works() {
    register_post_type('oeuvre', array(
        'labels' => array('name' => 'Créations', 'singular_name' => 'Création', 'add_new_item' => 'Ajouter une création', 'edit_item' => 'Modifier la création'),
        'public' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-art',
        'supports' => array('title', 'editor', 'thumbnail'),
        'rewrite' => array('slug' => 'oeuvre'),
        'has_archive' => false,
    ));
    register_taxonomy('discipline', 'oeuvre', array(
        'labels' => array('name' => 'Disciplines', 'singular_name' => 'Discipline'),
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => array('slug' => 'discipline'),
    ));
}
add_action('init', 'atelier_artiste_register_works');

function atelier_artiste_discipline_query($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_tax('discipline')) {
        $query->set('post_type', 'oeuvre');
        $query->set('posts_per_page', 12);
    }
}
add_action('pre_get_posts', 'atelier_artiste_discipline_query');

// Retrouver une page sans écrire son URL en dur dans les templates.
function atelier_artiste_page_url($slug) {
    $page = get_page_by_path($slug);
    return $page && 'publish' === $page->post_status ? get_permalink($page) : home_url('/');
}

function atelier_artiste_field($name, $post_id = false) {
    return function_exists('get_field') ? get_field($name, $post_id) : '';
}

// Le sélecteur ACF du carrousel montre uniquement les images de la médiathèque.
function atelier_artiste_carousel_query($args) {
    $args['post_status'] = 'inherit';
    $args['post_mime_type'] = 'image';
    return $args;
}
add_filter('acf/fields/relationship/query/name=accueil_carrousel', 'atelier_artiste_carousel_query');

function atelier_artiste_enqueue_scripts() {
    wp_enqueue_script('atelier-navigation', get_theme_file_uri('/navigation.js'), array(), (string) filemtime(get_theme_file_path('/navigation.js')), true);
    if (is_front_page()) {
        wp_enqueue_script('atelier-carousel', get_theme_file_uri('/carousel.js'), array(), (string) filemtime(get_theme_file_path('/carousel.js')), true);
    }
    if (is_singular('oeuvre')) {
        wp_enqueue_script('atelier-images', get_theme_file_uri('/images.js'), array(), (string) filemtime(get_theme_file_path('/images.js')), true);
    }
}
add_action('wp_enqueue_scripts', 'atelier_artiste_enqueue_scripts');

// Créations sert uniquement à ouvrir ses disciplines, sans changer de page.
function atelier_artiste_submenu_toggle($output, $item, $depth, $args) {
    if ('principal' === $args->theme_location && in_array('menu-item-has-children', $item->classes, true)) {
        if ('page' === $item->object && 'oeuvres' === get_post_field('post_name', $item->object_id)) {
            return preg_replace_callback('/<a\b[^>]*>.*?<\/a>/s', function () use ($item) {
                return '<button class="submenu-toggle submenu-parent" type="button" aria-expanded="false">' . esc_html($item->title) . ' <span aria-hidden="true">▾</span></button>';
            }, $output, 1);
        }
        $output .= '<button class="submenu-toggle" type="button" aria-expanded="false" aria-label="' . esc_attr(__('Ouvrir le sous-menu', 'atelier-artiste') . ' : ' . wp_strip_all_tags($item->title)) . '"><span aria-hidden="true">▾</span></button>';
    }
    return $output;
}
add_filter('walker_nav_menu_start_el', 'atelier_artiste_submenu_toggle', 10, 4);

// Garder le repère de rubrique sur les disciplines et les fiches des œuvres.
add_filter('nav_menu_css_class', function ($classes, $item, $args) {
    if ('principal' === ($args->theme_location ?? '') && 'page' === $item->object
        && 'oeuvres' === get_post_field('post_name', $item->object_id)
        && (is_tax('discipline') || is_singular('oeuvre'))) {
        $classes[] = 'current-menu-ancestor';
    }
    return array_unique($classes);
}, 10, 3);

// L'arobase visible vient du CSS ; le lien reste utilisable sans JavaScript.
function atelier_artiste_email_link($email) {
    if (!is_email($email)) { return ''; }
    list($name, $domain) = explode('@', $email, 2);
    return '<a class="text-link contact-email" href="mailto:' . antispambot(esc_attr($email), 1) . '" aria-label="' . antispambot(esc_attr($email)) . '"><span aria-hidden="true">'
        . esc_html($name) . '<span class="email-domain">' . esc_html($domain) . '</span></span></a>';
}

// L'ancienne liste générale n'est plus une destination du site.
add_action('template_redirect', function () {
    if (is_page('oeuvres') && !is_preview()) {
        $url = home_url('/');
        if (function_exists('atelier_langues_content_url')) {
            $url = atelier_langues_content_url($url);
        }
        wp_safe_redirect($url, 302);
        exit;
    }
}, 0);

// Les destinations restent éditables dans Apparence > Menus.
function atelier_artiste_social_icon($title, $item, $args) {
    if ('social' !== $args->theme_location) {
        return $title;
    }
    $icons = array(
        'icon-email' => 'envelope-regular-full.svg',
        'icon-instagram' => 'instagram-brands-solid-full.svg',
        'icon-linkedin' => 'linkedin-brands-solid-full.svg',
        'icon-soundcloud' => 'soundcloud-brands-solid-full.svg',
        'icon-spotify' => 'spotify-brands-solid-full.svg',
        'icon-youtube' => 'youtube-brands-solid-full.svg',
        'icon-disc' => 'compact-disc-solid-full.svg',
    );
    foreach ($icons as $class => $file) {
        if (in_array($class, $item->classes, true)) {
            $uploads = wp_upload_dir();
            $icon_url = esc_url($uploads['baseurl'] . '/2026/icons/' . $file);
            return '<span class="social-icon" aria-hidden="true" style="--icon: url(' . esc_attr('"' . $icon_url . '"') . ')"></span><span class="social-label">' . esc_html($title) . '</span>';
        }
    }
    return $title;
}
add_filter('nav_menu_item_title', 'atelier_artiste_social_icon', 10, 3);

// Les sites externes s’ouvrent à part ; les liens internes et e-mail restent inchangés.
function atelier_artiste_is_external_url($url) {
    $host = wp_parse_url($url, PHP_URL_HOST);
    $scheme = wp_parse_url($url, PHP_URL_SCHEME);
    return $host && in_array($scheme, array('http', 'https'), true)
        && strtolower($host) !== strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST));
}

function atelier_artiste_external_menu_links($attributes) {
    if (atelier_artiste_is_external_url($attributes['href'] ?? '')) {
        $attributes['target'] = '_blank';
        $relations = preg_split('/\s+/', trim($attributes['rel'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
        $attributes['rel'] = implode(' ', array_unique(array_merge($relations, array('noopener', 'noreferrer'))));
    }
    return $attributes;
}
add_filter('nav_menu_link_attributes', 'atelier_artiste_external_menu_links');

function atelier_artiste_external_content_links($content) {
    $html = new WP_HTML_Tag_Processor($content);
    while ($html->next_tag('A')) {
        if (atelier_artiste_is_external_url($html->get_attribute('href') ?? '')) {
            $html->set_attribute('target', '_blank');
            $relations = preg_split('/\s+/', trim($html->get_attribute('rel') ?? ''), -1, PREG_SPLIT_NO_EMPTY);
            $html->set_attribute('rel', implode(' ', array_unique(array_merge($relations, array('noopener', 'noreferrer')))));
        }
    }
    return $html->get_updated_html();
}
add_filter('the_content', 'atelier_artiste_external_content_links');

