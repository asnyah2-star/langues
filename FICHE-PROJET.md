# Fiche de suivi — Site de Tamara Szefer

## Repère actuel — 29 octobre 2026

Ce fichier conserve l’historique des choix ; plusieurs consignes anciennes ci-dessous ont été remplacées. Pour la reprise actuelle, lire d’abord `wp-content/plugins/atelier-langues/NOTES.md` et `FICHE-STAGE-LANGUES.md`.

Créations ouvre les disciplines ; `/oeuvres/` redirige vers l’accueil. `page.php` gère À propos et Contact ; les anciens templates spécifiques cités plus bas ne sont plus présents. Les coordonnées sont accessibles par les icônes du menu : le bloc e-mail/Instagram de Contact a été retiré. Le footer contient copyright, texte et menu facultatif. **Réglages du site** permet de modifier logo du menu, réseaux, e-mail et footer : son enregistrement synchronise l’e-mail ACF avec les icônes e-mail. L’accueil et Contact disposent de leurs logos indépendants. La version bilingue 0.2.0 fonctionne en local ; validation manuelle et préparation de production restent à terminer.

## Liens externes dans un nouvel onglet

L’attribut correct est `target="_blank"`, pas « about blank ». Les liens vers les sites externes des menus et des contenus de l’éditeur s’ouvrent désormais dans un nouvel onglet (ou une fenêtre selon le navigateur), avec `rel="noopener noreferrer"`. Les liens internes conservent la navigation habituelle et les liens `mailto:` ouvrent l’application de messagerie. Le lien Instagram de Contact porte aussi ces attributs. Les filtres de `functions.php` adaptent le HTML à l’affichage, sans réécrire les contenus enregistrés dans WordPress.

## Fiches créations — Description libre

Les liens automatiques « Se renseigner sur cette œuvre » et le lien YouTube spécifique au film ont été retirés du template `single-oeuvre.php`. La cliente rédige sa description dans l’éditeur principal de la création, affiché par `the_content()`. Elle peut y ajouter elle-même des liens. Aucun champ ACF supplémentaire n’est nécessaire pour ce texte. Cette décision remplace les anciennes mentions de ces liens automatiques dans l’historique.

## Menu Créations — Survol et disciplines

Le parent **Créations** est désormais un bouton sans lien vers la liste générale. À la souris, survoler le bouton ouvre les quatre disciplines. Le clic ou les touches Entrée/Espace permettent aussi de basculer le sous-menu, notamment sur téléphone ; Échap le ferme. Les disciplines restent des liens. Cette consigne remplace les mentions précédentes d’un parent Créations cliquable.

`functions.php` transforme uniquement l’entrée de la page `oeuvres` ayant des enfants en bouton. `navigation.js` gère le survol et le clic en maintenant `aria-expanded` à jour. La page générale existe toujours, mais elle n’est plus la destination du parent dans la navigation.

## Icônes dans la navigation — 23 octobre 2026

SoundCloud, LinkedIn et YouTube ont ensuite été ajoutés avec les adresses fournies. Le disque et Spotify ne sont pas affichés : aucune destination n’a été définie.

### Publier une création avec une image

Dans l’éditeur, une image mise en avant et une discipline ne constituent pas à elles seules le texte de la fiche. WordPress vérifie la présence d’un titre, d’un extrait ou d’un contenu pour rendre la fiche publiable. Pour notre usage : saisir le titre de la création, sélectionner l’image mise en avant, cocher la discipline, puis cliquer sur Publier et confirmer dans le panneau s’il apparaît. La description et les champs ACF facultatifs peuvent être complétés ensuite. Le titre du fichier image ne devient pas automatiquement le titre de la création. Si rien ne se passe malgré un titre renseigné, relever l’état du bouton et du panneau de publication avant toute modification du code.

Les icônes e-mail et Instagram sont ajoutées après Contact, dans un second menu WordPress nommé **Liens sociaux**, affecté à l’emplacement **Icônes après Contact**. Les SVG locaux viennent de `uploads/2026/icons`. Ils sont affichés comme images, sans intégrer leur code SVG dans le HTML. Les liens ont chacun un libellé lisible par les lecteurs d’écran et une zone cliquable d’au moins 44 px.

Pour modifier les adresses ou retirer un lien : **Apparence → Menus → Liens sociaux**. L’e-mail de ce menu se modifie dans son propre lien personnalisé ; il n’est pas synchronisé avec le champ e-mail de la page Accueil.

