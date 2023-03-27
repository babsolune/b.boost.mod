# Wiki 2 Specs  

## Objectif  
    Passer le module wiki en mvc

## Spec  
- items
    titre | catégorie | contenu | contenu court | statut qualité | renommage de l'auteur | vignette | mots clés | sources | publication différée | Nature de modification
- catégories 
richCategories avec ajout des droits de gestion des archives  

## Fonctionnement
- Page d'accueil en catégories/sous-catégories
- Explorateur sur le modèle de la page d'accueil de pages

- Page de réagencements de l'ordre des items dans une catégorie (accessible si 2 items min)

- création de la table des matières à partir des balises [title/] du contenu  seulement si contenu contient des titres
    - pouvoir récupérer le html (couleur, icônes, etc) dans les titres de la table des matières et du contenu
- Envoyer la table des matières dans une colonne latérale
    - dans le menu gauche si colonne gauche et droite sont présentes
    - dans colonne droite si seulement colonne droite présente
    - création de la colonne gauche si aucune colonne présente
- si position fixe dans config activé
    - position fixe (cf wiki 6.0)

- création d'un nouveau contenu séparé 
- à la création un item ou d'une contribution
- à l'édition un item déjà publié
    - simple mise à jour du contenu courant quand on édite un item non publié => évite d'avoir x contenus inutiles pour un item
    - édition multiple d'une contribution avant publication
    - passer un item déjà publié en brouillon pour pouvoir valider +rs fois avant revalidation définitive

- statut d'un item 
    - aucun | qualité | incomplet | en cours | à refaire(obsolète) | contesté || personnalisé + textarea

## Item
### bdd module_items
- id
- id_category
- id_order
- rewrited_title
- creation_date
- published
- publishing_start_date
- publishing_end_date
- views_number

### bdd module_contents
- content_id
- item_id
- title
- thumbnail
- active_content
- content
- summary
- update_date
- author_custom_name
- author_user_id
- defined_status
- customized_status
- change_reason
- sources

# TODO (~~ = fait)
- ~~page d'accueil~~
- ~~table des matières~~
- ~~table des matières dans colonnes~~
- table des matières en position fixe
- ~~création item et contenu séparé~~
- ~~simple mise à jour du contenu d'un item en brouillon~~
- ~~statut~~
- historique 
    - général
    - ~~particulier~~
        - ~~ellipsis sur le change reason~~
- ~~suppression d'un item = suppression des relations (+rs contenus, favoris, historique)~~
- ~~recherche~~
- favoris
