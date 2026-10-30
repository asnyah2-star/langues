# Structure du site — 30 octobre 2026

## Point de retour

- Commit initial : `313f234` (avant les corrections du thème).
- Sauvegarde : `.backups/2026-10-30_114822/`, fichiers et base SQL.
- Archive vérifiée avec ZipArchive et empreintes SHA-256 dans le manifeste.
- Les trois fichiers du thème modifiés ont été comparés à leur version archivée : celle-ci correspond au commit initial.
- La restauration complète n'a pas encore été testée. Conserver aussi une copie de la sauvegarde hors du dossier du site ; l'archive contient notamment la configuration WordPress.
- Git conserve le thème maison, le plugin maison, les outils et les documents. Les médias, WordPress, les extensions tierces, les sauvegardes et les identifiants sont exclus.

## Corrections

- Cadre commun centré de 1280 px maximum pour le header, le contenu et le footer.
- Header sticky, prise en compte de la barre WordPress et menu compact défilable sur petite hauteur.
- Navigation sur une ligne au-dessus de 1100 px ; seuil commun CSS/JavaScript conservé.
- Créations souligné sur les disciplines et les fiches d'œuvres.
- Adresse du champ de contact affichée sur Contact : arobase générée par `.email-domain::before`, lien et libellé accessible encodés avec `antispambot`. Ce procédé reste une obfuscation ; le lien contient une adresse récupérable et la copie du texte visible peut omettre l'arobase générée.
- Aucun changement au plugin Atelier Langues ni à ses données.

## Vérification

Syntaxe PHP des fichiers modifiés et de l'outil de sauvegarde valide, syntaxe JavaScript de navigation valide. Contact et discipline Arts plastiques répondent en HTTP 200 ; HTML de l'adresse et classe active du menu vérifiés.

Recette visuelle restant à faire : 375, 768, 1100, 1280 et 1920 px ; navigation clavier, défilement, barre WordPress et menus FR/EN. Aucun contrôle visuel automatisé n'a été effectué dans cette session.

Le formulaire et l'intégration des traductions dans ACF restent des étapes séparées.