Pour ajouter un autre profil, créer un lien personnalisé avec une vraie adresse et un titre explicite. Dans les options d’écran des menus, activer **Classes CSS**, puis saisir une des classes suivantes dans l’entrée : `icon-email`, `icon-instagram`, `icon-linkedin`, `icon-soundcloud`, `icon-spotify`, `icon-youtube`, `icon-disc`. Le titre reste le nom accessible du lien. Sans classe reconnue, le texte du lien s’affiche normalement. Les autres profils attendent leurs destinations : aucune adresse n’a été inventée.

Le filtre `nav_menu_item_title` dans `functions.php` remplace le titre visible par l’image et un texte masqué visuellement pour ce seul menu. `header.php` affiche le menu après la navigation principale, et `.social-links` règle ses dimensions dans le CSS.

## Ajustement de présentation — Texte au-dessus du visuel

L’accueil affiche désormais le titre et l’introduction centrés, puis l’image ou le carrousel en dessous. Ce choix remplace la superposition du texte sur les images décrite dans les étapes précédentes. Les champs restent les mêmes pour conserver les sélections : le mode « Image de fond » désigne désormais l’image fixe sous le texte. Le voile reste réglable sur le visuel ; choisir 0 % pour voir ses couleurs intactes.

Pour modifier le texte : Pages → Accueil → Accroche et Introduction. Pour changer le visuel : le groupe Accueil — Image ou carrousel. Aucun texte n’est à modifier dans le PHP pour ces deux champs.

Dans `front-page.php`, `.hero-copy` précède maintenant `.hero-images`. Dans le CSS, `text-align: center` centre les textes ; les images suivent dans le flux normal, sans positionnement absolu derrière le texte. L’image fixe conserve ses proportions et s’affiche en entier. Le carrousel utilise une zone au ratio 2:1 avec `object-fit: contain` pour garder une hauteur stable sans couper les images. Les textes alternatifs de la médiathèque sont maintenant utilisés pour ces images visibles.

## À utiliser maintenant — Accueil administrable et disciplines (23 octobre 2026)

Cette section remplace l’ancien essai de fond avec une URL dans le CSS. Le choix de l’image et du mode se fait désormais dans WordPress. Le titre actuel et les textes existants sont conservés ; aucune description de l’artiste n’a été inventée.

### Ta liste de travail, dans l’ordre

1. **Médias → Bibliothèque** : retrouver `mosa.png`, déjà référencée dans la médiathèque et utilisée pour cet essai. Déposer un fichier dans `uploads` ne suffit pas à le faire apparaître dans WordPress : pour les prochains médias, privilégier **Médias → Ajouter**. WordPress crée alors sa fiche et les tailles adaptées.
2. **Pages → Accueil → Modifier** : retrouver le titre dans **Accroche** et remplir **Introduction** avec une courte présentation validée par l’artiste. Ce sont les champs existants, sans doublons.
3. Dans **Accueil — Image ou carrousel**, choisir **Image de fond**, puis sélectionner l’image. Pour cet essai, `mosa.png` et un voile clair de 35 % sont déjà enregistrés. Cliquer sur **Mettre à jour** après chaque changement.
4. Pour essayer le carrousel : importer plusieurs images dans Médias, choisir **Carrousel d’images**, puis sélectionner les images dans le champ qui apparaît. Faire glisser les éléments sélectionnés pour les réordonner ; utiliser le signe moins pour en retirer. Enregistrer. Les flèches permettent un défilement manuel, sans mouvement automatique. Avec une seule image, les commandes ne s’affichent pas ; sans image, le texte reste visible.
5. **Créations → Disciplines** : les termes **Arts plastiques**, **Musique**, **Cinéma**, **Performance** existent déjà. Leur description est facultative et s’affiche sur la liste correspondante.
6. **Créations → modifier une fiche** : cocher une ou plusieurs disciplines et enregistrer. Aucune discipline n’a été attribuée automatiquement aux créations existantes. Tant qu’une discipline n’a pas de création publiée, sa page affiche un message d’attente.
7. **Apparence → Menus** : le lien **Créations** ouvre la liste complète ; ses quatre enfants ouvrent les listes par discipline. Pour modifier l’ordre, déplacer les entrées puis enregistrer. La petite flèche ouvre le sous-menu indépendamment du lien parent ; Échap le ferme au clavier.
8. Vérifier l’accueil et le menu sur ton téléphone avec tes propres images et textes. Les images de fond remplissent le bloc avec `object-fit: cover` : leurs bords peuvent être coupés selon l’écran. Éviter d’y placer une information indispensable qui n’existe nulle part ailleurs.
9. **Étape ultérieure : bilingue FR/EN.** Il n’est pas installé à cette étape. Choisir la solution avant de multiplier les traductions ; il faudra traduire aussi les noms des disciplines, les menus, les champs et les textes fixes du thème.

