# Atelier Langues — carnet d’apprentissage

## Point de reprise vérifié — 29 octobre 2026

**Conclusion : la version 0.2.0 est utilisable pour poursuivre les essais locaux. Nous nous sommes arrêtés à la préparation de la livraison et de l’autonomie de la cliente. La recette des formulaires, du mobile et du clavier reste à terminer avant une validation de production.** Les exercices et constats plus bas sont un historique, pas une liste d’actions encore à refaire.

### Ce qui existe aujourd’hui

- Le fichier principal charge **quatre modules**, dont `site-settings.php` ajouté après les trois modules initiaux.
- Titres, contenus, extraits et champs ACF textuels simples : français existant conservé, anglais dans `_atelier_langues_en`, même ID, médias partagés. Une case et un titre anglais déterminent la disponibilité ; un champ anglais vide reprend le français.
- Menus, noms des disciplines et **22 textes fixes** du thème sont traduisibles. URL `/fr/` et `/en/`, sélecteur sur les contenus et disciplines ; choix manuel mémorisé. Détection navigateur **activée en base**.
- **Réglages du site** centralise logo du menu, e-mail, réseaux et footer bilingue. Le guide de saisie des créations est déjà intégré. Les logos d’Accueil et de Contact restent dans leurs pages.
- Le parent **Créations** ouvre les disciplines. L’ancienne page `/oeuvres/` redirige vers l’accueil ; le template `page-oeuvres.php` n’existe plus. Inutile de reconstruire cette liste sans nouvelle décision.

### Vérifications de cette séance

- Banc existant : **39 vérifications réussies**, avec traductions fictives en mémoire, sans enregistrer de traduction.
- Syntaxe PHP : fichiers du plugin et du thème valides sous PHP CLI 8.3.28. Syntaxe JavaScript : `navigation.js`, `carousel.js`, `images.js` valides sous Node.
- **24 requêtes HTTP contrôlées** : accueil, À propos, Contact, quatre disciplines et Vision nocturne répondent 200 dans les deux langues ; `/fr/oeuvres/` et `/en/oeuvres/` redirigent en 302 vers leur accueil respectif. Sans préfixe, navigateur anglais → `/en/`, cookie français → `/fr/` ; `/en/` explicite reste en 200 malgré navigateur/cookie français. Le clic EN émet un cookie et redirige sans `atelier_choice`. URL inconnue → 404 ; `.backups/` → 403. Aucune erreur PHP visible dans ces réponses. Deux lectures supplémentaires confirment `<html lang="fr">` et `<html lang="en">` sur l’accueil. Les cinq autres fiches de créations n’ont pas été retestées en HTTP pendant cette revue.
- Lecture de la base : les quatre pages et les six créations publiées sont disponibles en anglais ; l’article de démonstration ne l’est pas. Contrairement à l’ancienne revue, Créations (ID 20) est maintenant disponible, mais sa redirection reste volontaire.
- Deux textes fixes sans anglais : **Agrandir l’image** et **Fermer l’image**. Ils sont à saisir dans Textes du thème. Le catalogue historique de 20 textes a donc grandi à 22.
- Champs non vides reprenant le français : contenu de la création **Quelques créations** (ID 108) et signature de Contact (`contact_signature`). Relire le contenu 108 : un contenu composé d’images peut être partagé volontairement. Décider aussi si la signature doit rester identique. Les autres contenus/extraits et champs ACF simples non vides des fiches disponibles ont une valeur anglaise ; cela ne juge pas leur qualité.
- La discipline Cinéma est encore nommée `movie theater` en anglais : faire valider **Cinema** ou **Film**, et harmoniser son libellé de menu. `visual arts` commence actuellement par une minuscule : harmonisation éditoriale à envisager.
- **18 images sur 18** ont un alt vide en médiathèque. Cela ne signifie pas que toutes doivent recevoir du texte : distinguer images informatives et décoratives. Voir `IMAGES-A-RELIRE.md`.
- Sauvegarde du 28 octobre présente : SQL, ZIP et manifeste dans `.backups/2026-10-28_153740/`. Présence constatée ; empreintes et restauration non revérifiées pendant cette séance. Elle ne couvre pas les notes mises à jour aujourd’hui.
- L’environnement CLI affiche un avertissement Xdebug : impossible d’ouvrir son journal dans `c:/wamp64/logs/`. Les contrôles réussissent malgré cet avertissement ; aucun réglage Wamp n’a été changé.

### Améliorations conseillées, dans l’ordre

