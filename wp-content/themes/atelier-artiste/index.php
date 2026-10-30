<?php get_header(); ?>
<main id="contenu">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class(); ?>>
                <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                <?php the_content(); ?>
            </article>
        <?php endwhile; ?>
        <?php the_posts_navigation(); ?>
    <?php else : ?>
        <p><?php esc_html_e('Aucun contenu pour le moment.', 'atelier-artiste'); ?></p>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
