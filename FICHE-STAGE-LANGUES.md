# Atelier Langues — fiche de présentation au responsable de stage

28 octobre 2026 — WordPress local / WampServer

## Reprise du 29 octobre 2026

La version locale 0.2.0 est implémentée ; la prochaine étape est de terminer sa recette et de fiabiliser les sauvegardes. **39 contrôles automatisés réussis à nouveau**, syntaxe PHP du plugin/thème et JavaScript du thème valide. Les quatre modules comprennent maintenant `site-settings.php` pour le logo du menu, les coordonnées et le footer. Le guide de création d’une œuvre est déjà ajouté dans l’administration.

État de la base revérifié : quatre pages et six créations disponibles en anglais ; détection activée. Le menu Créations ouvre les disciplines et l’ancienne URL `/oeuvres/` redirige vers l’accueil. Deux des **22 textes fixes** restent sans anglais : Agrandir l’image et Fermer l’image. Relire aussi `movie theater`, le contenu partagé de Quelques créations et la signature de Contact. Les 18 images en médiathèque ont un alt vide ; leur rôle informatif ou décoratif doit être évalué.

Conseil prioritaire : préserver les traductions ACF lorsqu’un champ manque dans le formulaire reçu, puis protéger les sauvegardes simultanées entre Atelier Langues et Réglages du site. Ensuite, tester les enregistrements réels, les droits, le mobile et le clavier ; améliorer l’éditeur bilingue après cette validation. La production et la restauration de sauvegarde restent à valider. Les détails et limites de la revue figurent en tête de `wp-content/plugins/atelier-langues/NOTES.md` ; les sections suivantes conservent la présentation du 28 octobre.

## Objectif et état actuel

Créer une extension maison français/anglais, sans API payante, avec saisie manuelle des traductions. Le français est la langue par défaut. La première version fonctionne en local ; elle n’est pas encore validée pour la production.

## Étapes réalisées

1. Audit du thème `atelier-artiste`, des contenus, des menus et des champs ACF gratuit.
2. Création du plugin : fichier principal et trois modules PHP (`admin.php`, `content.php`, `languages.php`).
3. Interface de traduction côte à côte : titres, contenus, extraits et champs textuels ACF simples ; images et dates partagées.
4. Traduction des libellés de menus, noms des disciplines et textes fixes du thème.
5. URL `/fr/` et `/en/`, sélecteur de langue, repli français lorsqu’une traduction manque.
6. Détection navigateur et mémorisation du choix manuel par cookie.
7. Intégration au thème : navigation par disciplines, Contact/footer administrables, logos facultatifs, galerie et dimensions des images.
8. Vérifications : 39 contrôles automatisés réussis lors de cette revue. Lors de la revue générale précédente, 28 URL publiques ont été testées ; elles n’ont pas toutes été retestées aujourd’hui. Les essais ne remplacent pas la recette visuelle et les tests de sauvegarde avec différents rôles.

## Langue du navigateur et cookie : deux choses différentes

L’en-tête HTTP **Accept-Language**, envoyé par le navigateur, donne ses préférences linguistiques. Le plugin lit cet en-tête ; aucun cookie n’est nécessaire pour cette détection.

Le cookie **`atelier_langues_choice`** mémorise un clic volontaire sur FR ou EN. Valeur : `fr` ou `en`. Il permet de retrouver ce choix à la prochaine visite d’une URL sans préfixe.

Priorités :

1. URL explicite `/fr/` ou `/en/`.
2. Sur une URL sans préfixe : cookie de choix, si la détection est activée.
3. Sinon : préférence fr/en de l’en-tête Accept-Language, pondérée par les valeurs `q`.
4. À défaut : français.

Si une page n’a pas d’anglais disponible, le plugin revient à sa version française. Une traduction absente dans un champ peut également reprendre le français.

Le réglage de détection est **actuellement activé**. Chemin : **Réglages → Atelier Langues → Détection de la langue**. Code : `wp-content/plugins/atelier-langues/includes/languages.php`.

Cookie : durée de 180 jours, chemin `/tamaraszefer/`, HttpOnly, SameSite=Lax ; Secure lorsque le site utilise HTTPS (le site local est en HTTP). HttpOnly empêche sa lecture par `document.cookie`, mais il reste visible dans les outils du navigateur. Aucun bandeau ou gestionnaire de consentement n’a été développé dans cette étape.

## Où vérifier dans le navigateur

