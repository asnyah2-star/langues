# Audit et première version — 28 octobre 2026

> **Relecture du 29 octobre 2026 :** ce document conserve les étapes historiques. L’état actuel et les priorités sont en tête de [NOTES.md](NOTES.md). Quatre modules sont chargés, le catalogue contient 22 textes, les noms de disciplines et leur sélecteur sont implémentés. Les 39 contrôles repassent ; syntaxe PHP/JavaScript valide. Deux risques de sauvegarde restent à traiter : disparition des traductions ACF absentes d’un envoi et protection partielle contre les modifications concurrentes. Les anciennes mentions « reste à traiter » ci-dessous ne constituent pas l’état actuel.

Revue générale après saisie : 39 contrôles automatisés et 28 URL publiques vérifiés ; voir la section finale de NOTES.md pour l’état précis des traductions, les essais de détection et les points restants. Le sélecteur est maintenant disponible sur les archives de disciplines dont le nom anglais est renseigné. Aucune modification des traductions de l’utilisateur.

Mise à jour disciplines : ajout minimal du nom anglais dans la fiche de modification de chaque discipline et affichage sur son archive anglaise. Les descriptions, slugs et sélecteur d’archive restent à traiter. Voir NOTES.md pour les manipulations et limites ; les remarques antérieures ci-dessous décrivent l’historique.

Nouvelle mise à jour : 20 chaînes fixes recensées sont désormais localisées avec les fonctions natives WordPress et éditables dans Réglages → Atelier Langues → Textes du thème. L’anglais reste à saisir ; les noms de disciplines et métadonnées d’images restent hors de ce catalogue. L’état initial décrit plus bas reste conservé comme historique. Les tests en mémoire couvrent aussi les deux langues, le repli français et l’isolation du domaine de traduction ; la sauvegarde du nouveau formulaire attend un essai manuel.

Mise à jour après la version initiale : les libellés des menus sont maintenant traduisibles depuis Apparence → Menus. Le détail historique ci-dessous décrit l’état initial 0.2.0 ; cette limite concernant les libellés de menus est levée. Les disciplines et chaînes fixes restent à traiter. Les nouveaux tests vérifient en mémoire la traduction des libellés, le repli, la conservation de l’objet original, l’échappement et la présence du nonce. L’enregistrement réel du menu reste à tester manuellement.

## État vérifié avant l’implémentation

Lecture des fichiers réels et interrogation de WordPress local en lecture seule, sans afficher la configuration de connexion ni les comptes utilisateurs.

- WordPress 7.1.3 ; PHP CLI 8.3.28 et version 8.3.28 sélectionnée dans la configuration Wamp. La version servie par Apache n’a pas été interrogée séparément.
- Thème actif : `atelier-artiste` 1.1.
- Extensions actives : ACF gratuit 6.8.10, Akismet et Atelier Langues. Hello Dolly est présent mais inactif. Aucun plugin SEO, formulaire, cache ou multilingue actif détecté. Aucun conflit établi avec Akismet ; pas de certification de compatibilité générale.
- Site en sous-dossier : `http://localhost/tamaraszefer`. Permaliens existants : `/%year%/%monthnum%/%day%/%postname%/`.
- Pages publiées : Accueil (10), Créations (20), À propos (21), Contact (22). Politique de confidentialité en brouillon. Un article de démonstration publié.
- Six créations de type `oeuvre`, déclaré dans le thème, avec une taxonomie `discipline` : Arts plastiques, Cinéma, Musique, Performance.
- Menus : `menu` et `Liens sociaux`. Emplacements : principal, social et footer.

## Templates et contenus

