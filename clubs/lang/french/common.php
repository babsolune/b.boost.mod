<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

 ####################################################
 #						French						#
 ####################################################

$lang['clubs.module.title'] = 'Clubs';

$lang['clubs.member.items']  = 'Clubs publiés par';
$lang['clubs.pending.items'] = 'Clubs en attente';
$lang['clubs.my.items']      = 'Mes clubs';
$lang['clubs.items.number']  = 'Nombre de clubs';

$lang['clubs.add']        = 'Ajouter un club';
$lang['clubs.edit']       = 'Modifier un club';
$lang['clubs.manage']     = 'Gérer les clubs';
$lang['clubs.management'] = 'Gestion des clubs';

$lang['clubs.visit.website'] = 'Visiter le site du club';
$lang['clubs.no.website']    = 'Aucun site répertorié';
$lang['clubs.link.infos']    = 'Informations sur le club';
$lang['clubs.contact']       = 'Contacter le club';
$lang['clubs.colors']        = 'Couleurs du club';
$lang['clubs.description']   = 'Description du club';
$lang['clubs.stadium.gps']   = 'Coordonnées GPS du stade';
$lang['clubs.stadium.lat']   = 'Latitude';
$lang['clubs.stadium.lng']   = 'Longitude';

// Form
$lang['clubs.short.title']           = 'Nom dans SCM';
$lang['clubs.short.title.clue']      = 'Nom du club affiché dans la gestion des résultats';
$lang['clubs.add.colors']            = 'Ajouter les couleurs du club';
$lang['clubs.stadium.location']      = 'Coordonnées GPS du stade';
$lang['clubs.stadium.location.desc'] = 'Remplissez le champ Adresse ou déplacez le pointeur.<br /> Seul le pointeur est nécessaire.';
$lang['clubs.stadium.address']       = 'Adresse du stade';
$lang['clubs.website.url']           = 'Adresse du site internet';
$lang['clubs.district']              = 'Comité du club';
$lang['clubs.logo']                  = 'Logo du club';
$lang['clubs.logo.mini']             = 'Mini logo';
$lang['clubs.logo.mini.desc']        = '<span style="color: var(--error-tone);">Max 32px de large</span><br /> S\'affiche sur la carte générale et le tableau de la liste des clubs.';
$lang['clubs.color.name']            = 'Nom de la couleur';
$lang['clubs.colors']                = 'Couleurs du club';
$lang['clubs.colors.desc']           = 'Le nom est optionnel. S\'il n\'est pas renseigné, il prend la valeur hexa de la couleur.';

// Configuration
$lang['clubs.root.category.description'] = '
    Bienvenue dans l\'espace du site consacré aux clubs !
    <br /><br />
    Une catégorie et un lien ont été créés pour vous montrer comment fonctionne ce module. Voici quelques conseils pour bien débuter sur ce module.
    <br /><br />
    <ul class="formatter-ul">
    	<li class="formatter-li"> Pour configurer ou personnaliser l\'accueil de votre module, rendez vous dans l\'<a href="' . ClubsUrlBuilder::configuration()->relative() . '">administration du module</a></li>
    	<li class="formatter-li"> Pour créer des catégories, <a href="' . CategoriesUrlBuilder::add()->relative() . '">cliquez ici</a> </li>
    	<li class="formatter-li"> Pour ajouter des liens, <a href="' . ClubsUrlBuilder::add()->relative() . '">cliquez ici</a></li>
    </ul>
    <br />Pour en savoir plus, n\'hésitez pas à consulter la documentation du module sur le site de <a href="http://www.phpboost.com">PHPBoost</a>.
';

// S.E.O.
$lang['clubs.seo.description.tag']     = 'Tous les clubs de la catégorie :subject.';
$lang['clubs.seo.description.pending'] = 'Tous les clubs en attente.';

// Messages
$lang['clubs.message.success.add']    = 'Le lien <b>:name</b> a été ajouté';
$lang['clubs.message.success.edit']   = 'Le lien <b>:name</b> a été modifié';
$lang['clubs.message.success.delete'] = 'Le lien <b>:name</b> a été supprimé';

// location
$lang['clubs.headquarter.address']      = 'Adresse du siège';
$lang['clubs.headquarter.address.clue'] = 'Remplissez le premier champ, et sélectionnez la valeur dans la liste déroulante, les infos sont envoyées dans les champs suivants.<br /> Modifiez si nécessaire ou remplissez directement les champs suivants.';
$lang['clubs.labels.enter.address']     = 'Entrez une adresse';
$lang['clubs.labels.street.number']     = 'Numéro';
$lang['clubs.labels.street.address']    = 'Rue, route, lieu-dit, ...';
$lang['clubs.labels.city']              = 'Ville';
$lang['clubs.labels.postal.code']       = 'Code Postal';
$lang['clubs.labels.phone']             = 'Téléphone';
$lang['clubs.labels.email']             = 'Email';

// Social Network
$lang['clubs.social.network']        = 'Réseaux sociaux';
$lang['clubs.labels.facebook']       = 'Adresse du compte Facebook <i class="fab fa-fw fa-facebook"></i>';
$lang['clubs.placeholder.facebook']  = 'https://www.facebook.com/...';
$lang['clubs.labels.twitter']        = 'Adresse du compte Twitter <i class="fab fa-fw fa-twitter"></i>';
$lang['clubs.placeholder.twitter']   = 'https://www.twitter.com/...';
$lang['clubs.labels.instagram']      = 'Adresse du compte Instagram <i class="fab fa-fw fa-instagram"></i>';
$lang['clubs.placeholder.instagram'] = 'https://www.instagram.com/...';
$lang['clubs.labels.youtube']        = 'Adresse du compte Youtube <i class="fab fa-fw fa-youtube"></i>';
$lang['clubs.placeholder.youtube']   = 'https://www.youtube.com/...';

// Warnings
$lang['clubs.no.gmap']            = 'Vous devez installer et activer le module GoogleMaps et le configurer (clé + lieu par défaut).';
$lang['clubs.no.default.address'] = 'L\'adresse par défaut n\'a pas été déclarée dans la configuration du module GoogleMaps.';
$lang['clubs.no.gps']             = 'Les coordonnées GPS du stade n\'ont pas été renseignées.';

?>