- Ouvrir le site, cliquer sur EN, puis ouvrir les outils de développement avec F12.
- Si F12 déclenche une fonction du PC : utiliser **Ctrl + Maj + I**, ou **⋮ → Plus d’outils → Outils de développement** (Chrome/Edge).
- Chrome/Edge : **Application → Stockage → Cookies → http://localhost**. Firefox : **Stockage → Cookies**. Chercher `atelier_langues_choice` et contrôler la valeur `en`.
- Dans **Réseau / Network**, recharger la page et sélectionner la requête du document HTML : **En-têtes de requête → Accept-Language** et **Cookie**. Le clic sur EN passe par une réponse 302 qui émet **Set-Cookie**, puis enlève le paramètre `atelier_choice` de l’URL.
- Pour tester la première visite : supprimer uniquement ce cookie, régler les langues préférées du navigateur, puis visiter `http://localhost/tamaraszefer/` sans `/fr/` ou `/en/`.
- Tester ensuite le choix manuel : cliquer FR, revisiter l’adresse sans préfixe ; le français doit être conservé même avec un navigateur anglais.
- Enfin, ouvrir directement `/en/` malgré ce choix français : l’URL explicite doit rester prioritaire.

## Base de données : où sont les textes ?

Pas de table supplémentaire. Les API WordPress et ACF utilisent les tables existantes (leur préfixe dépend de l’installation).

| Donnée | Stockage |
| --- | --- |
| Titre, contenu et extrait français | Table `posts`, colonnes `post_title`, `post_content`, `post_excerpt` |
| Valeurs ACF françaises et références de champs | Table `postmeta`, selon le stockage ACF existant |
| Traduction anglaise de la même fiche | Table `postmeta`, clé `_atelier_langues_en`, associée au même ID ; tableau sérialisé géré par WordPress |
| Libellé anglais d’une entrée de menu | Table `postmeta`, clé `_atelier_langues_menu_en` sur l’entrée de menu |
| Nom anglais d’une discipline | Table `termmeta`, clé `_atelier_langues_name_en` |
| Libellés fixes FR/EN du thème | Table `options`, option `atelier_langues_theme_strings` |
| Activation de la détection | Table `options`, option `atelier_langues_detect_browser` |
| Préférence personnelle du visiteur | Cookie dans son navigateur, pas une ligne en base |

Dans phpMyAdmin, on peut consulter ces tables en lecture seule. Pour modifier les textes, utiliser l’administration WordPress : ne pas éditer à la main les tableaux sérialisés.

## Pourquoi pas de balises <fr> et <en> ?

`<fr>` et `<en>` ne sont pas des balises HTML standard qui choisissent automatiquement une langue. Elles nécessiteraient notre propre traitement PHP ou JavaScript.

Notre choix est de séparer les valeurs en base, puis de sélectionner la bonne valeur en PHP avant l’envoi du HTML. **ACF fournit les champs ; le plugin réalise la sélection de langue.** ACF seul ne rend pas le site bilingue. Les titres et contenus WordPress sont aussi traduits sans dépendre d’ACF.

Le navigateur reçoit une page avec `<html lang="fr">` ou `<html lang="en">`. Les fonctions natives de localisation WordPress sont utilisées pour les chaînes fixes du thème. Il n’est donc pas nécessaire d’entourer chaque texte de deux conteneurs linguistiques.

## Pour terminer la première version aujourd’hui

Priorité : une recette complète plutôt que de nouvelles fonctions.

1. Relire les traductions et leurs cases de disponibilité ; vérifier les liens de chaque discipline, les créations, Contact et le footer.
2. Décider si Doux Chaos reste identique dans les deux langues ; relire la traduction Cinema/Film plutôt que Movie theater pour la discipline.
3. Exécuter les trois essais de langue ci-dessus et noter le résultat.
4. Tester les sauvegardes puis le rendu mobile, le clavier, les flèches du carrousel et les agrandissements d’images.
5. Vérifier les textes alternatifs des images, puis sauvegarder **les fichiers et la base de données** avec l’outil habituel avant livraison au responsable.

À prévoir avant une mise en ligne : revue des permissions et sauvegardes concurrentes, traitement SEO des archives et sitemaps, vérification du cache/HTTPS, éventuels slugs anglais. Les descriptions des disciplines, traductions des légendes/textes alternatifs et champs ACF complexes ne sont pas encore couverts. Le type `oeuvre` est encore déclaré dans le thème : ses données persistent en changeant de thème, mais sa déclaration doit être déplacée pour l’éditer sous un autre thème.

Notes détaillées : `wp-content/plugins/atelier-langues/NOTES.md` et `AUDIT.md`.