1. **Préserver les traductions lors d’un formulaire incomplet.** Dans `admin.php`, la sauvegarde reconstruit le tableau anglais depuis zéro. Les champs ACF absents de l’envoi, ou devenus invisibles après un changement de groupe, peuvent disparaître de la traduction. Partir des valeurs existantes et remplacer uniquement les clés effectivement reçues ; distinguer champ absent et champ volontairement vidé. Le formulaire des textes fixes applique déjà ce principe. À corriger avec un test ciblé avant d’élargir les formulaires.
2. **Renforcer la protection contre les modifications simultanées.** Le contrôle de `post_modified_gmt` protège partiellement le formulaire principal, mais les écritures ACF/métadonnées et le footer depuis Réglages du site n’ont pas de version commune. Deux écrans peuvent remplacer leurs changements. Prévoir une version de traduction contrôlée par les deux parcours, puis tester un conflit réel ; éviter les onglets d’édition parallèles en attendant.
3. **Faire la recette réelle des sauvegardes et des droits.** Enregistrer/recharger une modification réversible de contenu, menu, discipline, texte fixe, logo et footer ; vérifier FR/EN puis rétablir les valeurs souhaitées. Tester un compte à droits réduits. Les formulaires réservés à l’administration ne sont pas encore un parcours cliente éditrice.
4. **Améliorer la saisie ensuite.** Un éditeur visuel bilingue et un état des traductions manquantes aideraient davantage la cliente que de nouveaux champs. Garder les valeurs existantes et ne pas annoncer une couverture totale des blocs ou d’ACF complexe.
5. **Terminer la livraison.** Mobile, clavier, carrousel, dialogue d’image ; alt informatifs, restauration d’une sauvegarde à jour, HTTPS/cache, SEO des archives et sitemaps. Déplacer à terme la déclaration du type `oeuvre` et de sa taxonomie hors du thème pour conserver leur administration lors d’un changement de thème.

Cette séance met à jour la documentation ; aucun code fonctionnel ni contenu enregistré n’est modifié. Le rendu graphique et les sauvegardes depuis un navigateur connecté ne sont pas déclarés validés.

## Séance du 28 octobre 2026 : état initial

Objectif : construire progressivement une extension français/anglais, en écrivant et comprenant chaque étape.

Constat : `atelier-langues.php`, `readme.txt` et `../assets/admin.js` sont vides. Le thème affiche déjà les titres et contenus WordPress. Il utilise aussi ACF pour certains champs et déclare le type de contenu `oeuvre`.

Le thème gère la présentation. L’extension gérera les données et le comportement des traductions.

## Choix retenu : extension maison sans API payante

- Pour travailler avec Codex sous mon compte : me connecter moi-même à mon compte dans le client utilisé. L’assistant ne peut pas identifier avec certitude le titulaire de la session ni changer de compte à ma place.
- Les traductions seront saisies et enregistrées manuellement dans WordPress. L’extension permettra de les gérer et de les afficher selon la langue choisie.
- Aucune API payante, clé OpenAI ou connexion à un compte ChatGPT n’est prévue pour le fonctionnement du plugin. Il ne traduira pas automatiquement les textes.

Documentation pour la connexion personnelle à Codex : https://learn.chatgpt.com/docs/auth

## Progression envisagée

1. Faire reconnaître l’extension par WordPress.
2. Ajouter et sauvegarder un seul champ anglais sur un contenu de test, avec les vérifications WordPress nécessaires.
3. Afficher ce champ selon la langue choisie, avec retour au français si la traduction manque.
4. Étendre aux contenus et champs réellement nécessaires, puis aux menus et textes du thème.
5. Évaluer les pages traduites séparées et leurs URL avant de construire un mini-CMS : des champs bilingues ne deviennent pas automatiquement des pages liées.
6. Tester la saisie et l’affichage des traductions sans service payant.

Conserver le titre et le contenu WordPress actuels comme français évite de les recopier immédiatement dans de nouveaux champs `_fr`.

## Exercice 1 — à réaliser moi-même

Dans `atelier-langues.php`, saisir :

```php
<?php
/**
 * Plugin Name: Atelier Langues
 * Description: Gestion progressive des contenus en français et en anglais.
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}
```

Explications : `<?php` ouvre le code PHP ; l’en-tête permet à WordPress de reconnaître l’extension ; la condition empêche de poursuivre si le fichier est appelé en dehors du chargement de WordPress. Pas de balise PHP fermante nécessaire ici.

Vérification : enregistrer puis ouvrir **Extensions → Extensions installées**. Vérifier que « Atelier Langues » apparaît. L’activer ne doit produire aucune sortie ni changer l’affichage du site. Cet exercice ne crée encore aucun champ et ne traduit rien.

## Arborescence corrigée

Les fichiers vides `admin.js` et `admin.css` ont été déplacés depuis `plugins/assets/` vers `plugins/atelier-langues/assets/`. L’ancien dossier `plugins/assets/`, devenu vide, a été supprimé. Les fichiers PHP dans `includes/` existaient déjà et sont conservés. Aucun code fonctionnel n’a été ajouté.

```text
wp-content/plugins/atelier-langues/
├── atelier-langues.php
├── includes/
│   ├── admin.php
│   ├── languages.php
│   └── content.php
├── assets/
│   ├── admin.css
│   └── admin.js
├── readme.txt
└── NOTES.md
```

Le thème `atelier-artiste` conserve tous ses fichiers existants : la liste simplifiée du projet ne signifie pas qu’il faut supprimer ses autres modèles.

État de l’exercice 1 : code présent dans le fichier ; activation réussie confirmée par l’utilisateur.

## Exercice 2 — une page dans l’administration

