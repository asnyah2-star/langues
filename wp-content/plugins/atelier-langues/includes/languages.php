<?php
/** Routage : conserver les règles existantes et leur ajouter deux variantes. */
if (!defined('ABSPATH')) {
    exit;
}

function atelier_langues_current() {
    return 'en' === get_query_var('atelier_lang') ? 'en' : 'fr';
}

add_filter('query_vars', function ($vars) {
    $vars[] = 'atelier_lang';
    return $vars;
});

function atelier_langues_rewrite_rules($rules) {
    $localized = array();
    foreach (array('fr', 'en') as $lang) {
        $front = 'page' === get_option('show_on_front') ? '&page_id=' . (int) get_option('page_on_front') : '';
        $localized[$lang . '/?$'] = 'index.php?atelier_lang=' . $lang . $front;
        foreach ($rules as $regex => $query) {
            // Le préfixe n'ajoute aucun groupe capturant : $matches reste inchangé.
            if (0 === strpos($query, 'index.php?')) {
                $localized[$lang . '/' . ltrim($regex, '^')] = $query . '&atelier_lang=' . $lang;
            }
        }
    }
    return $localized + $rules;
}
add_filter('rewrite_rules_array', 'atelier_langues_rewrite_rules');

/** Localise uniquement les URL publiques du site, y compris en sous-dossier. */
function atelier_langues_url($url, $lang) {
    if (!in_array($lang, array('fr', 'en'), true)) {
        return $url;
    }
    $home = untrailingslashit(get_option('home'));
    if ($url !== $home && 0 !== strpos($url, $home . '/') && 0 !== strpos($url, $home . '?')) {
        return $url;
    }
    $relative = ltrim(substr($url, strlen($home)), '/');
    $relative = preg_replace('#^(fr|en)(/|(?=[?\#])|$)#', '', $relative);
    if (preg_match('#^(wp-admin|wp-json|wp-content|wp-includes|wp-login\.php)(/|[?]|$)#', $relative)) {
        return $url;
    }
    return $home . '/' . $lang . '/' . $relative;
}

/** Les liens vers une fiche non traduite doivent annoncer sa vraie langue. */
function atelier_langues_content_url($url) {
    if (!atelier_langues_frontend() || '' === $url || '#' === substr($url, 0, 1)) {
        return $url;
    }
    $home = untrailingslashit(get_option('home'));
    $plain = preg_replace('#^' . preg_quote($home, '#') . '/(?:fr|en)(?:/|$)#', $home . '/', $url);
    if (0 !== strpos($plain, $home . '/') && $plain !== $home) {
        return $url;
    }
    $id = url_to_postid($plain);
    $lang = atelier_langues_current();
    if ($id && 'en' === $lang && !atelier_langues_has_translation($id)) {
        $lang = 'fr';
    }
    return atelier_langues_url($url, $lang);
}

add_filter('nav_menu_link_attributes', function ($attributes) {
    if (isset($attributes['href'])) {
        $attributes['href'] = atelier_langues_content_url($attributes['href']);
    }
    return $attributes;
}, 20);
function atelier_langues_content_links($html) {
    if (!atelier_langues_frontend()) {
        return $html;
    }
    $processor = new WP_HTML_Tag_Processor($html);
    while ($processor->next_tag('A')) {
        $url = $processor->get_attribute('href');
        if (is_string($url)) {
            $processor->set_attribute('href', atelier_langues_content_url($url));
        }
    }
    return $processor->get_updated_html();
}
add_filter('the_content', 'atelier_langues_content_links', 20);
add_filter('get_custom_logo', 'atelier_langues_content_links');

function atelier_langues_post_url($url, $post) {
    if (!atelier_langues_frontend()) {
        return $url;
    }
    $id = is_object($post) ? $post->ID : (int) $post;
    $lang = atelier_langues_current();
    if ('en' === $lang && !atelier_langues_has_translation($id)) {
        $lang = 'fr';
    }
    return atelier_langues_url($url, $lang);
}
add_filter('page_link', 'atelier_langues_post_url', 10, 2);
add_filter('post_link', 'atelier_langues_post_url', 10, 2);
add_filter('post_type_link', 'atelier_langues_post_url', 10, 2);
add_filter('term_link', function ($url) {
    return atelier_langues_frontend() ? atelier_langues_url($url, atelier_langues_current()) : $url;
});

/** Respecte les pondérations q de l'en-tête, sans deviner une langue inconnue. */
function atelier_langues_browser_language($header) {
    $best = 'fr';
    $weight = 0;
    foreach (explode(',', strtolower($header)) as $entry) {
        if (preg_match('/^\s*(fr|en)(?:-[a-z0-9-]+)?\s*(?:;\s*q=(0(?:\.\d+)?|1(?:\.0+)?))?\s*$/', $entry, $match)) {
            $quality = isset($match[2]) ? (float) $match[2] : 1;
            if ($quality > $weight) {
                $best = $match[1];
                $weight = $quality;
            }
        }
    }
    return $best;
}

