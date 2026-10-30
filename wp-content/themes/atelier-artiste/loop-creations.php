<?php
// La même grille pour toutes les créations et pour chaque discipline.
$works = $args['query'];
$current = $args['current'];
?>
<?php if ($works->have_posts()) : ?>
    <div class="works-grid">
        <?php while ($works->have_posts()) : $works->the_post(); ?>
            <article class="work-card">
                <a href="<?php the_permalink(); ?>">
                    <h2><?php the_title(); ?></h2>
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('large'); ?>
                    <?php else : ?>
                        <div class="work-placeholder"><?php esc_html_e('Visuel à venir', 'atelier-artiste'); ?></div>
                    <?php endif; ?>
                </a>
                <?php $annee = atelier_artiste_field('oeuvre_annee'); ?>
                <?php if ($annee) : ?><p><?php echo esc_html($annee); ?></p><?php endif; ?>
            </article>
        <?php endwhile; ?>
    </div>
    <?php echo wp_kses_post(paginate_links(array('total' => $works->max_num_pages, 'current' => $current))); ?>
<?php else : ?>
    <p class="empty-state"><?php esc_html_e('Les créations seront présentées prochainement.', 'atelier-artiste'); ?></p>
<?php endif; ?>