Objectif : comprendre comment le fichier principal charge un autre fichier et comment un hook WordPress ajoute un menu. Étape préparatoire avant les champs bilingues.

À ajouter après la protection ABSPATH dans `atelier-langues.php` (sans remettre `<?php`) :

```php
require_once plugin_dir_path(__FILE__) . 'includes/admin.php';
```

`__FILE__` désigne le fichier courant ; `plugin_dir_path()` retrouve son dossier ; le point assemble les deux chaînes ; `require_once` charge le fichier une seule fois.

À écrire dans le fichier vide `includes/admin.php` :

```php
<?php
if (!defined('ABSPATH')) {
    exit;
}

function atelier_langues_add_admin_menu() {
    add_options_page(
        'Atelier Langues',
        'Atelier Langues',
        'manage_options',
        'atelier-langues',
        'atelier_langues_render_admin_page'
    );
}
add_action('admin_menu', 'atelier_langues_add_admin_menu');

function atelier_langues_render_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    echo '<div class="wrap">';
    echo '<h1>Atelier Langues</h1>';
    echo '<p>Le site proposera des contenus en français et en anglais.</p>';
    echo '</div>';
}
```

`add_action()` enregistre la fonction à appeler lorsque WordPress prépare le menu d’administration. `add_options_page()` ajoute une entrée dans Réglages : titre de page, texte du menu, droit requis, identifiant unique, puis fonction d’affichage. Le préfixe `atelier_langues_` réduit les risques de collision avec une autre extension. La fonction d’affichage vérifie aussi le droit d’accès. Les textes HTML sont fixes ; les futures valeurs saisies devront être échappées à l’affichage.

Vérification à faire : enregistrer les deux fichiers, recharger l’administration puis ouvrir **Réglages → Atelier Langues** avec un compte administrateur. Le titre et la phrase doivent apparaître. Aucune donnée n’est encore enregistrée. Il n’est pas nécessaire de désactiver puis réactiver le plugin.

État de l’exercice 2 : proposé, à écrire et à vérifier par l’utilisateur.

## Nouvelle étape : audit et première implémentation 0.2.0

Le cahier des charges joint demande désormais de commencer l’implémentation après l’audit. Les constats, choix, limites et tests sont détaillés dans [AUDIT.md](AUDIT.md).

- `atelier-langues.php` charge les trois modules et gère l’actualisation ponctuelle des permaliens.
- `includes/content.php` définit le stockage lié au même ID et les filtres d’affichage WordPress/ACF.
- `includes/languages.php` gère les URL, la langue active, les liens, les redirections, le cookie et le sélecteur.
- `includes/admin.php` affiche les colonnes français/anglais et reçoit les formulaires sécurisés.
- `assets/admin.css` adapte le tableau aux petits écrans. Aucun JavaScript nécessaire pour cette étape.
- `tests/smoke.php` teste en mémoire les fonctions principales sans enregistrer de traductions.
- Le thème n’a reçu que le sélecteur et l’adaptation des liens de retour à l’accueil dans `header.php`.

Notions à étudier dans cet ordre : métadonnées liées à un ID ; hooks de lecture ; formulaire `admin-post.php` ; capacités et nonce ; assainissement à l’entrée et échappement à la sortie ; query vars et règles de réécriture. La validation manuelle de l’enregistrement est la prochaine action à effectuer ensemble.

## Premier enregistrement vérifié : Accueil → Home

L’utilisateur confirme voir les champs. Vérification en base : le titre anglais « Home » est enregistré et le français « Accueil » est conservé. La disponibilité anglaise n’est pas cochée : le sélecteur affiche donc uniquement Français. Un message de statut dans l’administration explique désormais cette distinction.

Le sélecteur sert à changer la langue de la même page. Son lien souligné utilise les styles de liens existants ; `aria-current="page"` indique la langue active aux technologies d’assistance. Le choix est mémorisé au clic.

À essayer : cocher la disponibilité anglaise pour cet essai local, enregistrer, ouvrir Voir en anglais, puis vérifier `/en/` et le retour vers `/fr/`. Sur l’accueil, le grand titre provient du champ ACF Accroche. « Home » est le titre WordPress ; les libellés des menus restent à traiter séparément. Ne pas activer la détection navigateur avant d’avoir vérifié ce parcours.

## Présentation du sélecteur FR / EN

À la demande de l’utilisateur, le sélecteur est maintenant placé dans `header.php`, à la suite du menu social. `languages.php` affiche les abréviations FR / EN avec des noms accessibles complets. Le conteneur utilise `role="group"` pour éviter une navigation imbriquée dans la navigation principale.

Le thème définit dans `style.css` une capsule arrondie, des zones cliquables de 44 px, une langue active sur fond foncé et un contour rose au focus clavier. Ce sont toujours des liens : leur rôle est de naviguer vers une autre URL. Les liens se répartissent sur plusieurs lignes si la place manque. Une langue indisponible reste masquée.

À vérifier visuellement : placement après les icônes, absence de doublon sous l’en-tête, rendu mobile, focus avec Tab et activation avec Entrée. Le rendu visuel n’a pas été inspecté dans un navigateur graphique.