### Les champs ajoutés, et pourquoi

| Nom utilisé en PHP | Type ACF | Rôle |
|---|---|---|
| `accueil_visuel_mode` | Liste de choix | `none`, `image` ou `carousel` |
| `accueil_image_fond` | Image renvoyant son identifiant | Choisir un média, sans recopier son URL |
| `accueil_carrousel` | Relation multiple renvoyant les identifiants | Sélectionner et ordonner des images de la médiathèque |
| `accueil_voile` | Curseur de 0 à 100 | Atténuer les images avec un voile clair |

Les champs inutiles au mode choisi sont cachés par les conditions ACF. Leur valeur reste enregistrée : passer d’un mode à l’autre n’efface pas la sélection précédente. Les groupes sont enregistrés dans la base et restent modifiables dans ACF ; sauvegarder la base en plus du thème.

### Suivre le code pas à pas

Dans `front-page.php`, `$mode` reçoit le choix enregistré. `if` et `elseif` décident quelle liste d’identifiants utiliser. `foreach` génère une diapositive par image. `wp_get_attachment_image()` retrouve le fichier, ses dimensions et ses tailles disponibles à partir de l’identifiant WordPress. Le CSS positionne ces images derrière le texte : aucune adresse `localhost` n’est nécessaire dans la feuille de style.

Le voile s’applique aux images, pas au texte. Un fond clair derrière les textes garde une lecture stable d’une image à l’autre. Les images du fond sont décoratives ; leurs contenus importants doivent être présentés dans les fiches créations. La présentation de l’accueil reste un texte saisi par l’artiste, quel que soit le mode.

`carousel.js` change uniquement l’image affichée quand on clique sur une flèche. Sans JavaScript, la première image reste affichée et les commandes restent cachées. `navigation.js` gère l’ouverture du sous-menu avec son bouton, son état `aria-expanded`, le clavier et le tactile. Sans JavaScript, le sous-menu reste accessible au survol et au focus grâce au CSS.

Dans `functions.php`, `register_taxonomy('discipline', 'oeuvre', ...)` relie le classement aux créations. **Discipline** est la taxonomie ; **Cinéma** est un terme de cette taxonomie. Une création peut avoir plusieurs termes. Il s’agit d’une classification, pas d’un champ de description ni d’une condition PHP.

`taxonomy-discipline.php` utilise la requête principale préparée par WordPress pour la discipline visitée. `page-oeuvres.php` prépare la liste complète. Les deux appellent **`loop-creations.php`**, qui contient une seule fois le HTML des cartes et la pagination. Les listes affichent jusqu’à 12 créations par page.

### Les noms visibles et les identifiants techniques

Le menu, le titre de la liste et la rubrique d’administration affichent désormais **Créations**. L’identifiant interne du type reste `oeuvre`, les champs existants restent `oeuvre_...`, les fiches restent sous `/oeuvre/...` et la liste reste sous `/oeuvres/`. Cela conserve les données et les adresses existantes. Le fichier `single-oeuvre.php` reste donc le bon template. Un changement des URL pourra être traité séparément avec les redirections adaptées.

### Ce qui est volontairement laissé à remplir

La courte présentation, les autres médias, l’ordre du carrousel, les descriptions des disciplines et le classement des créations se complètent dans l’administration. Aucun média externe, lecteur audio/vidéo ni traduction automatique n’a été ajouté.

### Contrôles de cette étape

Syntaxe PHP et JavaScript vérifiée. Accueil, liste complète et quatre disciplines répondent en HTTP 200. Les cas sans image, carrousel vide, une image et deux images ont été contrôlés ; le mode image fixe est restauré après les essais. Dans Chrome, vérifications aux largeurs 1280, 390 et 320 px : pas de débordement horizontal, menu ouvrable et refermable avec Échap, flèches du carrousel et compteur fonctionnels. Une copie des données concernées a été conservée dans le dossier temporaire Windows avant les changements ; elle ne remplace pas une sauvegarde complète du site.

