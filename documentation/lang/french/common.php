<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
 */

####################################################
#                       French                     #
####################################################

$lang['documentation.module.title'] = 'Documentation';

// TreeLinks
$lang['item']  = 'fiche';
$lang['an.item']  = 'une fiche';
$lang['the.item']  = 'la fiche';
$lang['items.reorder']    = 'Réorganiser les fiches';
$lang['items.reordering'] = 'Réorganisation des fiches';

// Summary
$lang['documentation.summary'] = 'Table des matières';

// Titles
$lang['documentation.add.item']         = 'Ajouter une fiche';
$lang['documentation.edit.item']        = 'Modifier une fiche';
$lang['documentation.my.items']         = 'Mes fiches';
$lang['documentation.member.items']     = 'Fiches publiées par';
$lang['documentation.pending.items']    = 'Fiches en attente';
$lang['documentation.filter.items']     = 'Filtrer les fiches';
$lang['documentation.items.management'] = 'Gestion des fiches';
$lang['documentation.item.history']     = 'Historique de la fiche';
$lang['documentation.restore.item']     = 'Restaurer cette version';
$lang['documentation.restore.confirmation'] = 'Êtes-vous sûr de vouloir restaurer cette version ?';
$lang['documentation.history.init']     = 'Initialisation';
$lang['documentation.current.version']  = 'Version courrante';
$lang['documentation.delete.version']   = 'Supprimer cette version';
$lang['documentation.archive']          = 'Archive';
$lang['documentation.archived.item']    = 'Consulter';
$lang['documentation.archived.content'] = 'Cette fiche a été mise à jour, vous consultez ici une archive!';

// Levels
$lang['documentation.level'] = 'Niveau de confiance';

$lang['documentation.level.trust']  = 'Contenu de confiance';
$lang['documentation.level.claim']  = 'Contenu contesté';
$lang['documentation.level.redo']   = 'Contenu à refaire';
$lang['documentation.level.sketch'] = 'Contenu incomplet';
$lang['documentation.level.wip']    = 'Contenu en construction';

$lang['documentation.level.trust.message']  = 'Cette fiche est de grande qualité, elle est complète et fiable.';
$lang['documentation.level.claim.message']  = 'Cette fiche a été discutée et son contenu ne paraît pas correct. Vous pouvez éventuellement consulter les discussions à ce propos et peut-être y apporter vos connaissances.';
$lang['documentation.level.redo.message']   = 'Cette fiche est à refaire, son contenu n\'est pas très fiable.';
$lang['documentation.level.sketch.message'] = 'Cette fiche manque de sources.<br />Vos connaissances sont les bienvenues afin de le compléter.';
$lang['documentation.level.wip.message']    = 'Cette fiche est en cours de travaux, des modifications sont en cours de réalisation, revenez plus tard la reconsulter. Merci.';

$lang['documentation.level.custom'] = 'Niveau personnalisé';
$lang['documentation.level.custom.content'] = 'Description du niveau personalisé';

// Form
$lang['documentation.change.reason'] = 'Nature de la modification';

// Authorizations
$lang['documentation.manage.archives']     = 'Autorisation de gérer les archives';

// SEO
$lang['documentation.seo.description.root']    = 'Toutes les fiches en téléchargement du site :site.';
$lang['documentation.seo.description.tag']     = 'Toutes les fiches sur le sujet :subject.';
$lang['documentation.seo.description.pending'] = 'Toutes les fiches en attente.';
$lang['documentation.seo.description.member']  = 'Toutes les fiches de :author.';
$lang['documentation.seo.description.history'] = 'Historique de la fiche :item.';

// Messages helper
$lang['documentation.message.success.add']            = 'La fiche <b>:title</b> a été ajoutée';
$lang['documentation.message.success.edit']           = 'La fiche <b>:title</b> a été modifiée';
$lang['documentation.message.success.delete']         = 'La fiche <b>:title</b> a été supprimée';
$lang['documentation.message.success.delete.content'] = 'Le contenu :content de la fiche <b>:title</b> a été supprimé';
$lang['documentation.message.success.restore']        = 'Le contenu :content de la fiche <b>:title</b> a été restauré';
$lang['documentation.message.draft']            = '
    <div class="message-helper bgc warning">
        L\'édition d\'une fiche la place automatiquement en <b>brouillon</b>. Cela permet plusieurs validations sans en multiplier exécessivement les archives.
        <br /><br />
        <p>Pensez à modifier le status de publication en fin de travaux !</p>
    </div>
';
?>
