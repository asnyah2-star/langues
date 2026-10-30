=== Atelier Langues ===
Requires PHP: 7.4
Stable tag: 0.2.0

== Point de reprise — 29 octobre 2026 ==

La première version bilingue fonctionne en local. La prochaine étape est la
recette des enregistrements et des interactions, puis leur amélioration.
39 contrôles automatisés réussis ; syntaxe PHP et JavaScript vérifiée.
Cela ne valide pas encore les formulaires réels, le mobile ou la production.

Quatre modules sont chargés : content.php, languages.php, admin.php et
site-settings.php. Le dernier ajoute Réglages du site : logo du menu,
e-mail, liens sociaux et pied de page FR/EN, avec les stockages existants.
Les logos de l’accueil et de Contact se règlent sur leurs pages.
Créations ouvre les disciplines ; /oeuvres/ redirige vers l’accueil.

La détection est désactivée par défaut dans le code, mais elle est actuellement
activée sur cette installation. URL explicite > choix mémorisé > navigateur.
Le catalogue contient maintenant 22 textes fixes ; les deux libellés
Agrandir l’image / Fermer l’image n’ont pas encore de traduction anglaise.

Priorités : fiabiliser les sauvegardes incomplètes et concurrentes, tester les
formulaires et les droits, relire les traductions, puis valider mobile/clavier.
18 images ont un texte alternatif vide : décider lesquelles sont informatives
avant de les décrire. L’anglais des métadonnées d’images reste à développer.

Lire d’abord la synthèse du 29 octobre en tête de NOTES.md, puis la fiche
FICHE-STAGE-LANGUES.md à la racine. AUDIT.md conserve l’audit historique.

== Fonctionnement ==

Extension locale français/anglais, sans API payante.

Ouvrir Réglages > Atelier Langues pour saisir les traductions côte à côte.
Les contenus français existants restent la référence ; médias et dates sont partagés.
Cocher la disponibilité anglaise après relecture. Un champ anglais vide conserve le français.

URL préfixées /fr/ et /en/, slugs communs dans cette première version.
Détection navigateur désactivée initialement et activable dans les réglages.

Lire AUDIT.md pour les limites, les vérifications effectuées et les tests manuels.
Les libellés des menus se traduisent dans Apparence > Menus, champ Libellé anglais.
Les libellés fixes recensés se traduisent dans Réglages > Atelier Langues > Textes du thème.
Le nom anglais des disciplines se saisit dans Créations > Disciplines > Modifier.
Le sélecteur FR/EN est proposé sur leur archive si le nom anglais est renseigné.
Les descriptions des disciplines et métadonnées des images ne sont pas encore traduites.
Le plugin n'est pas encore validé pour une mise en production.