| Fichier | Rôle / textes à traduire |
| --- | --- |
| `functions.php` | Type œuvre, disciplines, trois menus, accès ACF, liens externes ; libellés Année/Technique et commandes de navigation notamment |
| `header.php` | Logo, menus, lien Aller au contenu, libellé Navigation principale, Accueil |
| `footer.php` | Texte ACF sur l’accueil, copyright, menu de pied de page et son nom accessible |
| `front-page.php` | Accroche et introduction ACF, carrousel partagé ; Artiste multidisciplinaire, Image précédente/suivante, titre de repli Tendre Chaos |
| `page.php` | À propos et Contact identifiés par leur slug français ; L’artiste, Démarche artistique, Parcours et expositions, phrase de repli Contact |
| `page-oeuvres.php` | Titre et contenu de Créations, requête paginée sur les œuvres |
| `loop-creations.php` | Cartes, titres, année ; Visuel à venir et message de liste vide |
| `single-oeuvre.php` | Titre, description, image, informations ACF ; Toutes les créations, Année, Technique, Dimensions |
| `taxonomy-discipline.php` | Nom et description de discipline et grille |
| `index.php` | Boucle générale et message Aucun contenu pour le moment |

`navigation.js` et `carousel.js` pilotent les interactions ; pas de système de traduction existant dans ces scripts. Les traductions livrées avec ACF concernent son interface, pas les contenus du site.

## Inventaire ACF réel

| Champs | Type | Traitement initial |
| --- | --- | --- |
| `accroche`, `oeuvre_technique`, `oeuvre_dimensions` | text | Valeur anglaise possible |
| `accueil_intro`, `artiste_intro`, `contact_intro`, `footer_texte` | textarea | Valeur anglaise possible |
| `artiste_demarche`, `artiste_parcours` | wysiwyg | HTML anglais possible ; formatage conservé par ACF |
| `contact_email` | email | Commun |
| `oeuvre_annee` | number | Commun |
| `accueil_visuel_mode` | select | Commun |
| `accueil_image_fond` | image | Commun |
| `accueil_carrousel` | relationship | Commun, sélection d’images existante conservée |
| `accueil_voile` | range | Commun |

Les URL simples sont prises en charge si ajoutées aux groupes applicables. Aucun champ `link`, répéteur, groupe imbriqué ou galerie n’a été trouvé : leur traduction n’est pas implémentée ni annoncée. Les règles ACF de visibilité sont utilisées pour sélectionner les groupes correspondant au contenu.

## Architecture retenue et étapes

1. Un seul identifiant par contenu. Champs WordPress/ACF français conservés ; métadonnée privée `_atelier_langues_en` pour l’anglais, reliée au même ID. Pas de double projet ni de duplication des médias.
2. Écran Réglages → Atelier Langues : titre, contenu, extrait et champs ACF simples côte à côte. Enregistrement via `admin-post.php`, capacité `manage_options` et `edit_post`, nonce lié au contenu, assainissement selon le type et échappement à l’affichage. Les modifications françaises ordinaires restent possibles dans l’éditeur WordPress.
3. Lecture anglaise par filtres WordPress et ACF ; titre anglais obligatoire et case de validation explicite pour rendre la version disponible. Champ anglais vide : repli français. Une page anglaise non disponible redirige temporairement vers sa version française. Aucun anglais n’est inventé.
4. Préfixes ajoutés aux règles WordPress existantes, sans changer les captures ni les permaliens stockés. Fonctionne en sous-dossier. Actualisation douce des règles une seule fois à la mise à niveau ; aucune modification de `.htaccess`. Désactivation : règles françaises/anglaises retirées, données conservées.
5. Sélecteur sur les pages et fiches singulières, lien anglais seulement si disponible ; préférence mémorisée après action explicite. Détection navigateur activable après validation manuelle ; URL explicite prioritaire, puis cookie, puis Accept-Language pondéré, puis français.
6. À poursuivre : traductions des menus, disciplines, médias et chaînes statiques ; slugs anglais ; référencement et intégration des extensions éventuelles.

## Périmètre et limites de la version 0.2.0