## Atelier PHP du 23 octobre 2026 — Une structure partagée

Cette partie décrit la structure actuelle et remplace les anciennes références aux fichiers `page-a-propos.php` et `page-contact.php` dans l’historique ci-dessous. Ces deux fichiers ont été regroupés dans `page.php`, puis retirés. Les pages WordPress et leurs contenus sont conservés. Aucun champ ni article supplémentaire n’est nécessaire pour ce regroupement.

### 1. Le parcours d’une demande

Quand on ouvre `/contact/`, WordPress retrouve la page dans la base de données puis choisit un template. Sans fichier `page-contact.php`, il utilise ici `page.php`. Ce fichier appelle le header, affiche le contenu de la page dans une boucle, puis appelle le footer. Même fonctionnement pour À propos. Garder les slugs `contact` et `a-propos` : les conditions les utilisent.

L’accueil garde `front-page.php`, la liste des œuvres garde `page-oeuvres.php`, une œuvre garde `single-oeuvre.php`. Ces présentations ont des rôles distincts. Le but est de partager les parties communes sans accumuler toutes les pages dans un seul fichier.

### 2. Comprendre la boucle principale, même pour une seule page

```php
<?php while (have_posts()) : the_post(); ?>
    <h1><?php the_title(); ?></h1>
    <?php the_content(); ?>
<?php endwhile; ?>
```

- `have_posts()` demande s’il reste un contenu à parcourir dans le résultat préparé par WordPress.
- `while` répète le bloc tant que la réponse est vraie.
- `the_post()` passe au contenu suivant et prépare ses données pour les fonctions d’affichage. Il ne crée ni ne publie aucun article.
- `the_title()` affiche le titre du contenu courant ; `the_content()` affiche son contenu mis en forme.
- `endwhile` ferme la boucle. Pour une page ordinaire, cette boucle parcourt généralement un seul contenu. Le mot « post » des fonctions WordPress inclut ici les pages, pas seulement les articles du blog.

### 3. Comprendre les conditions de notre page commune

```php
$is_about = is_page('a-propos');
$is_contact = is_page('contact');
```

Ces variables valent `true` (vrai) ou `false` (faux). Elles représentent la page consultée. `if ($is_about)` affiche le portrait et la présentation ; `else` affiche le contenu des autres pages. À l’intérieur de cette seconde branche, `if ($is_contact)` ajoute l’introduction et les coordonnées de Contact. Une autre page utilise simplement son titre et son contenu.

Le titre, la boucle et les appels au header/footer sont communs. La mise en page du portrait reste propre à À propos. On a conservé deux endroits pour `the_content()`, car le texte d’À propos doit rester à l’intérieur de sa colonne `.prose`. Un seul de ces endroits s’exécute pour une page donnée.

```php
$page_class = $is_about ? 'about-page' : ($is_contact ? 'contact-page' : '');
```

Le symbole `?` signifie « si vrai, prendre cette valeur », et `:` « sinon, prendre celle-ci ». Cette ligne choisit la classe CSS du `<main>` : `about-page`, `contact-page`, ou une chaîne vide. Les styles existants continuent donc à s’appliquer. `esc_attr()` protège la valeur lorsqu’elle est écrite dans un attribut HTML.

### 4. Une autre boucle : foreach

La présentation d’À propos possède deux sections facultatives : démarche et parcours. `foreach` parcourt un tableau qui associe chaque nom de champ ACF à un titre. À chaque tour, on lit le champ puis `if ($text)` vérifie qu’il est rempli avant d’afficher la section. On écrit le HTML de la section une seule fois pour les deux champs.

Différence : `while (have_posts())` parcourt les contenus WordPress ; `foreach` parcourt ici notre propre liste de champs.

### 5. Page, article, œuvre et single

- Une **page** convient aux informations stables : À propos, Contact.
- Un **article** est un contenu de blog ou d’actualité. Notre regroupement n’en nécessite pas.
- Une **œuvre** utilise le type de contenu personnalisé `oeuvre`, déclaré dans `functions.php`. Créer ses fiches dans Œuvres, pas dans Articles.
- **single** signifie la vue d’un contenu individuel. `single-oeuvre.php` affiche donc une œuvre, tandis que `page-oeuvres.php` utilise une requête `WP_Query` et une boucle pour en afficher plusieurs.
- Un champ **ACF** complète un contenu avec une information précise. Utiliser les champs déjà présents ; ne pas en créer seulement pour remplacer du HTML.

