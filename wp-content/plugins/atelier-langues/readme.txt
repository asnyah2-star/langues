=== Langues ===
Requires PHP: 7.4
Stable tag: 0.4.0

== Fonctionnement ==

Extension maison multilingue, avec traduction manuelle et sans API payante.
Une seule zone de saisie visible par texte, un onglet par langue configurée.

== Configuration réutilisable ==

atelier_langues_languages(), dans atelier-langues.php, contient la liste unique
des langues. FR/EN sont actives ; décommenter les exemples NL, ES ou RU pour
les ajouter. Onglets, formulaires, URL et détection suivent cette liste.
La première langue correspond aux contenus natifs et sert de secours.
Ne pas changer la langue source d’un site existant sans migration des contenus.
Sur un autre site, adapter également les noms ACF et le catalogue du thème.

L’encodeur reçoit un tableau code => texte et produit multi_CODE pour chaque
langue. Les anciennes valeurs FR/EN restent lisibles. Une langue désactivée
garde ses textes enregistrés ; ses URL ne sont plus actives.

Les textes ACF de l’accueil, d’À propos, de Contact, du footer et la technique
des œuvres se traduisent directement dans leur fiche. Les langues sont
réunies dans la valeur du champ avec multi et multi_CODE.
La cliente ne saisit pas ces balises. La liste des champs figure dans
atelier_langues_multi_fields(), dans atelier-langues.php.

Les anciens textes sont repris à l’ouverture, puis assemblés à l’enregistrement.
Les anciennes traductions ne sont pas supprimées. Les images, l’e-mail,
les liens, dates et dimensions restent communs.

== Autres traductions ==

Réglages > Langues : titres, contenus et extraits WordPress, disponibilité
de chaque traduction des pages/créations et textes fixes du thème. Leurs champs natifs
WordPress sont conservés ; les textes ACF ne sont plus dupliqués dans cet écran.

Apparence > Menus : libellés à onglets.
Créations > Disciplines > Modifier : nom et description à onglets.
Réglages du site : logo, coordonnées, liens et pied de page à onglets.

== Langue du visiteur ==

URL de langue explicite prioritaire, puis choix mémorisé, puis préférences du
navigateur. La langue source sert de secours si nécessaire. La détection est active
sur cette installation et reste réglable dans Langues.
Pour une page, chaque traduction nécessite un titre et le statut relu/disponible.

== Vérification et limites ==

94 contrôles multi et 43 contrôles généraux passent. La recette visuelle et
les sauvegardes réelles depuis WordPress restent à effectuer avec la cliente.
Pas de migration destructive automatique. Git ne sauvegarde pas la base.
Le retour à l’ancien code ne reconvertit pas les valeurs multi enregistrées.

Les slugs anglais et la traduction indépendante des textes alternatifs et
légendes des médias restent hors périmètre. Production non encore validée.

Lire NOTES.md pour les repères techniques et FICHE-STAGE-LANGUES.md à la racine
pour l’explication au responsable. AUDIT.md conserve l’audit historique.