## Étape suivante — les libellés des menus

Les titres des pages et les libellés de navigation sont des données distinctes. Traduire le titre Accueil en Home ne remplace donc pas le libellé Accueil du menu.

Dans **Apparence → Menus**, choisir le menu principal, déplier une entrée et renseigner **Libellé anglais (Atelier Langues)**, puis cliquer sur **Enregistrer le menu**. Le libellé de navigation standard reste français. Répéter pour les autres entrées et, si nécessaire, les menus sociaux ou de pied de page. Une valeur anglaise vide conserve le français.

Fonctionnement : `wp_nav_menu_item_custom_fields` affiche notre champ dans l’éditeur WordPress ; `wp_update_nav_menu_item` enregistre la métadonnée `_atelier_langues_menu_en` sur l’entrée de menu, après contrôle de la capacité `edit_theme_options` et du nonce propre à cette entrée. Une sauvegarde par import ou API sans ce champ conserve les données.

Dans `content.php`, le filtre `wp_setup_nav_menu_item` remplace le libellé lors de l’affichage anglais. Il intervient avant le rendu du thème : le bouton Créations et les noms accessibles des icônes utilisent ainsi le libellé traduit. Les liens, classes CSS et images des icônes sont conservés. Traduire un libellé ne rend pas disponible la traduction de la page cible : si celle-ci manque, le lien continue vers le français.

Fichiers modifiés : `includes/admin.php`, `includes/content.php`, `tests/smoke.php` et les documents de suivi. Aucun changement du thème pour cette étape.

Tests manuels : enregistrer un libellé anglais, recharger l’éditeur pour vérifier sa persistance, comparer le menu sous /fr/ et /en/, tester le sous-menu Créations et vérifier les icônes. L’enregistrement depuis l’éditeur de menus n’a pas encore été testé dans un navigateur connecté.

## Textes fixes du thème — saisie français / anglais

L’utilisateur indique avoir traduit ses contenus. La section **Textes du thème** est ajoutée en bas de **Réglages → Atelier Langues**, avec son propre bouton **Enregistrer les textes du thème**. Elle contient 20 libellés détectés dans les modèles (dont des noms accessibles et des textes de repli).

Les textes sont enregistrés dans l’option WordPress `atelier_langues_theme_strings`, indépendamment du thème. Le français reste celui d’origine si son champ est vide ; un anglais vide conserve le français. Aucune traduction anglaise n’est insérée automatiquement. Pour l’accueil, le titre de repli Tendre Chaos n’est utilisé que si le champ ACF Accroche est vide.

Le thème utilise maintenant les fonctions natives `__()`, `esc_html_e()` et `esc_attr_e()` avec le domaine `atelier-artiste`. Le plugin filtre `gettext` uniquement pour ce domaine, les chaînes du catalogue et l’affichage public. Sans le plugin, les textes français d’origine continuent de fonctionner. Un fichier de traduction natif peut être ajouté ultérieurement ; aucun fichier .po/.mo n’est livré à cette étape.

L’enregistrement contrôle la capacité `manage_options` et un nonce distinct, puis assainit le texte. Les modèles échappent les valeurs selon leur contexte HTML ou attribut. Les champs absents d’un formulaire tronqué ne sont pas effacés.

## Noms anglais des disciplines — ajout minimal

Dans **Créations → Disciplines**, cliquer sur le nom d’une discipline pour ouvrir sa fiche complète (pas Modification rapide). Renseigner **Nom anglais**, puis **Mettre à jour**. Le champ est ajouté uniquement à l’écran de modification : pour une nouvelle discipline, la créer d’abord puis la modifier.

`admin.php` ajoute le champ et sauvegarde une seule métadonnée `_atelier_langues_name_en` avec contrôle du droit `edit_term`, nonce et assainissement. `content.php` filtre `single_term_title` uniquement sur les disciplines et pour l’affichage public anglais ; cela couvre le titre de page et le titre de document WordPress qui utilise cette fonction. `taxonomy-discipline.php` échappe explicitement le titre HTML. Aucune duplication, aucun changement de slug ou d’association aux créations.

Le menu possède toujours son propre libellé anglais. La description de discipline n’est pas traduite à cette étape et le sélecteur n’est pas encore affiché sur ces archives. Pour vérifier : ouvrir la discipline depuis le menu anglais, ou visiter son adresse préfixée `/en/discipline/…/`, comparer avec `/fr/`, puis vider le nom anglais pour tester le repli. Tests automatisés en mémoire sur l’anglais, le français, le repli et l’isolation des autres taxonomies ; sauvegarde réelle à vérifier depuis WordPress.

## Retrouver Contact et le pied de page

Vérification de `page.php` et `footer.php` : Contact utilise `contact_intro`, puis le contenu principal WordPress. Si l’introduction est vide, la phrase de repli provient du thème et se modifie dans Textes du thème → Contact — texte de repli. Le footer utilise `footer_texte` enregistré sur l’Accueil, quelle que soit la page visitée. Le copyright associe une année automatique et le nom du site, communs aux langues.

