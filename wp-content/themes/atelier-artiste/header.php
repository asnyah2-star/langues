<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#contenu"><?php esc_html_e('Aller au contenu', 'atelier-artiste'); ?></a>
<header class="site-header">
    <!-- Le logo remplace le nom dès qu'il est sélectionné dans WordPress. -->
    <div class="site-branding">
        <?php if (has_custom_logo()) : ?>
            <?php the_custom_logo(); ?>
        <?php else : ?>
            <a class="site-logo" href="<?php echo esc_url(function_exists('atelier_langues_content_url') ? atelier_langues_content_url(home_url('/')) : home_url('/')); ?>"><?php bloginfo('name'); ?></a>
        <?php endif; ?>
    </div>
    <button class="nav-toggle" type="button" aria-label="<?php esc_attr_e('Navigation principale', 'atelier-artiste'); ?>" aria-controls="site-navigation" aria-expanded="false" hidden><span aria-hidden="true"></span></button>
    <nav id="site-navigation" class="site-navigation" aria-label="<?php esc_attr_e('Navigation principale', 'atelier-artiste'); ?>">
        <?php if (has_nav_menu('principal')) : ?>
            <?php wp_nav_menu(array('theme_location' => 'principal', 'container' => false, 'menu_class' => 'menu', 'fallback_cb' => false)); ?>
        <?php else : ?>
            <a href="<?php echo esc_url(function_exists('atelier_langues_content_url') ? atelier_langues_content_url(home_url('/')) : home_url('/')); ?>"><?php esc_html_e('Accueil', 'atelier-artiste'); ?></a>
        <?php endif; ?>
        <?php if (has_nav_menu('social')) : ?>
            <?php wp_nav_menu(array('theme_location' => 'social', 'container' => false, 'menu_class' => 'social-links', 'fallback_cb' => false)); ?>
        <?php endif; ?>
        <?php if (function_exists('atelier_langues_switcher')) { atelier_langues_switcher(); } ?>
    </nav>
</header>