### 6. Petit exercice pour refaire le raisonnement

1. Ouvrir `page.php` et repérer le header, le `<main>`, la boucle et le footer.
2. Suivre mentalement `/a-propos/` : quelles variables sont vraies ? Quelle branche s’affiche ?
3. Refaire avec `/contact/`, puis une page ordinaire : quel HTML est commun ?
4. Dans WordPress, modifier une phrase de Contact, enregistrer et vérifier le site. Le template reste le même ; seules les données changent.

Le lien « Aller au contenu » dans le header est un raccourci clavier : son CSS le cache habituellement et le montre au focus. Tester avec Tab puis Entrée ; `href="#contenu"` vise le `<main id="contenu">`. Il est conservé pour l’accessibilité.

Cette étape ne modifie ni le CSS, ni les champs en base, ni les liens des fiches œuvres. Les prochains regroupements doivent répondre à une répétition réelle, pas seulement réduire le nombre de fichiers.

## Mise à jour du 23 octobre 2026 — Footer simplifié

Les liens e-mail et Instagram ont été retirés du footer sur toutes les pages. Le copyright, le texte du footer et son menu facultatif sont conservés. Les anciennes mentions de coordonnées dans le footer ci-dessous décrivent l’état précédent.

Les coordonnées restent sur la page Contact. Le champ `contact_email` se remplit toujours sur Accueil et reste utilisé par Contact. Les pictogrammes dans le menu sont prévus par toi, mais ne sont pas ajoutés à cette étape. La modification de destination du lien existant « Se renseigner sur cette œuvre » a été annulée : les fiches œuvres conservent leur code précédent.

## 1. Où nous en sommes

Le site est en local sur ton ordinateur. Les modifications actuelles ne publient pas le site sur Internet.

Tu avais créé le thème, la page Accueil et des champs ACF. Pendant notre travail, l’assistant a aussi créé les pages Œuvres, À propos et Contact, les templates associés et de nouveaux groupes ACF. Tu n’as donc pas à les recréer. Nous allons les découvrir et les remplir progressivement.

Le logo original, les photographies d’œuvres et les textes validés restent à fournir. Le rose actuel est provisoire.

## 2. Les quatre notions à distinguer

| Élément | À quoi il sert | Exemple |
|---|---|---|
| Page WordPress | Enregistre un contenu dans la base de données | La page « À propos » |
| Template PHP | Décide où et comment afficher les contenus | `page-a-propos.php` |
| Champ ACF | Enregistre une information précise à remplir dans l’administration | Le champ `accroche` contient « Tendre Chaos » |
| CSS | Règle l’apparence : couleurs, tailles, positions, espacements | La mosaïque en fond de l’accueil |

Une page WordPress n’est donc pas un fichier PHP. WordPress associe la page à un fichier du thème pour fabriquer son affichage.

## 3. Le rôle de chaque page du site

| Page visible | Son rôle | Où saisir son contenu |
|---|---|---|
| Accueil | Présenter l’univers et donner accès aux œuvres | Pages → Accueil → Modifier |
| Œuvres | Montrer la liste des œuvres publiées | Pages → Œuvres pour l’introduction ; rubrique Œuvres pour les créations |
| À propos | Présenter l’artiste et sa démarche | Pages → À propos → Modifier |
| Contact | Donner accès à l’e-mail et à Instagram | Pages → Contact pour l’introduction ; Accueil pour l’e-mail partagé |
| Politique de confidentialité | Présenter les informations relatives aux données personnelles | Page existante en brouillon, à préparer selon les fonctionnalités finales |

Une **fiche œuvre** présente une seule création. Ce n’est pas une page ordinaire : elle se crée dans la rubrique **Œuvres** du menu de gauche. La page Œuvres rassemble automatiquement les fiches publiées.

## 4. Les fichiers du thème

Ils se trouvent dans `wp-content/themes/atelier-artiste/`.