Pour éviter de nouveaux champs, `admin.php` ajoute seulement des raccourcis en haut de l’écran et des explications pour Contact et Accueil. Aucun changement du stockage ou du thème. Le footer anglais nécessite que la version anglaise de l’Accueil soit déclarée disponible, puisque cette page porte ses traductions. Le champ e-mail commun est sur l’Accueil, modifiable dans l’éditeur WordPress ; le lien Instagram de Contact reste dans le thème à cette étape.

À faire : ouvrir Textes de Contact, traduire Introduction et le contenu éventuel, enregistrer ; ouvrir Pied de page (champs de l’accueil), traduire son texte et enregistrer ; vérifier Contact et le footer sur plusieurs pages en FR et EN. Si une phrase française persiste sur Contact avec une introduction vide, traduire la phrase de repli dans Textes du thème avec le bouton d’enregistrement de cette section.

## Revue d’ensemble après saisie des traductions

Lecture du code, état enregistré en base et essais HTTP réels : les six créations, Accueil, À propos et Contact sont déclarés disponibles en anglais. Aucun contenu principal ou champ ACF textuel non vide ne manque de valeur anglaise sur ces fiches, sauf l’Accroche de l’accueil (le titre Doux Chaos reste partagé par repli). La page générale Créations (ID 20) et l’article de démonstration ne sont pas disponibles en anglais. Les 20 libellés du thème ont une valeur anglaise enregistrée. Les quatre disciplines ont leur nom anglais. Ces contrôles constatent la présence des traductions, pas leur qualité linguistique.

Points éditoriaux laissés à l’utilisateur : décider si Doux Chaos doit rester identique ; déclarer la page générale Créations disponible si souhaité ; relire Movie theater (nom de discipline et entrée du menu), qui désigne une salle plutôt que l’art cinématographique. Les libellés sociaux et Contact sans anglais spécifique conservent leurs noms partagés.

Amélioration minimale : `languages.php` affiche désormais le sélecteur sur les archives de disciplines. Le lien EN est proposé si le nom anglais existe ; la bascule conserve le chemin, la pagination et les paramètres de la requête. Les descriptions de disciplines restent communes, et cette disponibilité de nom ne signifie pas que chaque création listée est traduite. Les liens de créations sans anglais conservent leur repli vers FR. Pas de nouvelle donnée ou nouveau fichier fonctionnel.

Vérifications exécutées : 39 contrôles CLI en mémoire ; syntaxe PHP du plugin valide ; 28 URL (4 pages, 6 créations et 4 disciplines dans les deux langues) aboutissent à 200 sans erreur PHP visible, avec un seul sélecteur et le bon attribut lang. /en/oeuvres/ revient vers /fr/oeuvres/ conformément à sa disponibilité. Les textes de footer enregistrés FR/EN sont retrouvés dans le HTML de Contact. Une URL inconnue répond 404.

La détection était déjà activée par l’utilisateur : essais HTTP confirmant la redirection sans préfixe selon Accept-Language, la priorité du cookie français sur un navigateur anglais, le respect des préfixes explicites malgré une préférence contraire, et la mémorisation du choix anglais sur une discipline avec nettoyage du paramètre de choix. Réponses de redirection non mises en cache ; cookie limité au sous-dossier, HttpOnly et SameSite=Lax sur ce site local HTTP.

Les scripts temporaires de vérification sont supprimés après usage. Aucune traduction existante n’a été modifiée. Restent notamment : contrôle visuel et clavier par l’utilisateur, essais des sauvegardes avec comptes de permissions différentes, traductions de descriptions et métadonnées des images, slugs anglais et référencement des archives/sitemaps avant publication.

## Présentation des images et du carrousel

À la demande de l’utilisateur : les commandes du carrousel sont placées dans le conteneur des images (`front-page.php`). Le CSS les positionne à mi-hauteur, à gauche et à droite du cadre. Le compteur reste annoncé aux lecteurs d’écran mais ses chiffres sont masqués visuellement ; le JavaScript existant continue de le mettre à jour.

Dans `style.css`, les visuels d’accueil sont limités à 960 px de large et 520 px de haut ; les images principales des fiches (dont Quelques créations) à 480 px de haut ; les images des textes à 720 px de large et 520 px de haut. Les petites images ne sont pas étirées, sauf celles du carrousel dans son cadre partagé. `object-fit: contain` conserve l’image entière : des marges peuvent apparaître selon ses proportions. Les cartes des grilles, le logo et les icônes conservent leurs règles propres.

Pas de nouveau champ ACF, pas de duplication de média, aucun changement des traductions. Ces tailles sont des limites d’affichage dans le thème ; les fichiers originaux restent dans la médiathèque. Vérifier visuellement sur ordinateur et mobile : centrage des flèches, absence de chiffres, défilement précédent/suivant, fiche Quelques créations et images insérées dans les textes.

## Quelques créations — rangement en galerie

La fiche associait une image mise en avant dans la colonne gauche et des blocs Image empilés dans la colonne de description. `single-oeuvre.php` détecte désormais les fiches comportant plusieurs images sans texte ni vidéo/iframe dans leur contenu affiché. Il leur ajoute la classe `work-gallery`, sans dépendre du titre, du slug ou de la langue. Les fiches avec une description conservent leur disposition habituelle.

