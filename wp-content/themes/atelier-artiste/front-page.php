<?php get_header(); ?>
<main id="contenu" class="home-main">
    <?php while (have_posts()) : the_post(); ?>
        <?php
        $accroche = atelier_artiste_field('accroche');
        $mode = atelier_artiste_field('accueil_visuel_mode');
        $images = array();
        if ('image' === $mode) {
            $images = array((int) atelier_artiste_field('accueil_image_fond'));
        } elseif ('carousel' === $mode) {
            $images = (array) atelier_artiste_field('accueil_carrousel');
        }
        $images = array_values(array_filter(array_map('absint', $images), 'wp_attachment_is_image'));
        $voile = atelier_artiste_field('accueil_voile');
        $voile = is_numeric($voile) ? max(0, min(100, (int) $voile)) : 35;
        ?>
        <section class="home-hero" aria-labelledby="titre-accueil" style="--hero-veil: <?php echo esc_attr($voile / 100); ?>;">
            <div class="hero-copy">
                <p class="eyebrow">Tamara Szefer <span aria-hidden="true">/</span> <?php esc_html_e('Artiste multidisciplinaire', 'atelier-artiste'); ?></p>
                <?php
                $home_logo = (int) atelier_artiste_field('accueil_logo');
                if ($home_logo) {
                    echo wp_get_attachment_image($home_logo, 'medium', false, array('class' => 'home-logo'));
                }
                ?>
                <h1 id="titre-accueil"><?php echo esc_html($accroche ?: __('Tendre Chaos', 'atelier-artiste')); ?></h1>
                <?php $intro = atelier_artiste_field('accueil_intro'); ?>
                <?php if ($intro) : ?>
                    <div class="hero-bottom">
                        <p><?php echo nl2br(esc_html($intro)); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($images) : ?>
                <div class="hero-images<?php echo count($images) > 1 ? ' is-carousel' : ''; ?>">
                    <?php foreach ($images as $index => $image_id) : ?>
                        <div class="hero-slide"<?php echo $index ? ' hidden' : ''; ?>>
                            <?php echo wp_get_attachment_image($image_id, 'full', false, array('loading' => $index ? 'lazy' : 'eager', 'fetchpriority' => $index ? 'auto' : 'high', 'sizes' => '100vw')); ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($images) > 1) : ?>
                        <div class="hero-controls" hidden>
                            <button type="button" data-direction="-1" aria-label="<?php esc_attr_e('Image précédente', 'atelier-artiste'); ?>">←</button>
                            <span class="hero-counter" role="status" aria-live="polite">1 / <?php echo count($images); ?></span>
                            <button type="button" data-direction="1" aria-label="<?php esc_attr_e('Image suivante', 'atelier-artiste'); ?>">→</button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
        <?php if (has_post_thumbnail()) : ?>
            <figure class="home-artwork">
                <?php the_post_thumbnail('full', array('loading' => 'eager')); ?>
                <?php if (get_the_post_thumbnail_caption()) : ?>
                    <figcaption><?php echo esc_html(get_the_post_thumbnail_caption()); ?></figcaption>
                <?php endif; ?>
            </figure>
        <?php endif; ?>
        <div class="home-content"><?php the_content(); ?></div>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
