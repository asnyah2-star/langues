# Langues — notes techniques

État du 30 octobre 2026 — version locale 0.4.0.

## Ajouter une langue et réutiliser le plugin

Une seule liste commande les onglets, les formulaires, les préfixes d’URL, les liens de changement de langue et la détection du navigateur : `atelier_langues_languages()`, dans `atelier-langues.php`.

FR et EN restent actives. Pour ajouter une langue, décommenter ou ajouter une ligne dans cette liste :

```php
'nl' => 'Nederlands',
'es' => 'Español',
'ru' => 'Русский',
```

Après enregistrement du fichier, recharger WordPress : les règles d’URL se régénèrent une fois si la liste a changé. Remplir le nouvel onglet des textes ACF et, dans Langues, le titre/contenu de chaque page concernée ; cocher sa disponibilité après relecture. Configurer une langue ne crée aucune traduction automatiquement.

La première entrée est la langue des contenus natifs et de secours. Conserver FR en premier sur ce site. Pour un autre site, choisir la langue source avant de saisir des contenus ; changer ensuite la source n’effectue pas de migration. Adapter également la liste des noms ACF et le catalogue/domaine des textes fixes au thème du nouveau site.

Les codes acceptent les variantes régionales en minuscules, par exemple `pt-br`. Le filtre WordPress `atelier_langues_languages` permet aussi de fournir la liste depuis un autre composant.

`multi_encode()` reçoit maintenant un tableau associatif, et non deux arguments :

```php
atelier_langues_multi_encode(array(
    'fr' => 'Bonjour',
    'en' => 'Hello',
    'nl' => 'Hallo',
    'es' => 'Hola',
    'ru' => 'Привет',
));
```

Une boucle produit `<multi_fr>`, `<multi_en>`, `<multi_nl>`, etc. Le lecteur n’impose ni deux langues ni un ordre particulier. Les anciennes enveloppes FR/EN restent valides. Une langue désactivée est masquée et ses URL ne sont plus routées, mais ses traductions stockées restent conservées pour une réactivation ultérieure. Un formulaire chargé avant l’ajout d’une langue est refusé s’il manque les nouvelles zones, au lieu d’effacer des données.

## Décisions actuelles

- Un composant multilingue commun, une seule zone visible par texte ; sans JavaScript, toutes les zones restent accessibles.
- Neuf champs ACF configurés dans atelier_langues_multi_fields(), dans le fichier principal : accroche, accueil_intro, artiste_intro, artiste_demarche, artiste_parcours, contact_intro, contact_signature, footer_texte, oeuvre_technique.
- Le filtre acf/load_field applique le type atelier_multi à ces seuls champs, sans changer leurs clés ni les définitions des groupes en base. L’écran de définition des groupes ACF est exclu.
- Le type d’origine est mémorisé dans langues_type pendant la requête : saisie, nettoyage et formatage respectent text, textarea ou wysiwyg.
- multi_encode échappe chaque contenu dans une enveloppe multi ; multi_decode restitue le tableau code → texte. La sélection de langue intervient avant le formatage ACF, y compris pour les textes riches.
- La reprise des valeurs historiques est paresseuse : aucune écriture à l’ouverture, assemblage à l’enregistrement. Une valeur multi est prioritaire sur l’ancienne traduction du même champ. L’ancien tableau anglais est conservé.
- Les mises à jour ACF doivent fournir un tableau contenant chaque code actif. Un appel incompatible n’écrase pas le champ. Les langues stockées hors configuration sont préservées pendant la fusion.

## Stockage et compatibilité WordPress

| Données | Stockage |
| --- | --- |
| Textes ACF configurés | Une valeur postmeta par champ, enveloppe multi après enregistrement |
| Titre, contenu et extrait source | Champs natifs de posts, pour conserver Gutenberg, les slugs et les intégrations |
| Autres titres, contenus, extraits et statuts ; anciennes traductions ACF | Tableau postmeta _atelier_langues_CODE, par exemple _atelier_langues_en ou _atelier_langues_nl |
| Libellé bilingue de menu | Enveloppe multi dans _atelier_langues_menu_en après enregistrement ; français natif conservé |
| Nom bilingue de discipline | Enveloppe multi dans termmeta _atelier_langues_name_en après enregistrement ; nom et slug natifs conservés |
| Description bilingue de discipline | Enveloppe multi dans _atelier_langues_description_en ; description française native conservée |
| Textes fixes | Option atelier_langues_theme_strings ; chaque entrée enregistrée devient multi |

