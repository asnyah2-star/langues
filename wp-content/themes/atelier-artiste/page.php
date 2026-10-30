<?php
get_header();

// Une structure commune ; seules les particularités des deux pages changent.
$is_about = is_page('a-propos');
$is_contact = is_page('contact');
$page_class = $is_about ? 'about-page' : ($is_contact ? 'contact-page' : '');
?>
<main id="contenu" class="<?php echo esc_attr($page_class); ?>">
    <?php while (have_posts()) : the_post(); ?>
        <?php if ($is_about) : ?>
            <p class="eyebrow"><?php esc_html_e('L’artiste', 'atelier-artiste'); ?></p>
        <?php endif; ?>
        <h1><?php the_title(); ?></h1>

        <?php if ($is_about) : ?>
            <div class="about-layout">
                <?php if (has_post_thumbnail()) : ?><figure><?php the_post_thumbnail('large'); ?></figure><?php endif; ?>
                <div class="prose">
                    <?php $intro = atelier_artiste_field('artiste_intro'); ?>
                    <?php if ($intro) : ?><p class="page-intro"><?php echo nl2br(esc_html($intro)); ?></p><?php endif; ?>
                    <?php the_content(); ?>
                    <?php foreach (array('artiste_demarche' => __('Démarche artistique', 'atelier-artiste'), 'artiste_parcours' => __('Parcours et expositions', 'atelier-artiste')) as $field => $title) : ?>
                        <?php $text = atelier_artiste_field($field); ?>
                        <?php if ($text) : ?>
                            <section class="artist-section">
                                <h2><?php echo esc_html($title); ?></h2>
                                <?php echo wp_kses_post($text); ?>
                            </section>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else : ?>
            <?php if ($is_contact) : ?>
                <?php $intro = atelier_artiste_field('contact_intro'); ?>
                <p class="page-intro"><?php echo esc_html($intro ?: __('Pour une question sur une œuvre, une exposition ou une collaboration.', 'atelier-artiste')); ?></p>
            <?php endif; ?>

            <?php the_content(); ?>

            <?php if ($is_contact) : ?>
                <?php
                $contact_logo = (int) atelier_artiste_field('contact_logo');
                $contact_signature = atelier_artiste_field('contact_signature');
                ?>
                <?php if ($contact_logo || $contact_signature) : ?>
                    <div class="contact-signature">
                        <?php if ($contact_logo) { echo wp_get_attachment_image($contact_logo, 'medium', false, array('class' => 'contact-logo')); } ?>
                        <?php if ($contact_signature) : ?><p><?php echo esc_html($contact_signature); ?></p><?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        <?php endif; ?>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
