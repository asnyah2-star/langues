# Repères pour modifier le site

## Les contenus dans WordPress

Depuis le 23 octobre : **Pages → Accueil → Accueil — Image ou carrousel** permet de choisir sans image, image fixe ou carrousel manuel, puis de régler le voile. `mosa.png` est sélectionnée pour le premier essai. La présentation brève reste dans le champ Introduction existant. **Créations → Disciplines** contient Arts plastiques, Musique, Cinéma et Performance ; cocher les disciplines dans chaque fiche. Le menu Créations et ses sous-menus sont gérés dans Apparence → Menus. Voir la première section de `FICHE-PROJET.md` pour le pas à pas actuel.

- **Pages > Accueil** : accroche, introduction, contenu et image mise en avant. Les champs Footer contiennent les coordonnées partagées par tout le site.
- **Pages > À propos** : introduction ACF, biographie dans l’éditeur et portrait en image mise en avant.
- **Pages > Contact** : introduction ACF et contenu complémentaire dans l’éditeur. L’e-mail est récupéré depuis Accueil.
- **Œuvres > Ajouter une œuvre** : titre, description dans l’éditeur, image mise en avant, année, technique et dimensions. Publier la fiche l’ajoute à la page Œuvres.
- **Pages > Œuvres** : texte introductif au-dessus de la liste automatique.
- **Apparence > Menus** : Accueil, Œuvres, À propos et Contact. Toutes les entrées sont gérées par WordPress et utilisent le même style.
- **ACF > Groupes de champs** : les groupes sont enregistrés en base et peuvent être modifiés dans l’interface. Sauvegarder la base de données lors d’un transfert du site.

## Les fichiers du thème

- `header.php` : signature et menu partagé.
- `front-page.php` : accueil.
- `page-oeuvres.php` : grille des œuvres publiées, 12 par page.
- `taxonomy-discipline.php` : liste des créations d’une discipline.
- `loop-creations.php` : boucle et cartes partagées par ces deux listes.
- `carousel.js` et `navigation.js` : commandes du carrousel et du sous-menu.
- `single-oeuvre.php` : fiche individuelle et informations ACF.
- `page.php` : structure commune aux pages ordinaires, à À propos et à Contact. Les conditions `is_page()` conservent leurs particularités.
- `footer.php` : texte du pied de page, copyright et menu facultatif. Les liens e-mail et Instagram sont sur la page Contact.
- `functions.php` : CSS, menus, déclaration du type Œuvre et fonctions utilitaires.
- `style.css` : couleurs dans `:root`, puis styles des composants. Les styles des pages intérieures sont à la fin.

`page-oeuvres.php` est choisi grâce au slug `oeuvres`. Dans `page.php`, les conditions utilisent les slugs `a-propos` et `contact`. Conserver ces identifiants. Les anciens fichiers `page-a-propos.php` et `page-contact.php` ont été retirés lors du regroupement.

## Avant la présentation

Ajouter le logo original, le portrait, la biographie validée et plusieurs œuvres avec leurs photographies. Les phrases d’attente disparaissent quand les contenus sont remplis. Aucun formulaire d’envoi n’est installé : le contact ouvre le logiciel de messagerie via un lien e-mail.