Dans `style.css`, cette classe place l’image mise en avant et les blocs Image dans une grille commune de trois colonnes (deux sous 800 px, une sous 500 px). Les cadres ont la même hauteur, les espaces sont réguliers, les images restent entières grâce à `object-fit: contain`. Aucun média ni contenu en base n’est modifié. Une fiche contenant du texte ou des légendes textuelles n’active pas cette détection simple ; un bloc Galerie WordPress explicite peut être une évolution ultérieure si nécessaire.

Le titre est conservé. Pour le renommer : Réglages → Atelier Langues → choisir Quelques créations → modifier le titre français et/ou anglais → Enregistrer les deux langues. Vérifier les rangées sur ordinateur et mobile. Vérification HTTP du balisage seulement ; le rendu graphique final reste à confirmer par l’utilisateur.

## Galerie — agrandissement au survol

Sur ordinateur avec souris (écran d’au moins 800 px), les images de galerie s’agrandissent à 140 % au survol, au-dessus des voisines, sans déplacer la grille. Le même effet est disponible au focus clavier : les figures reçoivent `tabindex="0"` dans `single-oeuvre.php`. Le CSS ajoute une ombre et un contour de focus ; l’animation est supprimée si la réduction des mouvements est demandée. Pas de JavaScript supplémentaire. Sur écran tactile, la présentation reste inchangée. Ce zoom est un aperçu, pas une ouverture de l’original en plein écran.

À vérifier visuellement : survol des trois images, absence de débordement horizontal, puis Tab pour passer d’une image à l’autre. Les fichiers et traductions restent inchangés.

## Créations et disciplines — images et titres plus petits

`loop-creations.php` place maintenant le titre au-dessus de l’image dans toutes les grilles de créations et disciplines. Le CSS règle les titres de cartes à 16 px, les images dans des cadres de 280 px de haut, le titre des fiches à 24 px et leur image principale à 400 px maximum. Les images des galeries sont réduites à 300 px sur grand écran. Les œuvres restent entières.

Le survol et le focus clavier agrandissent aussi les cartes et l’image principale des fiches sur ordinateur. Le titre des fiches reste au-dessus du visuel comme auparavant. Aucun contenu en base ni traduction modifié. Vérifier le rendu des disciplines et d’une fiche sur ordinateur et téléphone.

## Navigation par disciplines uniquement

L’utilisateur ne souhaite pas de page générale mélangeant toutes les créations. Vérification du HTML : Créations est déjà un bouton sans lien, qui ouvre les sous-disciplines. L’accès restant venait du lien Toutes les créations sur les fiches.

`single-oeuvre.php` remplace ce lien par un retour vers chaque discipline associée à l’œuvre, avec son nom anglais disponible en EN. Une œuvre associée à plusieurs disciplines conserve ces liens distincts. Sans discipline, aucun faux lien n’est ajouté.

`functions.php` redirige les accès publics à l’ancienne page `oeuvres` vers l’accueil (302, même langue si disponible). La page et ses données ne sont pas supprimées ; elle peut toujours être éditée ou prévisualisée. Aucun projet n’est déplacé ou supprimé. Le modèle `page-oeuvres.php` est conservé mais n’est plus affiché en navigation publique ordinaire. Une éventuelle future galerie sera une décision séparée.

À vérifier : Créations ouvre le sous-menu sans changer de page ; chaque discipline liste ses créations ; depuis une fiche, les liens de retour mènent aux disciplines ; /fr/oeuvres/ et /en/oeuvres/ reviennent à l’accueil.

## Couleur du logo dans la navigation

Couleur dominante relevée sur les pixels opaques de `cropped-logo_1.png` : **#3E6385**. La variable CSS `--logo-blue` applique ce bleu au menu (y compris Créations), au sélecteur FR/EN et au titre de l’accueil. La langue active conserve un texte clair sur fond bleu. Les icônes utilisent les SVG locaux comme masques CSS, avec `background: currentColor`, pour reprendre exactement la couleur du menu. Leur nom accessible est conservé.

Le logo original et les contenus restent inchangés. Les SVG n’ont pas été modifiés : leur écriture était refusée par les permissions du dossier ; la solution par masque permet de conserver ces fichiers. Pour ajuster ensuite tous les éléments ciblés, changer uniquement `--logo-blue` dans `style.css`. Vérifier après Ctrl+F5 en FR et EN.

## BAT AVRAHAM — description sans lien

## Contact — retrait des liens répétés

## Contact — logo et Doux Chaos centrés

## Accueil — logo facultatif et remplaçable

## Fiche pour le responsable de stage

## Recette finale — Contact, langue et accessibilité

## Navigation responsive — menu burger

## Images des fiches — ouverture au toucher et au clic

## Autonomie cliente — première étape

## Autonomie cliente — guide dans les créations

