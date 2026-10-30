<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('add_meta_boxes', function () {
    foreach (atelier_langues_post_types() as $type) {
        add_meta_box('langues-status', 'Langues', function ($post) {
            foreach (atelier_langues_languages() as $lang => $label) {
                if (atelier_langues_source() !== $lang) { echo '<p>' . esc_html($label . (atelier_langues_has_translation($post->ID, $lang) ? ' : disponible.' : ' : à préparer ou à relire.')) . '</p>'; }
            }
            echo '<p>Traduisez les champs ACF dans leurs onglets ci-dessous.</p>';
            if (current_user_can('manage_options')) {
                echo '<p><a href="' . esc_url(add_query_arg(array('page' => 'atelier-langues', 'content_id' => $post->ID), admin_url('options-general.php'))) . '">Titre, contenu principal et langues disponibles</a></p>';
            }
        }, $type, 'side');
    }
});

// Aider à publier sans créer une deuxième série de champs.
add_action('add_meta_boxes_oeuvre', function () {
    add_meta_box('atelier-creation-guide', 'Préparer cette création', function ($post) {
        echo '<ol><li><strong>Titre :</strong> nom de l’œuvre, saisi en haut de la fiche.</li><li><strong>Description :</strong> rédigez le texte dans l’éditeur principal. Utilisez ses blocs Image, Galerie, Audio ou Vidéo si nécessaire.</li><li><strong>Image mise en avant :</strong> choisissez le visuel principal dans les réglages de la création. Il représente aussi l’œuvre dans les listes.</li><li><strong>Disciplines :</strong> sélectionnez la ou les disciplines pertinentes pour que l’œuvre apparaisse dans les bonnes listes.</li><li><strong>Informations complémentaires :</strong> année, technique et dimensions sont facultatives. Les champs vides ne s’affichent pas.</li></ol>';
        echo '<p>Enregistrez d’abord un brouillon et vérifiez l’aperçu avant de publier. Pour une galerie, choisissez les images dans la médiathèque ; inutile de créer une fiche par image.</p>';
        echo '<p><a href="' . esc_url(add_query_arg(array('page' => 'atelier-langues', 'content_id' => $post->ID), admin_url('options-general.php'))) . '">Ouvrir les traductions de cette création</a> — après avoir enregistré le contenu source. Déclarez chaque traduction disponible seulement après relecture.</p>';
    }, 'oeuvre', 'normal', 'high');
});

add_filter('enter_title_here', function ($placeholder, $post) {
    return 'oeuvre' === $post->post_type ? 'Nom de l’œuvre' : $placeholder;
}, 10, 2);

foreach (array('oeuvre_annee' => 'Facultatif. Année de création, commune aux deux langues.', 'oeuvre_technique' => 'Facultatif. Matériaux ou technique ; utilisez les onglets FR/EN.', 'oeuvre_dimensions' => 'Facultatif. Indiquez les unités, par exemple 30 × 40 cm.') as $field_name => $help) {
    add_filter('acf/prepare_field/name=' . $field_name, function ($field) use ($help) {
        $field['instructions'] = $help;
        return $field;
    });
}