- Les contenus principaux et ACF simples peuvent être saisis et affichés. Les zones de contenu riches utilisent pour l’instant des zones de texte HTML/blocs, pas deux éditeurs visuels. L’ergonomie cliente reste à valider.
- Les libellés de menus, disciplines, textes statiques du thème, légendes et textes alternatifs restent français. Les liens de menus et liens internes des contenus sont localisés ; les liens vers une fiche sans anglais reviennent au français. Les textes de blocs tiers, widgets et URL dans des scripts ou CSS ne sont pas analysés.
- Les slugs sont actuellement communs : `/en/a-propos/`, pas encore `/en/about/`. Ne pas renommer les slugs français utilisés par les templates. Les archives préfixées fonctionnent, mais leurs libellés ne sont pas encore traduits ; le sélecteur n’est pas encore proposé sur ces archives.
- `html lang`, titre de document et canonique des contenus singuliers utilisent la langue active. Les `hreflang` fr/en ne sont émis que si l’anglais est déclaré disponible. Pas encore de sitemap bilingue, de descriptions SEO éditables ni de compatibilité avec un plugin SEO. Les archives et les pages partiellement traduites nécessitent une stratégie SEO avant publication.
- Un champ anglais vide conserve le français même sur une version anglaise validée : la relecture doit vérifier tous les champs avant publication.
- Pas de suppression des traductions à la désactivation. Pas d’appel externe ni de clé API. Le type `oeuvre` reste déclaré par le thème : changer de thème conserve les données, mais il faudra déplacer cette déclaration pour continuer à éditer ces créations sous un autre thème.
- Contrôle des modifications concurrentes fondé sur `post_modified_gmt` : ce n’est pas un verrou d’édition complet. Les traductions anglaises n’ont pas encore leur propre historique de révisions.
- Redirections de détection en 302, réponses non mises en cache et `Vary: Accept-Language, Cookie`. Tout futur cache/CDN doit exclure les URL sans préfixe de son cache ou respecter ces en-têtes. Les robots peuvent suivre les liens explicites ; aucun traitement différent fondé sur leur user-agent.
- Le cookie de choix dure 180 jours, est limité au chemin du site, HttpOnly, SameSite=Lax et Secure sous HTTPS. Il mémorise uniquement fr/en.

## Vérifications effectuées

- Analyse syntaxique PHP des fichiers du plugin : réussie sous PHP 8.3.28.
- Tests CLI en mémoire : voir `tests/smoke.php`. Aucun texte anglais de test n’est enregistré dans les contenus de la cliente.
- Appels HTTP : `/fr/`, `/fr/a-propos/`, une œuvre et une discipline en français répondent 200 sans erreur PHP visible ; `/en/` et `/en/a-propos/` sans traduction reviennent vers leur URL française ; une URL inexistante sous `/fr/` renvoie 404.
- Le premier test CLI d’affichage avait besoin des fonctions d’administration WordPress ; le banc de test a été corrigé pour les charger. Ce problème n’était pas une erreur de l’écran chargé depuis l’administration.
- Les enregistrements réels via navigateur, le choix mémorisé, la détection activée, le rendu mobile et la navigation clavier restent à vérifier manuellement. Aucun succès n’est revendiqué pour ces essais.

## Tests manuels à réaliser

1. Ouvrir Réglages → Atelier Langues, choisir une page de test et noter les textes français avant modification.
2. Renseigner son titre, son contenu et un champ ACF anglais ; cocher Version anglaise relue et enregistrer. Recharger : les valeurs doivent persister. Ne pas laisser une version de test déclarée disponible sur une page destinée à publication.
3. Visiter les liens Voir en français / Voir en anglais et changer de langue au clavier. Vérifier les titres, le texte, les images identiques, les liens et les champs ACF.
4. Vider une valeur anglaise : vérifier le repli français. Décocher la disponibilité : le lien anglais disparaît et une visite directe revient au français.
5. Vérifier une URL inconnue, la pagination, les paramètres utiles et les liens externes. Les fragments ne sont pas transmis au serveur.
6. Activer la détection dans les réglages, dans une fenêtre privée : tester navigateur anglais puis français, choix manuel, puis URL explicite contraire. Vérifier l’absence de boucle. Désactiver la détection si besoin.
7. Vérifier qu’un compte sans `manage_options` ne voit pas cet écran. Les rôles plus fins pour une cliente éditrice sont une étape ultérieure.
8. Si une URL valide renvoie 404 après un changement de permaliens : Réglages → Permaliens → Enregistrer les modifications, une seule fois.

Références d’API : https://developer.wordpress.org/reference/hooks/rewrite_rules_array/ et https://www.advancedcustomfields.com/resources/update_field/