`admin.php` ajoute un bloc d’aide dans l’éditeur des œuvres, un exemple de titre et des instructions pour les trois champs ACF existants. Aucun champ de données supplémentaire, migration ou suppression. Le bloc explique les étapes et propose un lien vers la traduction du même ID après enregistrement du français. Vérifier dans Créations → Ajouter une création ; la position exacte du panneau dépend de l’éditeur WordPress et des préférences d’affichage.

Nouvel écran **Réglages du site** : module unique `includes/site-settings.php`, chargé par le fichier principal. Il réutilise les sources existantes et n’introduit pas de table ou d’option de contenu supplémentaire. Logo choisi via la médiathèque WordPress ; e-mail synchronisé avec les icônes correspondantes lors d’un enregistrement ; destinations sociales existantes éditables et masquées lorsqu’elles sont vides ; footer FR/EN conservé dans ACF et la traduction de l’accueil. Les traductions des autres champs de l’accueil ne sont pas remplacées par cette sauvegarde.

Contrôles : capacité manage_options + edit_theme_options, nonce, vérification de l’image, adresse e-mail et URL HTTP/HTTPS avant modification. Les identifiants sociaux reçus ne sont acceptés que s’ils font partie du menu social actuel. L’écran Atelier Langues pointe désormais vers ces réglages pour les coordonnées. Aucune migration automatique ; voir AUTONOMIE-CLIENTE.md à la racine pour l’inventaire, les limites et l’ordre des prochaines étapes.

`images.js`, chargé uniquement sur les fiches de créations, transforme les images sans lien existant en boutons accessibles. Un appui ou un clic ouvre un dialogue natif avec l’image entière, une croix de fermeture et Échap ; le dialogue restaure le focus et le défilement de fond est bloqué. Les liens d’images déjà présents sont conservés. Sans JavaScript ou sans support du dialogue, les images restent visibles normalement.

Les libellés Agrandir l’image et Fermer l’image sont traduisibles dans Textes du thème. L’aperçu utilise l’image chargée par le navigateur, pas systématiquement le fichier original de la médiathèque. La fenêtre d’image est commune au mobile et à l’ordinateur. Aucun plugin supplémentaire. Tests graphiques et toucher réels à effectuer par l’utilisateur.

Ajustement : suppression du texte visible Menu, bouton rond à trois traits CSS devenant une croix quand ouvert, nom accessible conservé. Panneau compact avec espacement, bordure et ombre légère. Diagnostic téléphone : le virtualhost Wamp `localhost` contient `Require local`, ce qui interdit les accès depuis d’autres appareils. Aucune configuration Apache ni règle de pare-feu n’a été modifiée ; une configuration réseau de développement spécifique reste nécessaire, ainsi que la gestion des URL WordPress basées sur localhost.

`header.php` ajoute un bouton Menu (symbole burger décoratif, `aria-controls` et `aria-expanded`). Le JavaScript existant `navigation.js` masque/replie la navigation sous 1100 px et la rétablit sur grand écran. Sans JavaScript, les liens restent visibles. Les icônes et FR/EN restent après les liens dans le panneau.

Le CSS existant est complété par une disposition verticale sous 1100 px : logo et bouton sur la première ligne, navigation en dessous, disciplines dans le flux. Pas de bibliothèque ni nouveau fichier. Échap ferme d’abord un sous-menu ciblé, puis le panneau ; clic extérieur et sortie du focus ferment aussi le panneau. Le bouton indique son état aux lecteurs d’écran.

À tester : largeur de 390 px, 768 px et 1440 px ; clic Menu, ouverture de Créations, navigation Tab/Entrée/Échap, clic extérieur, agrandissement/réduction de fenêtre, FR/EN. Le rendu graphique et les interactions réelles restent à valider dans le navigateur ; contrôles de syntaxe et présence du balisage effectués séparément.

