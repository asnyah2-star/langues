<?php
if (!defined('ABSPATH')) {
    exit;
}

// Aider à publier sans créer une deuxième série de champs.
add_action('add_meta_boxes_oeuvre', function () {
    add_meta_box('atelier-creation-guide', 'Préparer cette création', function ($post) {
        echo '<ol><li><strong>Titre :</strong> nom de l’œuvre, saisi en haut de la fiche.</li><li><strong>Description :</strong> rédigez le texte dans l’éditeur principal. Utilisez ses blocs Image, Galerie, Audio ou Vidéo si nécessaire.</li><li><strong>Image mise en avant :</strong> choisissez le visuel principal dans les réglages de la création. Il représente aussi l’œuvre dans les listes.</li><li><strong>Disciplines :</strong> sélectionnez la ou les disciplines pertinentes pour que l’œuvre apparaisse dans les bonnes listes.</li><li><strong>Informations complémentaires :</strong> année, technique et dimensions sont facultatives. Les champs vides ne s’affichent pas.</li></ol>';
        echo '<p>Enregistrez d’abord un brouillon et vérifiez l’aperçu avant de publier. Pour une galerie, choisissez les images dans la médiathèque ; inutile de créer une fiche par image.</p>';
        echo '<p><a href="' . esc_url(add_query_arg(array('page' => 'atelier-langues', 'content_id' => $post->ID), admin_url('options-general.php'))) . '">Ouvrir la traduction de cette création</a> — après avoir enregistré le français. Déclarez l’anglais disponible seulement après relecture.</p>';
    }, 'oeuvre', 'normal', 'high');
});

add_filter('enter_title_here', function ($placeholder, $post) {
    return 'oeuvre' === $post->post_type ? 'Nom de l’œuvre' : $placeholder;
}, 10, 2);

foreach (array('oeuvre_annee' => 'Facultatif. Année de création, commune aux deux langues.', 'oeuvre_technique' => 'Facultatif. Matériaux ou technique ; traduction dans Atelier Langues.', 'oeuvre_dimensions' => 'Facultatif. Indiquez les unités, par exemple 30 × 40 cm.') as $field_name => $help) {
    add_filter('acf/prepare_field/name=' . $field_name, function ($field) use ($help) {
        $field['instructions'] = $help;
        return $field;
    });
}

function atelier_langues_add_admin_menu() {
    add_options_page(
        'Atelier Langues',
        'Atelier Langues',
        'manage_options',
        'atelier-langues',
        'atelier_langues_render_admin_page'
    );
}
add_action('admin_menu', 'atelier_langues_add_admin_menu');

