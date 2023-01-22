<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 01 21
 * @since       PHPBoost 6.0 - 2023 01 21
 */

####################################################
#                       French                     #
####################################################

$lang['guide.module.title'] = 'Guide';
$lang['guide.menu.title'] = 'Arborescence de la guide';

// TreeLinks
$lang['item']  = 'fiche';
$lang['an.item']  = 'une fiche';
$lang['the.item']  = 'la fiche';
$lang['items.reorder']    = 'Réorganiser les fiches';
$lang['items.reordering'] = 'Réorganisation des fiches';

// Summary
$lang['guide.summary'] = 'Table des matières';

// Titles
$lang['guide.add.item']         = 'Ajouter une fiche';
$lang['guide.edit.item']        = 'Modifier une fiche';
$lang['guide.my.items']         = 'Mes fiches';
$lang['guide.member.items']     = 'Fiches publiées par';
$lang['guide.pending.items']    = 'Fiches en attente';
$lang['guide.filter.items']     = 'Filtrer les fiches';
$lang['guide.items.management'] = 'Gestion des fiches';
$lang['guide.item.history']     = 'Historique de la fiche';
$lang['guide.restore.item']     = 'Restaurer cette version';
$lang['guide.restore.confirmation'] = 'Êtes-vous sûr de vouloir restaurer cette version ?';
$lang['guide.history.init']     = 'Initialisation';
$lang['guide.current.version']  = 'Version courrante';
$lang['guide.delete.version']   = 'Supprimer cette version';
$lang['guide.archive']          = 'Archive';
$lang['guide.archived.item']    = 'Consulter';
$lang['guide.archived.content'] = 'Cette fiche a été mise à jour, vous consultez ici une archive!';

// Levels
$lang['guide.level'] = 'Niveau de confiance';

$lang['guide.level.trust']  = 'Contenu de confiance';
$lang['guide.level.claim']  = 'Contenu contesté';
$lang['guide.level.redo']   = 'Contenu à refaire';
$lang['guide.level.sketch'] = 'Contenu incomplet';
$lang['guide.level.wip']    = 'Contenu en construction';

$lang['guide.level.trust.message']  = 'Cette fiche est de grande qualité, elle est complète et fiable.';
$lang['guide.level.claim.message']  = 'Cette fiche a été discutée et son contenu ne paraît pas correct. Vous pouvez éventuellement consulter les discussions à ce propos et peut-être y apporter vos connaissances.';
$lang['guide.level.redo.message']   = 'Cette fiche est à refaire, son contenu n\'est pas très fiable.';
$lang['guide.level.sketch.message'] = 'Cette fiche manque de sources.<br />Vos connaissances sont les bienvenues afin de le compléter.';
$lang['guide.level.wip.message']    = 'Cette fiche est en cours de travaux, des modifications sont en cours de réalisation, revenez plus tard la reconsulter. Merci.';

$lang['guide.level.custom'] = 'Niveau personnalisé';
$lang['guide.level.custom.content'] = 'Description du niveau personalisé';

// Form
$lang['guide.change.reason'] = 'Nature de la modification';

// Authorizations
$lang['guide.manage.archives']     = 'Autorisation de gérer les archives';

// SEO
$lang['guide.seo.description.root']    = 'Toutes les fiches en téléchargement du site :site.';
$lang['guide.seo.description.tag']     = 'Toutes les fiches sur le sujet :subject.';
$lang['guide.seo.description.pending'] = 'Toutes les fiches en attente.';
$lang['guide.seo.description.member']  = 'Toutes les fiches de :author.';
$lang['guide.seo.description.history'] = 'Historique de la fiche :item.';

// Messages helper
$lang['guide.message.success.add']            = 'La fiche <b>:title</b> a été ajoutée';
$lang['guide.message.success.edit']           = 'La fiche <b>:title</b> a été modifiée';
$lang['guide.message.success.delete']         = 'La fiche <b>:title</b> a été supprimée';
$lang['guide.message.success.delete.content'] = 'Le contenu :content de la fiche <b>:title</b> a été supprimé';
$lang['guide.message.success.restore']        = 'Le contenu :content de la fiche <b>:title</b> a été restauré';
$lang['guide.message.draft']            = '
    <div class="message-helper bgc warning">
        L\'édition d\'une fiche la place automatiquement en <b>brouillon</b>. Cela permet plusieurs validations sans en multiplier exécessivement les archives.
        <br /><br />
        <p>Pensez à modifier le status de publication en fin de travaux !</p>
    </div>
';
?>
