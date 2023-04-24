<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 03 27
 * @since       PHPBoost 6.0 - 2022 11 18
 */

####################################################
#                       French                     #
####################################################

$lang['docsheet.module.title']   = 'Documentation';
$lang['docsheet.menu.title']     = 'Arborescence de la documentation';
$lang['docsheet.explorer']       = 'Explorateur';

// TreeLinks
$lang['item']               = 'fiche';
$lang['items']              = 'fiches';
$lang['an.item']            = 'une fiche';
$lang['the.item']           = 'la fiche';
$lang['items.reorder']      = 'Réorganiser les fiches';
$lang['items.reordering']   = 'Réorganisation des fiches';

// Table of contents
$lang['docsheet.contents.table']          = 'Table des matières';
$lang['docsheet.name']                    = 'Nom de la documentaiton';
$lang['docsheet.sticky.contents.table']   = 'Afficher la table des matières en position fixe';

// Titles
$lang['docsheet.root']               = 'Sans catégorie';
$lang['docsheet.add.item']           = 'Ajouter une fiche';
$lang['docsheet.edit.item']          = 'Modifier une fiche';
$lang['docsheet.my.items']           = 'Mes fiches';
$lang['docsheet.my.tracked']         = 'Mes favoris';
$lang['docsheet.member.items']       = 'Fiches publiées par';
$lang['docsheet.pending.items']      = 'Fiches en attente';
$lang['docsheet.filter.items']       = 'Filtrer les fiches';
$lang['docsheet.items.management']   = 'Gestion des fiches';
$lang['docsheet.item.history']       = 'Historique de la fiche';
$lang['docsheet.restore.item']       = 'Restaurer cette version';
$lang['docsheet.confirm.restore']    = 'Êtes-vous sûr de vouloir restaurer cette version ?';
$lang['docsheet.history.init']       = 'Initialisation';
$lang['docsheet.current.version']    = 'Version courrante';
$lang['docsheet.delete.version']     = 'Supprimer cette version';
$lang['docsheet.archive']            = 'Archive';
$lang['docsheet.archived.item']      = 'Consulter';
$lang['docsheet.archived.content']   = 'Cette fiche a été mise à jour, vous consultez ici une archive !';
$lang['docsheet.track']              = 'Suivre cette fiche';
$lang['docsheet.untrack']            = 'Ne plus suivre cette fiche';

// Levels
$lang['docsheet.level'] = 'Niveau de confiance';

$lang['docsheet.level.trust']  = 'Contenu de confiance';
$lang['docsheet.level.claim']  = 'Contenu contesté';
$lang['docsheet.level.redo']   = 'Contenu à refaire';
$lang['docsheet.level.sketch'] = 'Contenu incomplet';
$lang['docsheet.level.wip']    = 'Contenu en construction';

$lang['docsheet.level.trust.message']  = 'Cette fiche est de grande qualité, elle est complète et fiable.';
$lang['docsheet.level.claim.message']  = 'Cette fiche a été discutée et son contenu ne paraît pas correct. Vous pouvez éventuellement consulter les discussions à ce propos et peut-être y apporter vos connaissances.';
$lang['docsheet.level.redo.message']   = 'Cette fiche est à refaire, son contenu n\'est pas très fiable.';
$lang['docsheet.level.sketch.message'] = 'Cette fiche manque de sources.<br />Vos connaissances sont les bienvenues afin de le compléter.';
$lang['docsheet.level.wip.message']    = 'Cette fiche est en cours de travaux, des modifications sont en cours de réalisation, n`hésitez pas à revenir plus tard la consulter.';

$lang['docsheet.level.custom']           = 'Niveau personnalisé';
$lang['docsheet.level.custom.content']   = 'Description du niveau personalisé';

// Form
$lang['docsheet.change.reason']        = 'Nature de la modification';
$lang['docsheet.suggestions.number']   = 'Nombre d\'éléments suggérés à afficher';
$lang['docsheet.homepage']             = 'Choisir le type de page d\'accueil';
$lang['docsheet.homepage.categories']  = 'Catégories';
$lang['docsheet.homepage.explorer']    = 'Explorateur';

// Authorizations
$lang['docsheet.manage.archives'] = 'Autorisation de gérer les archives';

// SEO
$lang['docsheet.seo.description.root']      = 'Toutes les fiches du site :site.';
$lang['docsheet.seo.description.tag']       = 'Toutes les fiches sur le sujet :subject.';
$lang['docsheet.seo.description.pending']   = 'Toutes les fiches en attente.';
$lang['docsheet.seo.description.member']    = 'Toutes les fiches de :author.';
$lang['docsheet.seo.description.tracked']   = 'Toutes les fiches suivies de :author.';
$lang['docsheet.seo.description.history']   = 'Historique de la fiche :item.';

// Messages helper
$lang['docsheet.message.success.add']            = 'La fiche <b>:title</b> a été ajoutée';
$lang['docsheet.message.success.edit']           = 'La fiche <b>:title</b> a été modifiée';
$lang['docsheet.message.success.delete']         = 'La fiche <b>:title</b> a été supprimée';
$lang['docsheet.message.success.delete.content'] = 'Le contenu :content de la fiche <b>:title</b> a été supprimé';
$lang['docsheet.message.success.restore']        = 'Le contenu :content de la fiche <b>:title</b> a été restauré';
$lang['docsheet.message.draft']            = '
    <div class="message-helper bgc warning">
        L\'édition d\'une fiche la place automatiquement en <b>brouillon</b>. Cela permet plusieurs validations sans en multiplier excessivement les archives.
        <br /><br />
        <p>Pensez à modifier le status de publication en fin de travaux !</p>
    </div>
';
?>