function atelier_langues_render_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $post_id = isset($_GET['content_id']) ? absint($_GET['content_id']) : (int) get_option('page_on_front');
    $post = get_post($post_id);
    echo '<div class="wrap"><h1>Atelier Langues</h1>';
    echo '<p>Saisissez vos traductions manuellement. Une valeur anglaise vide conserve la valeur française. Les médias restent partagés.</p>';
    $front_id = (int) get_option('page_on_front');
    $contact = get_page_by_path('contact');
    echo '<p>Accès directs : ';
    if ($contact) {
        echo '<a href="' . esc_url(add_query_arg(array('page' => 'atelier-langues', 'content_id' => $contact->ID), admin_url('options-general.php'))) . '">Textes de Contact</a> · ';
    }
    if ($front_id) {
        echo '<a href="' . esc_url(add_query_arg(array('page' => 'atelier-langues', 'content_id' => $front_id), admin_url('options-general.php'))) . '">Pied de page (champs de l’accueil)</a> · ';
    }
    echo '<a href="#theme-strings">Textes fixes du thème</a></p>';
    if (isset($_GET['saved'])) {
        echo '<div class="notice notice-success"><p>Traductions enregistrées.</p></div>';
    }
    echo '<form method="get"><input type="hidden" name="page" value="atelier-langues"><label for="atelier-content">Contenu à traduire </label><select id="atelier-content" name="content_id">';
    foreach (get_posts(array('post_type' => atelier_langues_post_types(), 'post_status' => array('publish', 'draft', 'private', 'pending', 'future'), 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC')) as $item) {
        if (current_user_can('edit_post', $item->ID)) {
            echo '<option value="' . esc_attr($item->ID) . '" ' . selected($post_id, $item->ID, false) . '>' . esc_html($item->post_title . ' (' . $item->post_type . ')') . '</option>';
        }
    }
    echo '</select> ';
    submit_button('Ouvrir', 'secondary', 'open', false);
    echo '</form>';
    if ($post && in_array($post->post_type, atelier_langues_post_types(), true) && current_user_can('edit_post', $post_id)) {
        $english = atelier_langues_translation($post_id);
        if (atelier_langues_has_translation($post_id)) {
            echo '<p><strong>Statut : version anglaise disponible.</strong> Le sélecteur du site propose Français et English.</p>';
        } else {
            echo '<p><strong>Statut : version anglaise non disponible.</strong> Renseignez le titre anglais, puis cochez « Version anglaise relue et disponible » et enregistrez pour afficher le lien English sur le site.</p>';
        }
        if ($post_id === (int) get_option('page_on_front')) {
            echo '<p>Sur l’accueil, le grand titre visible vient du champ « Accroche ». Le champ « Titre » désigne le titre WordPress de la page ; sa traduction ne remplace pas automatiquement le texte du menu.</p>';
            echo '<p><strong>Pied de page :</strong> le champ de texte du footer ci-dessous est commun à toutes les pages. Sa traduction anglaise est utilisée si la version anglaise de l’accueil est déclarée disponible. L’année et le nom du site restent communs.</p>';
        }
        if ($contact && $post_id === (int) $contact->ID) {
            echo '<p><strong>Contact :</strong> traduisez le champ Introduction ci-dessous, puis le Contenu si vous y avez ajouté du texte. Si Introduction est vide, la phrase de repli se règle dans <a href="#theme-strings">Textes du thème → Contact — texte de repli</a>. Les coordonnées et les liens sociaux se modifient dans <a href="' . esc_url(admin_url('admin.php?page=atelier-site')) . '">Réglages du site</a>.</p>';
        }
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="atelier_langues_save"><input type="hidden" name="content_id" value="' . esc_attr($post_id) . '">';
        echo '<input type="hidden" name="source_modified" value="' . esc_attr($post->post_modified_gmt) . '">';
        wp_nonce_field('atelier_langues_save_' . $post_id);
        echo '<table class="widefat atelier-langues-table"><caption>Français et anglais — ' . esc_html($post->post_title) . '</caption><thead><tr><th scope="col">Champ</th><th scope="col">Français</th><th scope="col">Anglais</th></tr></thead><tbody>';
        atelier_langues_admin_row('Titre', 'title', $post->post_title, $english['title'] ?? '', 'text');
        atelier_langues_admin_row('Contenu (HTML ou blocs WordPress)', 'content', $post->post_content, $english['content'] ?? '', 'wysiwyg');
        atelier_langues_admin_row('Extrait', 'excerpt', $post->post_excerpt, $english['excerpt'] ?? '', 'textarea');
        foreach (atelier_langues_acf_fields($post_id) as $key => $field) {
            atelier_langues_admin_row($field['label'], 'acf][' . $key, (string) get_field($key, $post_id, false), $english['acf'][$key] ?? '', $field['type']);
        }
        echo '</tbody></table><p><label><input type="checkbox" name="english[ready]" value="1" ' . checked(!empty($english['ready']), true, false) . '> Version anglaise relue et disponible (titre anglais obligatoire)</label></p>';
        echo '<p>Les éditeurs ci-dessus acceptent le HTML. Les champs français existants sont mis à jour : évitez de modifier la même fiche dans un autre onglet en parallèle.</p>';
        submit_button('Enregistrer les deux langues');
        echo '</form>';
        if ('publish' === $post->post_status) {
            echo '<p><a href="' . esc_url(atelier_langues_url(get_permalink($post_id), 'fr')) . '">Voir en français</a>';
            if (atelier_langues_has_translation($post_id)) {
                echo ' · <a href="' . esc_url(atelier_langues_url(get_permalink($post_id), 'en')) . '">Voir en anglais</a>';
            }
            echo '</p>';
        }
    }
    echo '<hr><h2>Détection de la langue</h2><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="atelier_langues_settings">';
    wp_nonce_field('atelier_langues_settings');
    echo '<label><input type="checkbox" name="detect_browser" value="1" ' . checked((bool) get_option('atelier_langues_detect_browser'), true, false) . '> Rediriger les URL sans préfixe selon le choix mémorisé ou la langue du navigateur</label><p>Activez après avoir vérifié /fr/ et /en/. Les URL explicitement préfixées restent prioritaires. Un cache de pages devra exclure les URL sans préfixe.</p>';
    submit_button('Enregistrer ce réglage', 'secondary');
    echo '</form>';
    atelier_langues_theme_form();
    echo '</div>';
}

function atelier_langues_theme_form() {
    $saved = get_option('atelier_langues_theme_strings', array());
    echo '<hr><h2 id="theme-strings">Textes du thème</h2><p>Ces petits libellés sont partagés entre les pages. Un anglais vide conserve le français. Saisissez du texte simple, sans HTML.</p>';
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="atelier_langues_theme_strings">';
    wp_nonce_field('atelier_langues_theme_strings');
    echo '<table class="widefat atelier-langues-table"><caption>Libellés du thème Atelier Artiste</caption><thead><tr><th scope="col">Emplacement</th><th scope="col">Français</th><th scope="col">Anglais</th></tr></thead><tbody>';
    foreach (atelier_langues_theme_strings() as $original => $location) {
        $key = md5($original);
        atelier_langues_admin_row($location, 'theme][' . $key, $saved[$key]['fr'] ?? $original, $saved[$key]['en'] ?? '', 'text');
    }
    echo '</tbody></table>';
    submit_button('Enregistrer les textes du thème');
    echo '</form>';
}

add_action('admin_post_atelier_langues_theme_strings', function () {
    if (!current_user_can('manage_options')) {
        wp_die('Accès refusé.', '', array('response' => 403));
    }
    check_admin_referer('atelier_langues_theme_strings');
    $fr = $_POST['french']['theme'] ?? array();
    $en = $_POST['english']['theme'] ?? array();
    if (!is_array($fr) || !is_array($en)) {
        wp_die('Formulaire invalide.', '', array('response' => 400));
    }
    $saved = get_option('atelier_langues_theme_strings', array());
    $saved = is_array($saved) ? $saved : array();
    foreach (atelier_langues_theme_strings() as $original => $location) {
        $key = md5($original);
        // Un formulaire tronqué ne doit pas effacer les valeurs absentes.
        if (isset($fr[$key], $en[$key]) && is_string($fr[$key]) && is_string($en[$key])) {
            $saved[$key] = array('fr' => sanitize_text_field(wp_unslash($fr[$key])), 'en' => sanitize_text_field(wp_unslash($en[$key])));
        }
    }
    update_option('atelier_langues_theme_strings', $saved);
    wp_safe_redirect(admin_url('options-general.php?page=atelier-langues&saved=1#theme-strings'));
    exit;
});

/** Des libellés distincts et des colonnes adaptables, sans script nécessaire. */
function atelier_langues_admin_row($label, $key, $french, $english, $type) {
    echo '<tr><th scope="row">' . esc_html($label) . '</th>';
    foreach (array('french' => $french, 'english' => $english) as $lang => $value) {
        $name = $lang . '[' . $key . ']';
        $id = sanitize_html_class($lang . '-' . str_replace('][', '-', $key));
        echo '<td><label class="screen-reader-text" for="' . esc_attr($id) . '">' . esc_html($label . ('french' === $lang ? ' français' : ' anglais')) . '</label>';
        if (in_array($type, array('textarea', 'wysiwyg'), true)) {
            echo '<textarea class="large-text" rows="8" id="' . esc_attr($id) . '" name="' . esc_attr($name) . '">' . esc_textarea($value) . '</textarea>';
        } else {
            echo '<input class="large-text" type="' . ('url' === $type ? 'url' : 'text') . '" id="' . esc_attr($id) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '">';
        }
        echo '</td>';
    }
    echo '</tr>';
}

function atelier_langues_clean_value($value, $type) {
    if (!is_string($value)) {
        return '';
    }
    if ('wysiwyg' === $type) {
        return wp_kses_post($value);
    }
    if ('url' === $type) {
        return esc_url_raw($value);
    }
    return 'textarea' === $type ? sanitize_textarea_field($value) : sanitize_text_field($value);
}

add_action('admin_post_atelier_langues_save', function () {
    $id = isset($_POST['content_id']) ? absint($_POST['content_id']) : 0;
    $post = get_post($id);
    if (!current_user_can('manage_options') || !current_user_can('edit_post', $id) || !$post || !in_array($post->post_type, atelier_langues_post_types(), true)) {
        wp_die('Accès refusé.', '', array('response' => 403));
    }
    check_admin_referer('atelier_langues_save_' . $id);
    if (!isset($_POST['source_modified']) || $post->post_modified_gmt !== wp_unslash($_POST['source_modified'])) {
        wp_die('Le contenu a changé dans un autre onglet. Rechargez la page avant de réessayer.', '', array('response' => 409));
    }
    $fr = isset($_POST['french']) && is_array($_POST['french']) ? wp_unslash($_POST['french']) : array();
    $en = isset($_POST['english']) && is_array($_POST['english']) ? wp_unslash($_POST['english']) : array();
    if (!isset($fr['title'], $fr['content'], $fr['excerpt'], $en['title'], $en['content'], $en['excerpt'])) {
        wp_die('Formulaire incomplet : aucune modification enregistrée.', '', array('response' => 400));
    }
    $english = array();
    $update = array('ID' => $id);
    foreach (array('title' => 'text', 'content' => 'wysiwyg', 'excerpt' => 'textarea') as $key => $type) {
        $update['post_' . $key] = atelier_langues_clean_value($fr[$key], $type);
        $english[$key] = atelier_langues_clean_value($en[$key], $type);
    }
    $result = wp_update_post(wp_slash($update), true);
    if (is_wp_error($result)) {
        wp_die(esc_html($result->get_error_message()));
    }
    $english['ready'] = !empty($en['ready']) && '' !== $english['title'];
    foreach (atelier_langues_acf_fields($id) as $key => $field) {
        if (isset($fr['acf'][$key])) {
            update_field($key, atelier_langues_clean_value($fr['acf'][$key], $field['type']), $id);
        }
        if (isset($en['acf'][$key])) {
            $english['acf'][$key] = atelier_langues_clean_value($en['acf'][$key], $field['type']);
        }
    }
    update_post_meta($id, '_atelier_langues_en', wp_slash($english));
    wp_safe_redirect(add_query_arg(array('page' => 'atelier-langues', 'content_id' => $id, 'saved' => 1), admin_url('options-general.php')));
    exit;
});

add_action('admin_post_atelier_langues_settings', function () {
    if (!current_user_can('manage_options')) {
        wp_die('Accès refusé.', '', array('response' => 403));
    }
    check_admin_referer('atelier_langues_settings');
    update_option('atelier_langues_detect_browser', !empty($_POST['detect_browser']));
    wp_safe_redirect(add_query_arg(array('page' => 'atelier-langues', 'saved' => 1), admin_url('options-general.php')));
    exit;
});

add_action('admin_enqueue_scripts', function ($hook) {
    if ('settings_page_atelier-langues' === $hook) {
        wp_enqueue_style('atelier-langues-admin', plugins_url('../assets/admin.css', __FILE__), array(), '0.2.0');
    }
});

/** Le libellé français reste celui de WordPress ; seul l'anglais est ajouté. */
function atelier_langues_menu_field($item_id) {
    $english = get_post_meta($item_id, '_atelier_langues_menu_en', true);
    $english = is_string($english) ? $english : '';
    echo '<p class="description description-wide">';
    echo '<label for="atelier-menu-en-' . esc_attr($item_id) . '">Libellé anglais (Atelier Langues)<br>';
    echo '<input type="text" class="widefat" id="atelier-menu-en-' . esc_attr($item_id) . '" name="atelier_menu_en[' . esc_attr($item_id) . ']" value="' . esc_attr($english) . '"></label>';
    echo '<span class="description">Vide : le libellé français est conservé. Les icônes et les destinations restent partagées.</span>';
    wp_nonce_field('atelier_menu_' . $item_id, 'atelier_menu_nonce[' . $item_id . ']', false);
    echo '</p>';
}
add_action('wp_nav_menu_item_custom_fields', 'atelier_langues_menu_field');

function atelier_langues_save_menu_field($menu_id, $item_id) {
    // Ne rien modifier lors d'une sauvegarde sans notre formulaire (API, import…).
    if (!current_user_can('edit_theme_options') || 'nav_menu_item' !== get_post_type($item_id)) {
        return;
    }
    $nonce = $_POST['atelier_menu_nonce'][$item_id] ?? '';
    $value = $_POST['atelier_menu_en'][$item_id] ?? null;
    if (!is_string($nonce) || !wp_verify_nonce(wp_unslash($nonce), 'atelier_menu_' . $item_id) || !is_string($value)) {
        return;
    }
    update_post_meta($item_id, '_atelier_langues_menu_en', wp_slash(sanitize_text_field(wp_unslash($value))));
}
add_action('wp_update_nav_menu_item', 'atelier_langues_save_menu_field', 10, 2);

/** Une seule valeur supplémentaire sur la discipline existante. */
add_action('discipline_edit_form_fields', function ($term) {
    $value = get_term_meta($term->term_id, '_atelier_langues_name_en', true);
    echo '<tr class="form-field"><th scope="row"><label for="atelier-discipline-en">Nom anglais</label></th><td>';
    echo '<input type="text" id="atelier-discipline-en" name="atelier_discipline_en" value="' . esc_attr(is_string($value) ? $value : '') . '">';
    echo '<p class="description">Vide : le nom français est conservé. Le slug et les créations associées restent inchangés.</p>';
    wp_nonce_field('atelier_discipline_' . $term->term_id, 'atelier_discipline_nonce');
    echo '</td></tr>';
});

add_action('edited_discipline', function ($term_id) {
    $value = $_POST['atelier_discipline_en'] ?? null;
    $nonce = $_POST['atelier_discipline_nonce'] ?? '';
    if (!current_user_can('edit_term', $term_id) || !is_string($value) || !is_string($nonce) || !wp_verify_nonce(wp_unslash($nonce), 'atelier_discipline_' . $term_id)) {
        return;
    }
    update_term_meta($term_id, '_atelier_langues_name_en', wp_slash(sanitize_text_field(wp_unslash($value))));
});