function atelier_langues_add_admin_menu() {
    add_options_page(
        'Langues',
        'Langues',
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
    echo '<div class="wrap"><h1>Langues</h1>';
    echo '<p>Saisissez vos traductions manuellement. Un texte vide reprend la langue source. Les médias restent partagés.</p>';
    $front_id = (int) get_option('page_on_front');
    $contact = get_page_by_path('contact');
    echo '<p>Accès directs : ';
    if ($contact) {
        echo '<a href="' . esc_url(get_edit_post_link($contact->ID)) . '">Textes de Contact</a> · ';
    }
    if ($front_id) {
        echo '<a href="' . esc_url(admin_url('admin.php?page=atelier-site')) . '">Pied de page</a> · ';
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
        $translations = array();
        foreach (atelier_langues_languages() as $lang => $label) { $translations[$lang] = atelier_langues_translation($post_id, $lang); }
        echo '<p>Ci-dessous : titre, contenu principal et extrait WordPress. Les textes ACF (accroche, introduction, biographie, technique…) se traduisent directement dans la fiche. Le pied de page est aussi accessible dans Réglages du site.</p>';
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="atelier_langues_save"><input type="hidden" name="content_id" value="' . esc_attr($post_id) . '">';
        echo '<input type="hidden" name="source_modified" value="' . esc_attr($post->post_modified_gmt) . '">';
        wp_nonce_field('atelier_langues_save_' . $post_id);
        echo '<table class="widefat atelier-langues-table"><caption>' . esc_html($post->post_title) . '</caption><thead><tr><th scope="col">Champ</th><th scope="col" colspan="2">Texte — choisissez un onglet</th></tr></thead><tbody>';
        foreach (array('title' => array('Titre', 'text'), 'content' => array('Contenu principal', 'wysiwyg'), 'excerpt' => array('Extrait', 'textarea')) as $key => $definition) {
            $parts = array();
            foreach (atelier_langues_languages() as $lang => $label) { $parts[$lang] = atelier_langues_source() === $lang ? $post->{'post_' . $key} : ($translations[$lang][$key] ?? ''); }
            atelier_langues_admin_row($definition[0], $key, $parts, $definition[1]);
        }
        echo '</tbody></table><p><a href="' . esc_url(get_edit_post_link($post_id)) . '">Modifier les champs ACF et leurs traductions directement dans la fiche</a></p>';
        foreach (atelier_langues_languages() as $lang => $label) {
            if (atelier_langues_source() === $lang) { continue; }
            echo '<p><label><input type="checkbox" name="translations[' . esc_attr($lang) . '][ready]" value="1" ' . checked(!empty($translations[$lang]['ready']), true, false) . '> ' . esc_html($label) . ' : traduction relue et disponible (titre traduit requis)</label></p>';
        }
        echo '<p>Les éditeurs ci-dessus acceptent le HTML. Les champs natifs dans la langue source sont mis à jour : évitez de modifier la même fiche dans un autre onglet en parallèle.</p>';
        submit_button('Enregistrer les traductions');
        echo '</form>';
        if ('publish' === $post->post_status) {
            foreach (atelier_langues_languages() as $lang => $label) {
                if (atelier_langues_has_translation($post_id, $lang)) { echo '<p><a href="' . esc_url(atelier_langues_url(get_permalink($post_id), $lang)) . '">Voir : ' . esc_html($label) . '</a></p>'; }
            }
        }
    }
    echo '<hr><h2>Configuration</h2><p>Langues actives : ' . esc_html(implode(', ', atelier_langues_languages())) . '. La liste se définit dans atelier_langues_languages(), dans le fichier principal du plugin. La première est la langue des contenus natifs et sert de secours.</p><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="atelier_langues_settings">';
    wp_nonce_field('atelier_langues_settings');
    echo '<label><input type="checkbox" name="detect_browser" value="1" ' . checked((bool) get_option('atelier_langues_detect_browser', true), true, false) . '> Rediriger les URL sans préfixe selon le choix mémorisé ou la langue du navigateur</label><p>Vérifiez les URL des langues configurées. Les URL explicitement préfixées restent prioritaires. Un cache de pages devra exclure les URL sans préfixe.</p>';
    submit_button('Enregistrer ce réglage', 'secondary');
    echo '</form>';
    atelier_langues_theme_form();
    echo '</div>';
}

function atelier_langues_theme_form() {
    $saved = get_option('atelier_langues_theme_strings', array());
    echo '<hr><h2 id="theme-strings">Textes du thème</h2><p>Ces petits libellés sont partagés entre les pages. Un texte vide reprend la langue source. Saisissez du texte simple, sans HTML.</p>';
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="atelier_langues_theme_strings">';
    wp_nonce_field('atelier_langues_theme_strings');
    echo '<table class="widefat atelier-langues-table"><caption>Libellés du thème Atelier Artiste</caption><thead><tr><th scope="col">Emplacement</th><th scope="col" colspan="2">Texte — choisissez un onglet</th></tr></thead><tbody>';
    foreach (atelier_langues_theme_strings() as $original => $location) {
        $key = md5($original);
        $parts = atelier_langues_multi_parts($saved[$key] ?? $original);
        if ('' === trim($parts[atelier_langues_source()])) { $parts[atelier_langues_source()] = $original; }
        atelier_langues_admin_row($location, 'theme][' . $key, $parts, 'text');
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
    $submitted = $_POST['translations'] ?? array();
    if (!is_array($submitted)) {
        wp_die('Formulaire invalide.', '', array('response' => 400));
    }
    $saved = get_option('atelier_langues_theme_strings', array());
    $saved = is_array($saved) ? $saved : array();
    foreach (atelier_langues_theme_strings() as $original => $location) {
        $key = md5($original);
        // Un formulaire tronqué ne doit pas effacer les valeurs absentes.
        $values = array();
        foreach (atelier_langues_languages() as $lang => $label) { $values[$lang] = $submitted[$lang]['theme'][$key] ?? null; }
        if (atelier_langues_multi_valid($values)) { $saved[$key] = atelier_langues_multi_merge($saved[$key] ?? $original, wp_unslash($values), 'text'); }
    }
    update_option('atelier_langues_theme_strings', $saved);
    wp_safe_redirect(admin_url('options-general.php?page=atelier-langues&saved=1#theme-strings'));
    exit;
});

/** Un composant partagé : une seule langue visible par champ. */
function atelier_langues_admin_row($label, $key, $parts, $type) {
    $names = array();
    foreach (atelier_langues_languages() as $lang => $language_label) { $names[$lang] = 'translations[' . $lang . '][' . $key . ']'; }
    echo '<tr><th scope="row">' . esc_html($label) . '</th><td colspan="2">';
    atelier_langues_multi_editor(atelier_langues_multi_parts($parts), $names, $type, $label);
    echo '</td></tr>';
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

/** Valider toutes les langues avant d'enregistrer le moindre contenu. */
function atelier_langues_save_post_translations($id, $submitted) {
    if (!is_array($submitted)) { return new WP_Error('incomplete', 'Formulaire incomplet.'); }
    $clean = array();
    foreach (atelier_langues_languages() as $lang => $label) {
        foreach (array('title' => 'text', 'content' => 'wysiwyg', 'excerpt' => 'textarea') as $key => $type) {
            if (!isset($submitted[$lang][$key]) || !is_string($submitted[$lang][$key])) { return new WP_Error('incomplete', 'Formulaire incomplet : rechargez la page. Aucune modification enregistrée.'); }
            $clean[$lang][$key] = atelier_langues_clean_value($submitted[$lang][$key], $type);
        }
    }
    $source = atelier_langues_source();
    $result = wp_update_post(wp_slash(array('ID' => $id, 'post_title' => $clean[$source]['title'], 'post_content' => $clean[$source]['content'], 'post_excerpt' => $clean[$source]['excerpt'])), true);
    if (is_wp_error($result)) { return $result; }
    foreach (atelier_langues_languages() as $lang => $label) {
        if ($source === $lang) { continue; }
        $entry = array_replace(atelier_langues_translation($id, $lang), $clean[$lang]);
        $entry['ready'] = !empty($submitted[$lang]['ready']) && '' !== trim($entry['title']);
        update_post_meta($id, '_atelier_langues_' . $lang, wp_slash($entry));
    }
    return true;
}

add_action('admin_post_atelier_langues_save', function () {
    $id = isset($_POST['content_id']) ? absint($_POST['content_id']) : 0;
    $post = get_post($id);
    if (!current_user_can('manage_options') || !current_user_can('edit_post', $id) || !$post || !in_array($post->post_type, atelier_langues_post_types(), true)) {
        wp_die('Accès refusé.', '', array('response' => 403));
    }
    check_admin_referer('atelier_langues_save_' . $id);
    if (!isset($_POST['source_modified']) || !is_string($_POST['source_modified']) || $post->post_modified_gmt !== wp_unslash($_POST['source_modified'])) {
        wp_die('Le contenu a changé dans un autre onglet. Rechargez la page avant de réessayer.', '', array('response' => 409));
    }
    $result = atelier_langues_save_post_translations($id, isset($_POST['translations']) ? wp_unslash($_POST['translations']) : null);
    if (is_wp_error($result)) {
        wp_die(esc_html($result->get_error_message()), '', array('response' => 400));
    }
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

/** Le libellé source reste celui de WordPress ; les traductions partagent une valeur multi. */
function atelier_langues_menu_field($item_id) {
    $english = get_post_meta($item_id, '_atelier_langues_menu_en', true);
    $english = is_string($english) ? $english : '';
    echo '<div class="description description-wide">';
    atelier_langues_native_editor(get_post_field('post_title', $item_id), $english, 'atelier_menu[' . $item_id . ']', '#edit-menu-item-title-' . $item_id, 'text', 'Libellé du menu');
    echo '<span class="description">Vide : le libellé français est conservé. Les icônes et les destinations restent partagées.</span>';
    wp_nonce_field('atelier_menu_' . $item_id, 'atelier_menu_nonce[' . $item_id . ']', false);
    echo '</div>';
}
add_action('wp_nav_menu_item_custom_fields', 'atelier_langues_menu_field');

function atelier_langues_save_menu_field($menu_id, $item_id) {
    // Ne rien modifier lors d'une sauvegarde sans notre formulaire (API, import…).
    if (!current_user_can('edit_theme_options') || 'nav_menu_item' !== get_post_type($item_id)) {
        return;
    }
    $nonce = $_POST['atelier_menu_nonce'][$item_id] ?? '';
    $value = $_POST['atelier_menu'][$item_id] ?? null;
    if (!is_string($nonce) || !wp_verify_nonce(wp_unslash($nonce), 'atelier_menu_' . $item_id)) {
        return;
    }
    $packed = atelier_langues_native_save(get_post_field('post_title', $item_id), get_post_meta($item_id, '_atelier_langues_menu_en', true), wp_unslash($value), 'text');
    if (null !== $packed) { update_post_meta($item_id, '_atelier_langues_menu_en', wp_slash($packed)); }
}
add_action('wp_update_nav_menu_item', 'atelier_langues_save_menu_field', 10, 2);

/** Une seule valeur supplémentaire sur la discipline existante. */
add_action('discipline_edit_form_fields', function ($term) {
    $value = get_term_meta($term->term_id, '_atelier_langues_name_en', true);
    echo '<tr class="form-field"><th scope="row">Nom de la discipline</th><td>';
    atelier_langues_native_editor($term->name, is_string($value) ? $value : '', 'atelier_discipline', '#name', 'text', 'Discipline');
    echo '<p class="description">Vide : le nom français est conservé. Le slug et les créations associées restent inchangés.</p>';
    wp_nonce_field('atelier_discipline_' . $term->term_id, 'atelier_discipline_nonce');
    echo '</td></tr>';
    echo '<tr class="form-field"><th scope="row">Description</th><td>';
    atelier_langues_native_editor($term->description, get_term_meta($term->term_id, '_atelier_langues_description_en', true), 'atelier_discipline_description', '#description', 'textarea', 'Description de la discipline');
    echo '</td></tr>';
});

add_action('edited_discipline', function ($term_id) {
    $value = $_POST['atelier_discipline'] ?? null;
    $nonce = $_POST['atelier_discipline_nonce'] ?? '';
    if (!current_user_can('edit_term', $term_id) || !is_string($nonce) || !wp_verify_nonce(wp_unslash($nonce), 'atelier_discipline_' . $term_id)) {
        return;
    }
    $term = get_term($term_id, 'discipline');
    if (!$term || is_wp_error($term)) { return; }
    $packed = atelier_langues_native_save($term->name, get_term_meta($term_id, '_atelier_langues_name_en', true), wp_unslash($value), 'text');
    if (null !== $packed) { update_term_meta($term_id, '_atelier_langues_name_en', wp_slash($packed)); }
    $description = atelier_langues_native_save($term->description, get_term_meta($term_id, '_atelier_langues_description_en', true), isset($_POST['atelier_discipline_description']) ? wp_unslash($_POST['atelier_discipline_description']) : null, 'wysiwyg');
    if (null !== $description) { update_term_meta($term_id, '_atelier_langues_description_en', wp_slash($description)); }
});
