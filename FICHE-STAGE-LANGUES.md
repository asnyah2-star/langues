# Langues — présentation au responsable de stage

État du 30 octobre 2026 — version locale 0.4.0, branche refonte-langues-multi.

## Ce que je peux expliquer simplement

« J’ai repris l’ergonomie de l’extension pour traduire les textes au même endroit que leur contenu. Chaque texte ACF traduisible a un onglet par langue active, avec une seule zone visible. Les versions sont enregistrées dans le même champ, à l’intérieur de balises multi. Une liste centrale permet d’ajouter des langues sans recopier le traitement de traduction. Le site choisit la version à afficher selon la langue demandée ; la cliente n’écrit jamais les balises. J’ai conservé les anciens textes et réutilisé un composant commun. »

La traduction est manuelle : le navigateur choisit une langue, il ne traduit pas les textes.

## Ajouter des langues

La fonction `atelier_langues_languages()` dans le fichier principal `atelier-langues.php` contient la configuration. FR et EN sont actives ; des lignes commentées sont prêtes pour NL, ES et RU. Décommenter une ligne ajoute son onglet, ses URL et sa détection. Il faut ensuite renseigner les textes et déclarer les traductions relues dans Langues pour qu’elles soient proposées sur le site.

La première langue de la liste est celle des contenus natifs et sert de secours. Garder FR en premier sur ce site. La suppression d’une langue de la liste ne supprime pas ses textes enregistrés, mais désactive ses URL. Pour réutiliser le plugin ailleurs, configurer les langues et adapter les noms des champs ACF et les textes fixes au nouveau thème.

## Où travaille la cliente ?

| Contenu | Écran WordPress |
| --- | --- |
| Accroche et introduction de l’accueil | Pages → Accueil, onglets des champs ACF |
| Biographie, démarche et parcours | Pages → À propos, onglets des champs ACF |
| Introduction et signature de Contact | Pages → Contact, onglets des champs ACF |
| Technique d’une œuvre | Créations → fiche de l’œuvre, onglets du champ Technique |
| Pied de page | Réglages du site → Pied de page, ou champ Footer de l’accueil |
| Titre, contenu principal et extrait WordPress | Réglages → Langues → sélectionner la fiche |
| Libellés des menus | Apparence → Menus → ouvrir une entrée, onglets FR/EN |
| Nom et description d’une discipline | Créations → Disciplines → Modifier, onglets FR/EN |
| Petits textes fixes du thème | Réglages → Langues → Textes du thème |

Les titres et contenus principaux n’ont pas été déplacés dans ACF. Ils conservent leurs champs WordPress et leur traduction associée pour préserver les blocs et les URL. Leur traduction reste donc dans l’écran Langues ; tout n’est pas fusionné dans la fiche native.

Un encadré Langues dans chaque fiche indique le statut anglais et donne un accès direct à la traduction du titre et du contenu principal.

Les images, le carrousel, les liens, l’e-mail, l’année et les dimensions restent partagés. Les textes alternatifs et légendes des médias ne disposent pas encore de traduction indépendante. Le formulaire de contact reste une étape séparée.

## Un seul champ ACF, plusieurs langues

Exemple de valeur enregistrée :

```html
<multi>
<multi_fr>Présentation française</multi_fr>
<multi_en>English presentation</multi_en>
</multi>
```

La cliente voit les onglets, pas ce code. Cet exemple FR/EN reste valide ; les autres langues ajoutent leurs balises, par exemple `<multi_nl>` ou `<multi_ru>`. L’encodeur reçoit un tableau code → texte et génère les balises par une boucle. Le contenu interne est échappé puis décodé à la lecture. Les champs riches conservent leur mise en forme autorisée ; les champs simples restent du texte.

À l’ouverture d’un ancien champ, le plugin reprend son français et son anglais existants. La réunion dans multi se fait seulement à l’enregistrement. Le nouvel affichage privilégie ensuite la valeur multi ; l’ancienne traduction reste conservée comme secours historique. Aucun traitement en masse ne supprime les données existantes.

## Comment le site choisit-il sa langue ?

1. Une URL explicite /fr/ ou /en/ est prioritaire.
2. Sans préfixe, le choix manuel mémorisé du visiteur est respecté.
3. Sinon, le plugin compare les préférences du navigateur aux langues actives.
4. Si aucune langue prise en charge ne correspond, ou si une traduction manque, le français sert de secours.

La détection navigateur est activée sur cette installation. Le choix manuel est mémorisé 180 jours. Pour une page, chaque traduction doit être relue et déclarée disponible dans Langues, avec un titre traduit, avant d’être proposée sur le site.

## Où lire le code ?

| Fichier du plugin atelier-langues | Rôle |
| --- | --- |
| atelier-langues.php | Charge les modules, configure les langues et les neuf champs ACF multilingues |
| includes/multi.php | Assemble/lit les balises, construit les onglets et adapte les champs ACF |
| assets/multi.js | Change d’onglet au clic ou au clavier, sans effacer les textes |
| assets/admin.css | Présentation commune des formulaires |
| includes/content.php | Choisit les traductions à afficher, avant le formatage |
| includes/languages.php | URL, navigateur, cookie et sélecteur FR/EN |
| includes/admin.php | Traduction des contenus WordPress, menus, disciplines et textes fixes |
| includes/site-settings.php | Logo, coordonnées, liens sociaux et footer |

Le nom affiché est Langues. Le dossier et les fonctions gardent leurs anciens identifiants techniques pour préserver l’installation.

## Vérification et limites

94 contrôles d’intégration multi et 43 contrôles généraux passent. Les essais vérifient six langues, dont le russe, sur un brouillon temporaire supprimé à la fin. La configuration réelle reste FR/EN. Le JavaScript a été vérifié sur un DOM simulé avec six onglets : onglet initial, clic, clavier et conservation des textes. Cela ne remplace pas une recette visuelle dans WordPress, notamment des éditeurs riches, des menus et des enregistrements réels.

Une sauvegarde fichiers + base existe dans .backups/2026-10-30_134721. Sa restauration complète n’a pas été testée. Git conserve le code, pas les contenus ACF. Après un enregistrement multi, un retour à l’ancien code seul ne reconvertit pas les données : il faut aussi restaurer ou convertir les champs concernés.

La fiche FICHE-PROJET.md et AUDIT.md conservent l’historique ; cette fiche et NOTES.md décrivent le fonctionnement actuel.
