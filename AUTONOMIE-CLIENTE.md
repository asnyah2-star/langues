# Préparer la livraison — autonomie de la cliente

## Première étape réalisée : un point d’entrée pour les réglages

Ouvrir **Réglages du site** dans le menu gauche de WordPress.

| Besoin de la cliente | Où le modifier | État |
| --- | --- | --- |
| Logo du menu | Réglages du site → Logo du menu → Choisir / remplacer ou Retirer | Ajouté, sélecteur de médiathèque |
| E-mail | Réglages du site → E-mail de contact | Champ existant et icône e-mail synchronisés à l’enregistrement |
| Adresses des réseaux sociaux | Réglages du site → champ du réseau | Adresses existantes réutilisées ; vider masque l’icône |
| Ajouter ou réordonner les réseaux | Apparence → Menus → Liens sociaux | Fonctionnement existant, lien d’aide dans l’écran |
| Texte du footer français / anglais | Réglages du site → Pied de page | Même stockage ACF et traduction que l’accueil, pas de nouvelle copie |
| Logo et carrousel de l’accueil | Pages → Accueil | Champs existants, raccourci dans Réglages du site |
| Logo et phrase de Contact | Pages → Contact | Champs existants, raccourci dans Réglages du site |
| Biographie et portrait | Pages → À propos | Champs et éditeur existants |
| Nouvelle création | Créations → Ajouter une création | À simplifier et à tester avec la cliente |
| Traductions | Réglages → Atelier Langues | Fonctionnel ; saisie HTML à améliorer |
| Discipline et nom anglais | Créations → Disciplines | Fonctionnel ; description anglaise non prise en charge |
| Libellés anglais des menus | Apparence → Menus | Champ Libellé anglais |
| Textes fixes du thème | Réglages → Atelier Langues → Textes du thème | Catalogue existant |
| Texte alternatif des images | Médias → Bibliothèque → image | Français partagé actuellement ; anglais à prévoir |

## Ce qui est conservé

Les données restent dans leurs emplacements d’origine : logo dans les réglages du thème, liens dans les entrées du menu social, footer et e-mail sur l’accueil, anglais du footer dans la traduction de l’accueil. Rien n’est migré ni supprimé. Les anciens écrans restent utilisables : une seule valeur enregistrée peut être visible depuis plusieurs écrans.

L’e-mail affiché dans le nouvel écran est celui de l’icône si elle existe, sinon celui du champ ACF. Les deux sources sont harmonisées lors d’un enregistrement volontaire. Les réseaux absents du menu ne sont pas inventés. Vider un lien masque l’entrée sur le site sans la supprimer. Le texte anglais du footer nécessite toujours une version anglaise de l’accueil disponible.

Le nouvel écran est réservé aux utilisateurs ayant `manage_options` et `edit_theme_options`. Le compte cliente et ses droits adaptés restent à préparer : ne pas lui donner automatiquement tous les droits administrateur pour contourner ce point.

## Ordre des prochaines étapes

1. **Faire un essai des réglages** : modifier un texte du footer, enregistrer, vérifier FR/EN et recharger l’administration ; tester logo et réseau social, puis remettre les valeurs souhaitées.
2. **Simplifier la fiche création** : confirmer avec la cliente les données vraiment utiles (description, image principale, galerie, discipline, année, technique, dimensions, crédits, médias/liens facultatifs). Ne pas ajouter des champs sans besoin identifié.
3. **Améliorer la saisie bilingue** : éditeur visuel, état des traductions et textes alternatifs/légendes.
4. **Préparer les droits et le guide cliente** : parcours guidés pour ajouter une création, remplacer une image et traduire un texte.
5. **Recette et livraison** : tests mobile/clavier, référencement, sauvegarde actualisée avec restauration testée, hébergement/HTTPS et responsabilité de maintenance clairement définie.

## Vérifications de cette étape

## Fiches de créations : guide intégré

L’écran **Créations → Ajouter une création** (et Modifier) comporte maintenant un bloc **Préparer cette création** : titre, description/blocs de médias, image mise en avant, disciplines, informations facultatives, brouillon et aperçu, puis lien vers la traduction de la fiche. Le titre suggère « Nom de l’œuvre ». Les champs ACF année/technique/dimensions portent des instructions courtes.

Pas de champ supplémentaire ni de modification des données. Les blocs WordPress restent le moyen d’ajouter images, galerie, audio ou vidéo ; les besoins futurs de crédits et de médias structurés restent à confirmer avec la cliente. Tester la publication d’une création en brouillon avec elle avant de figer l’ergonomie. Une Galerie WordPress avec légendes peut nécessiter un ajustement de présentation : elle n’active pas nécessairement notre détection actuelle des fiches composées uniquement d’images sans texte.

Syntaxe PHP valide ; rendu de l’écran vérifié en mémoire avec logo, e-mail, footer, cinq entrées sociales et nonce ; 39 contrôles existants réussis. Aucun réglage de contenu n’a été modifié pendant ces vérifications. Sauvegarde réelle du nouveau formulaire et interaction du sélecteur de médias encore à tester dans le navigateur connecté. La sauvegarde antérieure ne couvre pas cette nouvelle étape.