Logo de Contact réduit à 140 px. Montserrat variable ajoutée localement dans `themes/atelier-artiste/assets/fonts/` avec sa licence OFL (source : dépôt officiel Google Fonts, https://github.com/google/fonts/tree/main/ofl/montserrat). Le CSS applique cette police aux textes de Contact, dont le titre et Doux Chaos ; les autres pages conservent leurs polices précédentes. Aucun appel Google Fonts au chargement du site.

Les essais HTTP réels confirment les trois priorités : adresse sans préfixe avec navigateur anglais → /en/ ; même adresse avec cookie français → /fr/ ; /en/ explicite avec navigateur et cookie français → 200 sans retour forcé au français.

Pour ouvrir les outils sans F12 : **Ctrl + Maj + I**, ou menu Chrome/Edge **⋮ → Plus d’outils → Outils de développement**. Puis Application → Stockage → Cookies → localhost. Le raccourci F12 peut être affecté à une fonction matérielle du PC ; Fn+F12 est une autre possibilité selon le clavier.

Un inventaire `IMAGES-A-RELIRE.md` à la racine liste 18 images sans texte alternatif. Il ne remplit pas automatiquement les descriptions : les images informatives doivent être décrites à partir de leur contenu et validées ; les images décoratives peuvent garder un alt vide. Les traductions anglaises des alt restent à implémenter.

Le contrôle a retrouvé le sélecteur limité aux fiches singulières dans la version courante de `languages.php` : le support des disciplines a été rétabli et les 39 contrôles repassent. La tentative de capture mobile avec Chrome sans interface a échoué au démarrage du processus graphique ; aucun succès de recette visuelle n’est revendiqué. Vérifier manuellement avec Ctrl+Maj+I, puis Ctrl+Maj+M pour le mode mobile, et avec Tab/Entrée/Échap pour le clavier.

Sauvegarde locale : dossier `.backups` à la racine, interdit en HTTP par `.htaccess` (403 vérifié). Export SQL et archive des fichiers séparés ; contrôle de lecture ZIP et empreintes SHA-256 dans un manifeste lorsque la création est achevée. Une sauvegarde contrôlée en lecture ne prouve pas sa restauration : un essai de restauration séparé reste nécessaire avant livraison finale. Ne pas publier ni partager ces sauvegardes avec le site : elles contiennent sa configuration et sa base.

La fiche courte `FICHE-STAGE-LANGUES.md`, à la racine du site, rassemble les étapes, les lieux de stockage, la différence Accept-Language/cookie, les manipulations F12 et le choix de ne pas utiliser de balises personnalisées fr/en. Réglage vérifié en base : détection activée. Banc de test relancé : 39 contrôles réussis. Aucune donnée de traduction modifiée pendant cette vérification.

Ajustement demandé : le logo est désormais entre le nom de l’artiste (surtitre) et Doux Chaos, avec une largeur maximale de 120 px. Le champ ACF conserve son fonctionnement pour le remplacer ou le retirer.

Champ ACF image `accueil_logo` (retour ID, non obligatoire) créé dans **Accueil — Logo**, uniquement sur la page d’accueil. Le logo actuel du site a été choisi comme valeur initiale sans écraser une sélection existante. Il reste indépendant du logo du menu et de Contact, et partagé entre FR et EN.

Pour remplacer ou retirer : **Pages → Toutes les pages → Accueil → Accueil — Logo → Logo de l’accueil**, puis **Mettre à jour**. Retirer l’image masque le logo sans repli automatique. `front-page.php` l’affiche avant le surtitre et le titre ; `style.css` le centre et le limite à 180 px, comme celui de Contact. Aucun nouveau code permanent de création de champs : le script ponctuel est supprimé après usage. Syntaxe PHP et présence du logo vérifiées en HTTP FR/EN ; rendu graphique à confirmer.

Deux champs ACF gratuits sont créés en base dans le groupe **Contact — Logo et signature**, affiché uniquement sur Contact : `contact_logo` (image, retour ID) et `contact_signature` (texte). Le logo du site a été sélectionné et la phrase Doux Chaos initialisée, sans écraser une valeur préexistante. Le script ponctuel de création est supprimé après usage ; aucun chargement PHP supplémentaire permanent.

Pour modifier : **Pages → Toutes les pages → Contact → Contact — Logo et signature**. Le logo est commun aux langues ; le texte se traduit depuis **Réglages → Atelier Langues → Contact → Phrase sous le logo**, grâce au mécanisme ACF existant. L’anglais absent conserve Doux Chaos.

`page.php` affiche le logo et sa phrase sous le contenu de Contact. `style.css` centre le bloc, limite le logo à 180 px (adapté au mobile) et reprend le bleu du logo pour la phrase. Vérification HTTP FR/EN : bloc et image présents, réponse 200 sans erreur PHP visible. Rendu graphique à confirmer dans le navigateur.

À la demande de l’utilisateur, `page.php` n’affiche plus le bloc e-mail et Instagram sous le texte de Contact. Les icônes du menu sont conservées. Le champ e-mail existant n’est pas supprimé de la base. La présentation et le contenu principal restent modifiables et traduisibles comme auparavant. Pour enrichir la page, privilégier des informations validées par l’artiste : demandes acceptées, disponibilités ou zone d’activité, sans inventer de coordonnées ni d’engagement de réponse.

À la demande de l’utilisateur, les balises de lien SoundCloud entourant la description de la création court-métrage (ID 105) ont été retirées du contenu français et de sa traduction anglaise. Les textes et les paragraphes sont conservés, ainsi que les autres métadonnées de traduction. Vérification HTTP FR/EN : la destination bat-avraham-laboratory ne figure plus dans la page. Aucun changement des liens sociaux du menu.

Fichiers concernés : `includes/content.php` (catalogue et lecture), `includes/admin.php` (formulaire et sauvegarde), les modèles `page.php`, `single-oeuvre.php`, `front-page.php`, `header.php`, `footer.php`, `loop-creations.php`, `index.php` et `functions.php` (localisation des libellés), plus le banc de test. Le design et les textes enregistrés des pages ne sont pas modifiés.

Exercice : trouver **À propos — surtitre**, saisir l’anglais choisi pour L’artiste, enregistrer avec le bouton de cette section et ouvrir À propos en EN. Revenir en FR pour comparer. Vérifier aussi les titres de sections et le lien de retour d’une création. L’enregistrement réel de ce nouveau formulaire reste à tester dans le navigateur.