| Fichier | Fonction |
|---|---|
| `header.php` | Ouvre le document HTML et affiche le nom de l’artiste et la navigation |
| `footer.php` | Affiche le copyright, le texte du footer, l’e-mail et Instagram, puis ferme le document |
| `front-page.php` | Affiche l’accueil : accroche, introduction, image mise en avant et contenu |
| `page-oeuvres.php` | Affiche l’introduction de la page Œuvres, puis la grille automatique |
| `single-oeuvre.php` | Affiche une œuvre : titre, image, description, année, technique et dimensions |
| `page-a-propos.php` | Affiche la présentation de l’artiste et son portrait |
| `page-contact.php` | Affiche l’introduction, l’e-mail et le lien Instagram |
| `page.php` | Sert aux autres pages qui n’ont pas de template spécifique |
| `index.php` | Fichier de secours utilisé si WordPress ne trouve pas de template plus précis |
| `functions.php` | Charge le CSS, déclare les menus et la rubrique Œuvres, fournit des fonctions partagées |
| `style.css` | Contient la présentation du thème et les règles graphiques |
| `GUIDE.md` | Repère technique complémentaire ; ce fichier n’est pas une page du site |

Les fichiers `page-oeuvres.php`, `page-a-propos.php` et `page-contact.php` dépendent des identifiants d’URL (slugs) `oeuvres`, `a-propos` et `contact`. Pour le moment, conserver ces identifiants.

### Comment les morceaux s’assemblent

Dans un template, `get_header()` charge `header.php`. Ensuite vient le contenu principal dans `<main>`. Enfin, `get_footer()` charge `footer.php`.

Le header et le footer sont partagés par les pages. Il n’est pas nécessaire de créer une page WordPress « Header » ou « Footer ».

## 5. Les champs ACF existants

Un **groupe** rassemble plusieurs champs et définit l’écran sur lequel ils apparaissent. Le **libellé** est le nom lisible dans WordPress ; le **nom du champ** est utilisé par le code. Conserver les noms ci-dessous pour que le code retrouve les valeurs.

| Écran où remplir le champ | Nom du champ | Utilité |
|---|---|---|
| Accueil | `accroche` | Titre principal, actuellement « Tendre Chaos » |
| Accueil | `accueil_intro` | Quelques lignes de présentation sous le titre |
| Accueil | `footer_texte` | Texte partagé dans le pied de page, par exemple « Artiste à Bruxelles » |
| Accueil | `contact_email` | Adresse utilisée dans le footer et la page Contact |
| À propos | `artiste_intro` | Courte présentation de l’artiste |
| Contact | `contact_intro` | Phrase d’introduction de la page Contact |
| Une fiche œuvre | `oeuvre_annee` | Année de création |
| Une fiche œuvre | `oeuvre_technique` | Technique et matériaux |
| Une fiche œuvre | `oeuvre_dimensions` | Dimensions avec unité, ou « dimensions variables » |

Les champs du footer sont placés sur Accueil par simplicité. Le code les lit à cet endroit même lorsque le visiteur consulte une autre page.

Les biographies et descriptions longues se rédigent dans l’éditeur WordPress. Les images principales se choisissent dans **Image mise en avant** : nous n’avons pas créé de champ ACF supplémentaire pour ces images.

## 6. Le menu

Dans **Apparence → Menus**, le menu associé à l’emplacement **Navigation principale** contient Accueil, À propos, Œuvres et Contact. Les quatre liens sont gérés par WordPress. Aucun bouton Contact n’est ajouté séparément dans `header.php`.

Une balise `<a>` est un lien. Le menu conduit à la page Contact ; le lien « Se renseigner sur cette œuvre » d’une fiche conduit au footer de la fiche avec `#contact`.

## 7. Quelques repères de code

```php
$accroche = atelier_artiste_field('accroche');
```

Lit la valeur du champ ACF et la place dans une variable. Cette fonction du thème appelle `get_field()` si ACF est disponible et renvoie un texte vide sinon. Une variable est un nom qui représente une valeur pendant l’exécution du code.

```php
echo esc_html($accroche);
```

Affiche la valeur comme du texte en protégeant les caractères HTML.

```html
<p class="footer-description">Artiste à Bruxelles</p>
```

`p` signifie paragraphe. `class` donne un nom à cet élément pour pouvoir le cibler dans le CSS.

```css
.footer-description { font-size: 14px; }
```

Le point désigne une classe. `font-size` change la taille du texte.

```css
.site-navigation a:hover {
    text-decoration: underline;
    text-decoration-color: var(--pink);
}
```

Cet exemple souligne les liens du menu en rose au survol. `--pink` est la couleur définie au début du CSS dans `:root`.

## 8. Parcours pas à pas

Faire une seule étape, enregistrer, vérifier, puis passer à la suivante.