Les anciens libellés anglais simples et les anciennes entrées de textes fixes en tableau restent lisibles. Les descriptions de disciplines sont modifiables sur la fiche Modifier. La création rapide d’une discipline reste native : traduire après sa création.

Les noms historiques `_atelier_langues_menu_en`, `_atelier_langues_name_en` et `_atelier_langues_description_en` sont conservés pour ne pas déplacer les données : leur enveloppe multi peut contenir toutes les langues. Un ancien anglais simple n’est jamais interprété comme une autre traduction.

Les contrôles français natifs des menus et disciplines sont déplacés visuellement dans leur onglet par JavaScript : leurs noms de formulaire et leur sauvegarde WordPress sont conservés. Le lien de discipline des fiches d’œuvres a été adapté au nouveau format de libellé.

## Ergonomie et nettoyage

Le formulaire ACF en double a été retiré de Réglages → Langues. Cet écran conserve les titres, contenus, extraits, statuts et textes fixes. Les champs ACF se traduisent dans les pages et créations. Le footer de Réglages du site utilise le même composant et le même champ que l’accueil.

Le CSS a été regroupé dans assets/admin.css. Le JavaScript et le CSS sont chargés uniquement sur les écrans concernés. Les zones riches utilisent l’éditeur WordPress ; les contenus principaux qui contiennent des blocs restent en mode texte dans l’écran de traduction pour ne pas réinterpréter leur structure. Le français reste éditable avec Gutenberg dans sa fiche native.

Le navigateur de la personne qui édite détermine le premier onglet affiché parmi les langues actives. Changer d’onglet ne désactive pas les champs : tous les textes sont soumis. Les éditeurs riches synchronisent leur textarea pour la sauvegarde ACF. Flèches gauche/droite, Début et Fin gèrent la navigation clavier, quel que soit le nombre d’onglets.

## Affichage public

Priorité : URL explicite, choix mémorisé, préférences navigateur, puis langue source de secours (FR ici). Détection active sur l’installation et active par défaut sur une installation neuve. Les pondérations et variantes régionales du navigateur sont respectées. Pour chaque traduction d’une page : statut relu + titre traduit dans Langues. Un texte manquant reprend la source.

Les coordonnées, liens, médias, dates et dimensions sont communs. Les textes alternatifs/légendes des médias, slugs anglais et formulaires multilingues ne sont pas couverts par cette refonte. Les rôles cliente et la recette SEO/cache/production restent à préparer.

## Contrôles

- tests/multi.php : 94 contrôles, dont chaque champ configuré, lecture réelle get_field(), formatage riche, six langues, russe/cyrillique, variantes régionales, détection, URL, sauvegardes natives, formulaires incomplets et langues désactivées conservées. Brouillon temporaire supprimé en finally ; les langues supplémentaires sont injectées uniquement pendant le test.
- tests/smoke.php : 43 contrôles en lecture, dont anciens formats, menus et disciplines multi, description anglaise et textes fixes multi.
- JavaScript : syntaxe valide et interactions avec six onglets vérifiées sur DOM simulé ; pas de validation visuelle automatisée dans WordPress.
- Dix pages publiques FR/EN (accueil, À propos, Contact, une discipline et une œuvre) répondent en HTTP 200, sans enveloppe multi visible ni erreur PHP détectée dans leur HTML.
- Vérifier manuellement : onglets, textes repris, sauvegarde et réouverture, textes riches, menus ajoutés sans rechargement, descriptions de disciplines, footer et FR/EN publics.

Sauvegarde avant refonte : .backups/2026-10-30_134721. Restauration complète non testée. Ne pas supprimer les anciennes traductions tant que la recette n’est pas terminée. Revenir à l’ancien code après une écriture multi nécessite aussi de reconvertir ou restaurer les données.

Aucun fichier ni dossier supplémentaire n’a été créé pendant la généralisation : les fichiers du pilote ont été réutilisés. AUDIT.md reste un historique, pas une description de la version actuelle. FICHE-STAGE-LANGUES.md fournit l’explication destinée au responsable.