function atelier_langues_request_url() {
    $home = wp_parse_url(get_option('home'));
    $origin = $home['scheme'] . '://' . $home['host'] . (isset($home['port']) ? ':' . $home['port'] : '');
    return $origin . wp_unslash($_SERVER['REQUEST_URI'] ?? '/');
}

add_action('template_redirect', function () {
    if (!atelier_langues_frontend() || is_feed() || is_preview() || is_404() || is_trackback() || is_embed() || !in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', array('GET', 'HEAD'), true)) {
        return;
    }
    $explicit = get_query_var('atelier_lang');
    $url = atelier_langues_request_url();
    if (in_array($explicit, array('fr', 'en'), true)) {
        if (isset($_GET['atelier_choice']) && '1' === $_GET['atelier_choice']) {
            setcookie('atelier_langues_choice', $explicit, array('expires' => time() + 180 * DAY_IN_SECONDS, 'path' => wp_parse_url(home_url('/'), PHP_URL_PATH) ?: '/', 'secure' => is_ssl(), 'httponly' => true, 'samesite' => 'Lax'));
            nocache_headers();
            wp_safe_redirect(remove_query_arg('atelier_choice', $url), 302);
            exit;
        }
        if ('en' === $explicit && is_singular() && !atelier_langues_has_translation(get_queried_object_id())) {
            nocache_headers();
            wp_safe_redirect(atelier_langues_url($url, 'fr'), 302);
            exit;
        }
        return;
    }
    // Option activable après les premiers tests : pas de redirection surprise.
    if (!get_option('atelier_langues_detect_browser')) {
        return;
    }
    $choice = $_COOKIE['atelier_langues_choice'] ?? '';
    $lang = in_array($choice, array('fr', 'en'), true) ? $choice : atelier_langues_browser_language($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
    if ('en' === $lang && is_singular() && !atelier_langues_has_translation(get_queried_object_id())) {
        $lang = 'fr';
    }
    nocache_headers();
    header('Vary: Accept-Language, Cookie', false);
    wp_safe_redirect(atelier_langues_url($url, $lang), 302);
    exit;
}, 1);

// WordPress ne doit pas enlever le préfixe ou deviner une destination pour une 404.
add_filter('redirect_canonical', function ($redirect) {
    return get_query_var('atelier_lang') ? false : $redirect;
});
add_filter('language_attributes', function ($attributes) {
    return atelier_langues_frontend() ? preg_replace('/\blang="[^"]*"/', 'lang="' . atelier_langues_current() . '"', $attributes) : $attributes;
});
add_filter('get_canonical_url', function ($url, $post) {
    return atelier_langues_post_url($url, $post);
}, 10, 2);

add_action('wp_head', function () {
    if (!is_singular() || is_404()) {
        return;
    }
    $id = get_queried_object_id();
    if (atelier_langues_has_translation($id)) {
        foreach (array('fr', 'en') as $lang) {
            echo '<link rel="alternate" hreflang="' . esc_attr($lang) . '" href="' . esc_url(atelier_langues_url(get_permalink($id), $lang)) . '" />' . "\n";
        }
    }
});

/** Liens HTML utilisables au clavier ; pas de JavaScript nécessaire. */
function atelier_langues_switcher() {
    if (is_404() || (!is_singular() && !is_tax('discipline'))) {
        return;
    }
    $id = get_queried_object_id();
    $discipline = is_tax('discipline');
    $available = $discipline ? (bool) trim((string) get_term_meta($id, '_atelier_langues_name_en', true)) : atelier_langues_has_translation($id);
    $base = $discipline ? atelier_langues_request_url() : get_permalink($id);
    $base = remove_query_arg(array('atelier_lang', 'atelier_choice'), $base);
    echo '<div class="atelier-langues-switcher" role="group" aria-label="Langues / Languages">';
    foreach (array('fr' => 'Français', 'en' => 'English') as $lang => $label) {
        if ('en' === $lang && !$available) {
            continue;
        }
        $url = add_query_arg('atelier_choice', '1', atelier_langues_url($base, $lang));
        echo '<a href="' . esc_url($url) . '" lang="' . esc_attr($lang) . '" hreflang="' . esc_attr($lang) . '" aria-label="' . esc_attr($label) . '"' . ($lang === atelier_langues_current() ? ' aria-current="page"' : '') . '>' . esc_html(strtoupper($lang)) . '</a> ';
    }
    echo '</div>';
}
