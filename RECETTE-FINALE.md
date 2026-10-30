# Recette locale — 28 octobre 2026

## Nouvelle revue — 29 octobre 2026

39 contrôles automatisés réussis ; syntaxe PHP du plugin et du thème et JavaScript du thème valide. 24 requêtes HTTP vérifiées : accueil, À propos, Contact, quatre disciplines et Vision nocturne en FR/EN (200) ; ancienne liste `/oeuvres/` vers l’accueil de la même langue (302) ; détection navigateur, priorité du cookie et de l’URL explicite conformes ; clic EN avec émission du cookie et nettoyage de l’URL ; 404 inconnue et accès `.backups/` interdit (403). Aucune erreur PHP visible sur ces réponses. Deux lectures supplémentaires confirment la langue du document HTML sur les deux accueils. Les autres fiches n’ont pas toutes été retestées aujourd’hui.

Deux libellés d’image restent à traduire sur les 22 textes fixes ; 18 images ont un alt vide en médiathèque. La présence du SQL, du ZIP et du manifeste du 28 octobre a été constatée ; restauration et empreintes non revérifiées. Les essais manuels ci-dessous restent nécessaires. Avant livraison, corriger également la conservation des traductions ACF absentes d’un formulaire et renforcer la gestion des sauvegardes simultanées (détails dans la synthèse du 29 octobre de NOTES.md).

## Résultats conservés du 28 octobre

- Contact : logo réduit à 140 px ; Montserrat chargée localement avec sa licence. Source : https://github.com/google/fonts/tree/main/ofl/montserrat. Les autres pages conservent leurs polices actuelles.
- Langues : navigateur anglais sans cookie → /en/ ; cookie français → /fr/ ; URL /en/ explicite malgré cookie français → réponse 200 en anglais. Essais HTTP exécutés.
- Tests du plugin : 39 contrôles réussis après rétablissement du sélecteur sur les disciplines.
- Images : 18 images sans texte alternatif répertoriées dans IMAGES-A-RELIRE.md ; descriptions à valider, sans remplissage automatique.
- Police : fichier local accessible en HTTP 200.
- Sauvegarde : SQL et archive des fichiers dans `.backups`, avec manifeste et empreintes ; accès HTTP interdit (403). Archive contrôlée en lecture, restauration non testée. Garder également une copie hors du dossier du site avant publication.

## Essais manuels restants

1. Chrome/Edge : Ctrl+Maj+I (ou ⋮ → Plus d’outils → Outils de développement), Application → Cookies → localhost. Cliquer EN puis FR et vérifier `atelier_langues_choice`.
2. Ctrl+Maj+M pour simuler un téléphone : accueil, menu, discipline, fiche, Contact et footer. Vérifier l’absence de défilement horizontal, les logos et les flèches. Tester aussi un téléphone réel si possible.
3. Avec Tab, Entrée et Échap : parcourir le menu, ouvrir/fermer Créations, changer de langue et utiliser le carrousel.
4. Enregistrer une modification de test et vérifier sa persistance, puis tester un compte aux droits réduits.
5. Compléter les alt informatifs depuis la médiathèque ; garder vides ceux des images décoratives.

La tentative de capture mobile automatique a échoué au démarrage graphique de Chrome ; le rendu mobile n’est donc pas déclaré validé. Le référencement complet, les slugs anglais et les alt bilingues restent à traiter avant publication.
