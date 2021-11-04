<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 10 30
 * @since       PHPBoost 6.0 - 2021 10 30
*/

 ####################################################
 #						French						#
 ####################################################

$lang['flux.module.title'] = 'Flux RSS';

$lang['items'] = 'flux';
$lang['item']  = 'flux';

$lang['flux.member.items']  = 'Flux publiés par';
$lang['flux.my.items']      = 'Mes flux';
$lang['flux.pending.items'] = 'Flux en attente';
$lang['flux.items.number']  = 'Nombre de flux';
$lang['flux.filter.items']  = 'Filtrer les flux';

$lang['flux.add']        = 'Ajouter un flux';
$lang['flux.edit']       = 'Modifier un flux';
$lang['flux.management'] = 'Gestion des flux';

$lang['flux.website.infos']  = 'Infos sur le site';
$lang['flux.website.xml']    = 'Url du flux xml';
$lang['flux.rss.init']       = 'Le flux du site n\'a pas été initialisé.';
$lang['flux.rss.init.admin'] = 'L\'affichage des nouveaux éléments issus des flux du site est mis à jour en cliquant sur le bouton.';
$lang['flux.check.updates']  = 'Vérifier les nouveaux sujets sur le site';
$lang['flux.update']         = 'Mettre à jour';

// Configuration
$lang['flux.module.name']               = 'Titre du module';
$lang['flux.rss.number']                = 'Nombre d\'éléments de flux par site';
$lang['flux.root.category.description'] = '
    Bienvenue dans l\'espace du site consacré aux flux !
    <br /><br />
    Une catégorie et une flux ont été créés pour vous montrer comment fonctionne ce module. Voici quelques conseils pour bien débuter sur ce module.
    <br /><br />
    <ul class="formatter-ul">
    	<li class="formatter-li"> Pour configurer ou personnaliser l\'accueil de votre module, rendez vous dans l\'<a class="offload" href="' . FluxUrlBuilder::configuration()->relative() . '">administration du module</a></li>
    	<li class="formatter-li"> Pour créer des catégories, <a class="offload" href="' . CategoriesUrlBuilder::add()->relative() . '">cliquez ici</a> </li>
    	<li class="formatter-li"> Pour ajouter des flux, <a class="offload" href="' . FluxUrlBuilder::add()->relative() . '">cliquez ici</a></li>
    </ul>
    <br />Pour en savoir plus, n\'hésitez pas à consulter la documentation du module sur le site de <a class="offload" href="https://www.phpboost.com">PHPBoost</a>.
';

// S.E.O.
$lang['flux.seo.description.member'] = 'Toutes les flux publiés par :author.';
$lang['flux.seo.description.pending'] = 'Toutes les flux en attente.';

// Messages
$lang['flux.message.success.add']    = 'Le flux <b>:name</b> a été ajouté';
$lang['flux.message.success.edit']   = 'Le flux <b>:name</b> a été modifié';
$lang['flux.message.success.delete'] = 'Le flux <b>:name</b> a été supprimé';
?>
