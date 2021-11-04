<?php
/**
 * @copyright   &copy; 2005-2021 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

 ####################################################
 #						English						#
 ####################################################

$lang['clubs.module.title'] = 'Clubs';

$lang['clubs.member.items']  = 'Clubs published by';
$lang['clubs.pending.items'] = 'Pending clubs';
$lang['clubs.my.items']      = 'My clubs';
$lang['clubs.items.number']  = 'Clubs number';

$lang['clubs.add']        = 'Add club';
$lang['clubs.edit']       = 'Club edition';
$lang['clubs.manage']     = 'Manage clubs';
$lang['clubs.management'] = 'Clubs management';

$lang['clubs.visit.website'] = 'Visit club website';
$lang['clubs.no.website']    = 'No site listed';
$lang['clubs.link.infos']    = 'Informations about the club';
$lang['clubs.contact']       = 'Contact the club';
$lang['clubs.colors']        = 'Club colors';
$lang['clubs.description']   = 'Club description';
$lang['clubs.stadium.gps']   = 'GPS coordinates of the stadium ';
$lang['clubs.stadium.lat']   = 'Latitude';
$lang['clubs.stadium.lng']   = 'Longitude';

// Form
$lang['clubs.short.title']           = 'Name in SCM';
$lang['clubs.short.title.clue']      = 'Club nale displayed in results management';
$lang['clubs.add.colors']            = 'Add colors of the club';
$lang['clubs.stadium.location']      = 'GPS coordinates of the stadium ';
$lang['clubs.stadium.location.desc'] = 'Fill in the first field, and select the value from the drop-down list, the information is sent in the following fields. <br /> Modify if necessary or fill in the following fields directly. ';
$lang['clubs.stadium.address']       = 'Stadium address';
$lang['clubs.website.url']           = 'Website';
$lang['clubs.district']              = 'Club committee';
$lang['clubs.logo']                  = 'Club logo';
$lang['clubs.logo.mini']             = 'Mini logo';
$lang['clubs.logo.mini.desc']        = '<span style="color: var(--error-tone);">Max 32px wide</span><br /> Displayed on the general map and the club list table.';
$lang['clubs.color.name']            = 'Color name';
$lang['clubs.colors']                = 'Club colors';
$lang['clubs.colors.desc']           = 'The name is optionnal. I f not filled, it takes the hexa value of the color.';

// Configuration
$lang['clubs.root.category.description'] = '
    Welcome to the clubs section of the website!
    <br /><br />
    One category and one link were created to show you how this module works. Here are some tips to get started on this module.
    <br /><br />
    <ul class="formatter-ul">
    	<li class="formatter-li"> To configure or customize the module homepage your module, go into the <a href="' . ClubsUrlBuilder::configuration()->relative() . '">module administration</a></li>
    	<li class="formatter-li"> To create categories, <a href="' . CategoriessUrlBuilder::add()->relative() . '">clic here</a></li>
    	<li class="formatter-li"> To create links, <a href="' . ClubsUrlBuilder::add()->relative() . '">clic here</a></li>
    </ul>
    <br />To learn more, don \'t hesitate to consult the documentation for the module on <a href="http://www.phpboost.com">PHPBoost</a> clubssite.
';

// S.E.O.
$lang['clubs.seo.description.tag']     = 'All clubs of :subject.';
$lang['clubs.seo.description.pending'] = 'All pending clubs.';

// Messages
$lang['clubs.message.success.add']    = 'The club <b>:name</b> has been added';
$lang['clubs.message.success.edit']   = 'The club <b>:name</b> has been modified';
$lang['clubs.message.success.delete'] = 'The club <b>:name</b> has been deleted';

// location
$lang['clubs.headquarter.address']      = 'Headquarter address';
$lang['clubs.headquarter.address.clue'] = 'Fill in the first field, and select the value from the drop-down list, the information is sent in the following fields. <br /> Modify if necessary or fill in the following fields directly. ';
$lang['clubs.labels.enter.address']     = 'Enter an address';
$lang['clubs.labels.street.number']     = 'Street number';
$lang['clubs.labels.street.address']    = 'Street name';
$lang['clubs.labels.city']              = 'City';
$lang['clubs.labels.postal.code']       = 'Zip code';
$lang['clubs.labels.phone']             = 'Telephone';
$lang['clubs.labels.email']             = 'Email';

// Social Network
$lang['clubs.social.network']        = 'Social networks';
$lang['clubs.labels.facebook']       = 'Facebook <i class="fab fa-fw fa-facebook"></i>';
$lang['clubs.placeholder.facebook']  = 'https://www.facebook.com/...';
$lang['clubs.labels.twitter']        = 'Twitter <i class="fab fa-fw fa-twitter"></i>';
$lang['clubs.placeholder.twitter']   = 'https://www.twitter.com/...';
$lang['clubs.labels.instagram']      = 'Instagram <i class="fab fa-fw fa-instagram"></i>';
$lang['clubs.placeholder.instagram'] = 'https://www.instagram.com/...';
$lang['clubs.labels.youtube']        = 'Youtube <i class="fab fa-fw fa-youtube"></i>';
$lang['clubs.placeholder.youtube']   = 'https://www.youtube.com/...';

// Warnings
$lang['clubs.no.gmap']            = 'You must install and activate the GoogleMaps module and configure it (key + location by default).';
$lang['clubs.no.default.address'] = 'The default origin address has not been declared in the GoogleMaps module configuration.';
$lang['clubs.no.gps']             = 'GPS coordinates of the stadium have not been entered.';

?>