- [ ] **Étape 1 — Repérer les pages.** Ouvrir Pages → Toutes les pages et retrouver Accueil, Œuvres, À propos et Contact. Ne rien recréer.
- [ ] **Étape 2 — Comprendre Accueil.** Ouvrir Accueil → Modifier, retrouver Accroche et Introduction. Modifier seulement l’introduction, enregistrer et regarder le résultat sur le site.
- [ ] **Étape 3 — Comprendre le menu.** Ouvrir Apparence → Menus, repérer les quatre entrées et leur emplacement. Tester leurs liens sur le site.
- [ ] **Étape 4 — Remplir À propos.** Écrire une introduction, une biographie validée et choisir un portrait en image mise en avant. Enregistrer et vérifier.
- [ ] **Étape 5 — Ajouter une première œuvre.** Ouvrir Œuvres → Ajouter une œuvre. Renseigner titre, image, description et les trois champs ACF. Publier, puis vérifier la grille et la fiche individuelle.
- [ ] **Étape 6 — Vérifier Contact.** Relire l’introduction et tester les liens e-mail et Instagram. L’e-mail ouvre une application de messagerie : il n’y a pas de formulaire d’envoi installé.
- [ ] **Étape 7 — Préparer la présentation.** Ajouter plusieurs œuvres réelles, le logo original et les textes validés. Vérifier les pages sur ordinateur et téléphone.

## 9. Habitudes utiles

- Enregistrer dans WordPress après avoir modifié un contenu.
- Enregistrer le fichier dans l’éditeur après avoir modifié du code.
- Recharger le navigateur pour voir le résultat ; utiliser Ctrl + F5 si l’ancien CSS reste affiché.
- Faire une petite modification à la fois et vérifier son effet.
- Sauvegarder le thème **et la base de données** : les pages, les valeurs ACF et les groupes ACF sont enregistrés dans la base.

## 10. Derniers ajustements

- Le bouton « Découvrir les œuvres » a été retiré de l’accueil. L’accès aux œuvres se fait par le menu.
- Les coordonnées restent dans le footer. Contact possède aussi sa page et son entrée dans le menu ; les liens de renseignement des fiches œuvres utilisent `#contact` pour rejoindre le footer.
- Dans **Pages → À propos → Modifier**, le groupe ACF de présentation contient maintenant aussi **Démarche artistique** (`artiste_demarche`) et **Parcours et expositions** (`artiste_parcours`). Ces deux champs acceptent des paragraphes et des listes. Ils sont facultatifs : une section vide n’apparaît pas sur le site.
- La biographie principale reste dans l’éditeur WordPress de la page À propos ; l’introduction courte reste dans `artiste_intro`. Éviter de recopier le même texte dans plusieurs endroits.

## 11. État actuel après les derniers choix

Cette section remplace les indications précédentes concernant Contact et le menu.

- Contact est désormais une page publiée. Son lien est géré dans **Apparence → Menus**, comme Accueil, Œuvres et À propos. Aucun lien Contact supplémentaire n’est ajouté dans le header.
- Les deux coordonnées de la page Contact sont des liens simples avec le même style.
- L’accueil conserve une hauteur minimale de **80vh**.
- Les titres Œuvres, À propos, Contact et les titres des fiches œuvres utilisent **28px**. Les titres de la grille utilisent **18px**.
- Le portrait de la page À propos est limité à **480px** de hauteur. Image et texte sont centrés verticalement avec `align-items: center` dans `.about-layout`.
- Le lien YouTube de « Les Reines Invisibles » est actuellement spécifique à cette fiche et se trouve dans `single-oeuvre.php`.
- Les styles des anciens boutons roses et de l’ancien titre du footer ont été retirés, car ces éléments ne sont plus utilisés.

Pour reprendre : ajouter une œuvre dans la rubrique **Œuvres**, avec son titre, son image mise en avant, sa description et uniquement les informations ACF connues. Aucun nouveau champ n’est nécessaire pour cela.

## 12. Essai de mosaïque sur l’accueil — 22 octobre 2026

La mosaïque est appliquée à `.home-hero`, le bloc qui contient le titre « Tendre Chaos ». Ce n’est pas le fond de toute la page. Le réglage se trouve à la fin de `style.css`, sous le commentaire « Mosaïque en fond de l’accueil ».

L’image utilisée par le CSS est actuellement `tamosa.png` dans la médiathèque, et non `tamosa2.png`. Pour changer d’image : Médias → sélectionner l’image → copier son URL → remplacer uniquement l’adresse dans `url("...")`.

