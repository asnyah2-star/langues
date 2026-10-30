<?php get_header(); ?>
<main id="contenu" class="works-page">
    <h1><?php echo esc_html(single_term_title('', false)); ?></h1>
    <?php if (term_description()) : ?>
        <div class="page-intro"><?php echo wp_kses_post(term_description()); ?></div>
    <?php endif; ?>
    <?php
    global $wp_query;
    get_template_part('loop', 'creations', array('query' => $wp_query, 'current' => max(1, (int) get_query_var('paged'))));
    ?>
</main>
<?php get_footer(); ?>
