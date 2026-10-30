<?php
// Les champs du footer sont enregistrés sur la page Accueil.
$accueil_id = (int) get_option('page_on_front');
$footer_texte = '';
if ($accueil_id) {
    $footer_texte = atelier_artiste_field('footer_texte', $accueil_id);
}
?>
<footer id="contact" class="site-footer">
    <p>&copy; <?php echo esc_html(wp_date('Y')); ?> <?php bloginfo('name'); ?></p>
    <?php if ($footer_texte) : ?>
        <p><?php echo nl2br(esc_html($footer_texte)); ?></p>
    <?php endif; ?>
    <?php if (has_nav_menu('footer')) : ?>
        <nav aria-label="<?php esc_attr_e('Navigation du pied de page', 'atelier-artiste'); ?>">
            <?php wp_nav_menu(array('theme_location' => 'footer', 'container' => false, 'fallback_cb' => false)); ?>
        </nav>
    <?php endif; ?>
</footer>
<?php wp_footer(); ?>
</body>
</html>