| Réglage actuel | Ce qu’il fait | Comment expérimenter |
|---|---|---|
| `background-size: 105% auto` | Largeur de l’image égale à 105 % du bloc ; hauteur proportionnelle | Essayer 100 %, 105 %, 110 % ; augmenter accentue le recadrage |
| `background-position: center` | Centre l’image dans le bloc | Essayer `center top` pour privilégier le haut |
| `background-repeat: no-repeat` | Empêche la répétition de la mosaïque | Conserver pour cet essai |
| `rgba(252, 250, 248, 0.25)` deux fois | Ajoute un voile de la couleur du papier, à 25 % | Modifier les deux valeurs : 0.15 atténue moins, 0.40 atténue plus |
| `padding: clamp(64px, 10vw, 150px) 24px` | Espace intérieur : haut/bas variable, gauche/droite 24 px | Le padding contribue à la hauteur du bloc |

`linear-gradient()` est ici un voile uniforme : ses deux couleurs sont identiques. Il est placé avant l’image pour apparaître par-dessus. Le texte reste opaque. `opacity` sur `.home-hero` rendrait aussi ses textes transparents.

Les petits fonds clairs derrière les textes sont gérés par les règles juste après le fond. `width: fit-content` adapte leur largeur au texte, dans la limite de la place disponible. `padding: 8px 16px` ajoute de l’espace à l’intérieur de ces fonds.

### Pourquoi l’image peut être coupée

`cover` remplit le bloc sans déformer l’image, quitte à en couper une partie lorsque leurs proportions diffèrent. `contain` montre toute l’image mais peut laisser des espaces libres. `105% auto` conserve aussi les proportions : les côtés dépassent légèrement, et des espaces peuvent apparaître en hauteur. Le résultat dépend donc aussi de la taille de l’écran.

La hauteur minimale `80vh` concerne `.home-main`, l’ensemble de l’accueil, et non `.home-hero`. Le fond ne couvre donc pas automatiquement toute cette hauteur.

### Comprendre les règles qui se répètent

Il existe déjà une règle `.home-hero` au début du CSS et une règle mobile au milieu. La dernière règle du fichier remplace leurs propriétés communes, notamment `padding`. Ce n’est pas une erreur de syntaxe : à priorité égale, la dernière déclaration gagne. Pour poursuivre cet essai, modifier le bloc final existant plutôt que le recopier à chaque fois. Le padding mobile de 64 px est actuellement remplacé par le padding final.

### Autres méthodes dans WordPress

1. **Bloc Bannière (Cover)** : dans Pages → Accueil → Modifier, ajouter un bloc Bannière, choisir une image de la médiathèque et régler le voile, son opacité, le point focal et le texte dans l’éditeur. Dans ce thème, le contenu de l’éditeur est affiché sous le hero, dans `.home-content`, limité à 780 px. Une bannière ajoutée ici ne remplace donc pas automatiquement le fond derrière « Tendre Chaos » : cela demanderait une adaptation du template si on choisit cette méthode.
2. **Image mise en avant** : elle est déjà prise en charge sur Accueil et apparaît sous le hero. C’est une image visible avec ses proportions, pas un arrière-plan derrière le titre.
3. **Apparence → Personnaliser → Image d’arrière-plan** : cette option des thèmes classiques nécessite le support `custom-background`. Il n’est pas activé dans notre thème. Elle concerne le fond général du site ; elle n’a pas été ajoutée pour cet essai.

Documentation : [Bannière et réglages du voile](https://wordpress.com/support/wordpress-editor/blocks/cover-block/style-a-banner/) ; [arrière-plan des thèmes classiques](https://developer.wordpress.org/themes/classic-themes/functionality/custom-backgrounds/).

### Vérifications avant présentation

- Vérifier que l’image se charge et que le texte reste lisible sur ordinateur et téléphone.
- Tester les quatre liens du menu, une fiche œuvre et les coordonnées.
- Ajouter les textes alternatifs utiles au portrait et aux images des œuvres dans la médiathèque. Le fond CSS est décoratif : il n’a pas d’attribut `alt` ; les œuvres doivent aussi être présentées dans leurs fiches.
- Relire la biographie et retirer les paragraphes vides dans l’éditeur.
- Avant une mise en ligne, adapter l’URL locale `http://localhost/tamaraszefer/...` du fond au domaine final, ou rendre sa gestion indépendante du domaine.

La relecture des fichiers ne remplace pas une vérification visuelle sur téléphone. Conserver une sauvegarde du thème et de la base avant le transfert.
