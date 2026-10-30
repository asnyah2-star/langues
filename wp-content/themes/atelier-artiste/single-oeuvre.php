<?php get_header(); ?>
<main id="contenu">
    <?php while (have_posts()) : the_post(); ?>
        <?php foreach ((array) get_the_terms(get_the_ID(), 'discipline') as $discipline) : ?>
            <?php
            if (!$discipline instanceof WP_Term) { continue; }
            $discipline_url = get_term_link($discipline);
            if (is_wp_error($discipline_url)) { continue; }
            $name = $discipline->name;
            if (function_exists('atelier_langues_current') && atelier_langues_source() !== atelier_langues_current()) {
                $translated = get_term_meta($discipline->term_id, '_atelier_langues_name_en', true);
                $name = function_exists('atelier_langues_native_text') ? atelier_langues_native_text($translated, $name) : $name;
            }
            ?>
            <a class="text-link" href="<?php echo esc_url($discipline_url); ?>">← <?php echo esc_html($name); ?></a>
        <?php endforeach; ?>
        <h1><?php the_title(); ?></h1>
        <?php
        $work_content = apply_filters('the_content', get_the_content());
        // Une fiche composée uniquement d'images prend la forme d'une galerie.
        $gallery = substr_count($work_content, '<img') + (has_post_thumbnail() ? 1 : 0) > 1
            && '' === trim(wp_strip_all_tags($work_content))
            && false === strpos($work_content, '<iframe')
            && false === strpos($work_content, '<video');
        if ($gallery) {
            $gallery_html = new WP_HTML_Tag_Processor($work_content);
            while ($gallery_html->next_tag('FIGURE')) {
                $gallery_html->set_attribute('tabindex', '0');
            }
            $work_content = $gallery_html->get_updated_html();
        }
        ?>
        <div class="about-layout<?php echo $gallery ? ' work-gallery' : ''; ?>">
            <?php if (has_post_thumbnail()) : ?><figure class="work-visual" tabindex="0"><?php the_post_thumbnail('full'); ?></figure><?php endif; ?>
            <div class="prose">
                <?php
                $details = array();
                foreach (array('oeuvre_annee' => __('Année', 'atelier-artiste'), 'oeuvre_technique' => __('Technique', 'atelier-artiste'), 'oeuvre_dimensions' => __('Dimensions', 'atelier-artiste')) as $name => $label) {
                    $value = atelier_artiste_field($name);
                    if ($value) {
                        $details[$label] = $value;
                    }
                }
                ?>
                <?php if ($details) : ?>
                    <dl class="work-details">
                        <?php foreach ($details as $label => $value) : ?>
                            <div><dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html($value); ?></dd></div>
                        <?php endforeach; ?>
                    </dl>
                <?php endif; ?>
                <?php echo $work_content; // Contenu passé par les filtres WordPress, comme the_content(). ?>
            </div>
        </div>
    <?php endwhile; ?>
</main>
<dialog class="image-viewer" aria-label="Image" data-open-label="<?php esc_attr_e('Agrandir l’image', 'atelier-artiste'); ?>">
    <button class="image-viewer-close" type="button" aria-label="<?php esc_attr_e('Fermer l’image', 'atelier-artiste'); ?>" autofocus>×</button>
    <img alt="">
</dialog>
<?php get_footer(); ?>
