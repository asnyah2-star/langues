<?php
/** Édition à onglets et stockage des textes ACF dans une valeur bilingue. */
if (!defined('ABSPATH')) { exit; }

function atelier_langues_multi_decode($value) {
    if (!is_string($value) || !preg_match('~\A<multi>(.*)</multi>\z~s', $value, $body)) { return null; }
    $pattern = '~<multi_([a-z]{2,3}(?:-[a-z0-9]{2,8})*)>(.*?)</multi_\1>~s';
    if (!preg_match_all($pattern, $body[1], $matches, PREG_SET_ORDER) || '' !== trim(preg_replace($pattern, '', $body[1]))) { return null; }
    $parts = array();
    foreach ($matches as $match) {
        if (isset($parts[$match[1]])) { return null; }
        $parts[$match[1]] = html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    return $parts;
}

function atelier_langues_multi_encode(array $translations) {
    $result = "<multi>\n";
    foreach ($translations as $code => $text) {
        if (!is_string($code) || !preg_match('/^[a-z]{2,3}(?:-[a-z0-9]{2,8})*$/D', $code) || !is_string($text)) { continue; }
        $result .= '<multi_' . $code . '>' . htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</multi_' . $code . ">\n";
    }
    return $result . '</multi>';
}

function atelier_langues_multi_parts($value) {
    $parts = is_array($value) ? $value : (atelier_langues_multi_decode($value) ?? array(atelier_langues_source() => (string) $value));
    return array_replace(array_fill_keys(array_keys(atelier_langues_languages()), ''), $parts);
}

function atelier_langues_multi_valid($values) {
    if (!is_array($values)) { return false; }
    foreach (atelier_langues_languages() as $lang => $label) {
        if (!isset($values[$lang]) || !is_string($values[$lang])) { return false; }
    }
    return true;
}

/** Fusionner les langues actives sans effacer une langue désactivée. */
function atelier_langues_multi_merge($stored, $submitted, $type) {
    $parts = atelier_langues_multi_parts($stored);
    foreach (atelier_langues_languages() as $lang => $label) { $parts[$lang] = atelier_langues_clean_value($submitted[$lang], $type); }
    return atelier_langues_multi_encode($parts);
}

function atelier_langues_native_parts($source, $stored) {
    $parts = atelier_langues_multi_decode($stored) ?? array('en' => is_string($stored) ? $stored : '');
    $parts[atelier_langues_source()] = $source;
    return atelier_langues_multi_parts($parts);
}

function atelier_langues_native_text($stored, $fallback = '', $language = null) {
    $language = $language ?? atelier_langues_current();
    if (atelier_langues_source() === $language) { return $fallback; }
    $parts = atelier_langues_multi_decode($stored) ?? array('en' => is_string($stored) ? $stored : '');
    return isset($parts[$language]) && '' !== trim($parts[$language]) ? $parts[$language] : $fallback;
}

function atelier_langues_native_save($source, $stored, $submitted, $type) {
    if (!is_array($submitted)) { return null; }
    $submitted[atelier_langues_source()] = $source;
    if (!atelier_langues_multi_valid($submitted)) { return null; }
    return atelier_langues_multi_merge(atelier_langues_native_parts($source, $stored), $submitted, $type);
}

/** Réutiliser le contrôle français natif sans dupliquer sa sauvegarde. */
function atelier_langues_native_editor($source, $stored, $name, $selector, $type = 'text', $label = 'Libellé') {
    echo '<div data-langues-native="' . esc_attr($selector) . '">';
    $names = array();
    foreach (atelier_langues_languages() as $lang => $language_label) { $names[$lang] = atelier_langues_source() === $lang ? '' : $name . '[' . $lang . ']'; }
    atelier_langues_multi_editor(atelier_langues_native_parts($source, $stored), $names, $type, $label);
    echo '</div>';
}

/** Le même composant sert aux champs ACF et aux formulaires WordPress. */
function atelier_langues_multi_editor($parts, $names, $type = 'textarea', $label = 'Texte') {
    $id = wp_unique_id('langues-');
    echo '<div class="langues-multi-editors"><div class="langues-multi-tabs" role="tablist" aria-label="' . esc_attr($label) . '" hidden>';
    foreach (atelier_langues_languages() as $lang => $name) {
        $active = atelier_langues_source() === $lang;
        echo '<button type="button" role="tab" data-language="' . esc_attr($lang) . '" id="' . esc_attr($id . '-tab-' . $lang) . '" aria-controls="' . esc_attr($id . '-panel-' . $lang) . '" aria-selected="' . ($active ? 'true' : 'false') . '" tabindex="' . ($active ? '0' : '-1') . '">' . esc_html($name) . '</button>';
    }
    echo '</div>';
    foreach (atelier_langues_languages() as $lang => $name) {
        $input_id = $id . '-' . $lang;
        echo '<div class="langues-multi-panel" id="' . esc_attr($id . '-panel-' . $lang) . '"><label for="' . esc_attr($input_id) . '">' . esc_html($label . ' — ' . $name) . '</label>';
        if ('wysiwyg' === $type) {
            // Les contenus en blocs sont conservés tels quels en mode texte.
            wp_editor($parts[$lang], $input_id, array('textarea_name' => $names[$lang], 'textarea_rows' => 8, 'wpautop' => false, 'tinymce' => !has_blocks($parts[$lang]), 'media_buttons' => false));
        } elseif (in_array($type, array('text', 'url'), true)) {
            echo '<input class="large-text" type="' . esc_attr($type) . '" lang="' . esc_attr($lang) . '" id="' . esc_attr($input_id) . '" ' . ('' === $names[$lang] ? 'readonly' : 'name="' . esc_attr($names[$lang]) . '"') . ' value="' . esc_attr($parts[$lang]) . '">';
        } else {
            echo '<textarea rows="8" lang="' . esc_attr($lang) . '" id="' . esc_attr($input_id) . '" ' . ('' === $names[$lang] ? 'readonly' : 'name="' . esc_attr($names[$lang]) . '"') . '>' . esc_textarea($parts[$lang]) . '</textarea>';
        }
        echo '</div>';
    }
    echo '</div>';
}

add_action('acf/include_field_types', function () {
    class Atelier_Langues_Multi_Field extends acf_field {
        public function initialize() {
            $this->name = 'atelier_multi';
            $this->label = 'Texte multilingue';
            $this->category = 'basic';
        }

        public function render_field($field) {
            $id = acf_get_form_data('post_id');
            $parts = atelier_langues_multi_decode($field['value']);
            $names = array();
            $fallback = array();
            foreach (atelier_langues_languages() as $lang => $label) {
                $names[$lang] = $field['name'] . '[' . $lang . ']';
                if (null === $parts) { $legacy = is_numeric($id) ? atelier_langues_translation((int) $id, $lang) : array(); $fallback[$lang] = atelier_langues_source() === $lang ? (string) $field['value'] : ($legacy['acf'][$field['key']] ?? ''); }
            }
            atelier_langues_multi_editor(atelier_langues_multi_parts($parts ?? $fallback), $names, $field['langues_type'] ?? 'textarea', $field['label']);
            echo '<p class="description">Changez d’onglet pour traduire. Les versions sont enregistrées ensemble ; texte vide : reprise de la langue source.</p>';
        }

        public function validate_value($valid, $value, $field, $input) {
            if (!atelier_langues_multi_valid($value)) { return 'Le formulaire doit contenir toutes les langues configurées. Rechargez la page.'; }
            if (!empty($field['required']) && '' === trim($value[atelier_langues_source()])) { return 'Le texte dans la langue source est obligatoire.'; }
            return $valid;
        }

        public function update_value($value, $post_id, $field) {
            if (!atelier_langues_multi_valid($value)) {
                // API, import ou formulaire incomplet : conserver la valeur existante.
                return get_post_meta((int) $post_id, $field['name'], true);
            }
            $type = $field['langues_type'] ?? 'textarea';
            return atelier_langues_multi_merge(get_post_meta((int) $post_id, $field['name'], true), $value, $type);
        }

        public function format_value($value, $post_id, $field, $escape_html = false) {
            // Rétablir le formatage ACF d'origine après le choix de langue.
            if (atelier_langues_frontend() && isset($field['langues_type'])) {
                $field['type'] = $field['langues_type'];
                $native = acf_get_field_type($field['type']);
                if (method_exists($native, 'format_value')) { return $native->format_value($value, $post_id, $field, $escape_html); }
            }
            return $value;
        }
    }
    acf_register_field_type('Atelier_Langues_Multi_Field');
});

// Le groupe ACF enregistré en base n'est pas modifié.
add_filter('acf/load_field', function ($field) {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if ($screen && 'acf-field-group' === $screen->post_type) { return $field; }
    if (in_array($field['name'], atelier_langues_multi_fields(), true) && in_array($field['type'], array('text', 'textarea', 'wysiwyg'), true)) {
        $field['langues_type'] = $field['type'];
        $field['type'] = 'atelier_multi';
    }
    return $field;
});

add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, array('post.php', 'post-new.php', 'nav-menus.php', 'term.php', 'edit-tags.php', 'settings_page_atelier-langues', 'toplevel_page_atelier-site'), true)) { return; }
    wp_enqueue_style('langues-admin', plugins_url('../assets/admin.css', __FILE__), array(), (string) filemtime(dirname(__DIR__) . '/assets/admin.css'));
    wp_enqueue_script('langues-multi-tabs', plugins_url('../assets/multi.js', __FILE__), array(), (string) filemtime(dirname(__DIR__) . '/assets/multi.js'), true);
});
