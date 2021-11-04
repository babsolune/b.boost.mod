<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 10 30
 * @since       PHPBoost 6.0 - 2021 10 30
*/

 ####################################################
 #						English						#
 ####################################################

$lang['flux.module.title'] = 'RSS feeds';

$lang['item']  = 'feed';

$lang['flux.member.items']  = 'Feeds published by';
$lang['flux.my.items']      = 'My feeds';
$lang['flux.pending.items'] = 'Pending feeds';
$lang['flux.items.number']  = 'Feeds number';
$lang['flux.filter.items']  = 'Filter feeds';

$lang['flux.add']        = 'Add a feed';
$lang['flux.edit']       = 'Feed edition';
$lang['flux.management'] = 'Feeds management';

$lang['flux.website.infos']  = 'Infos about the website';
$lang['flux.website.xml']    = 'xml feed url';
$lang['flux.rss.init']       = 'The site feed has not been initialized.';
$lang['flux.rss.init.admin'] = 'The display of new elements from the site feeds is updated by clicking on the button.';
$lang['flux.check.updates']  = 'Check new site feed topics.';
$lang['flux.update']         = 'Update';

// Configuration
$lang['flux.module.name']               = 'Module title';
$lang['flux.rss.number']                = 'Feed items number per site';
$lang['flux.root.category.description'] = '
    Welcome to the flux section of the website!
    <br /><br />
    A category and a feed were created to show you how this module works. Here are some tips to get started on this module.
    <br /><br />
    <ul class="formatter-ul">
    	<li class="formatter-li"> To configure or customize the module homepage your module, go into the <a class="offload" href="' . FluxUrlBuilder::configuration()->relative() . '">module administration</a></li>
    	<li class="formatter-li"> To create categories, <a class="offload" href="' . CategoriessUrlBuilder::add()->relative() . '">clic here</a></li>
    	<li class="formatter-li"> To create feeds, <a class="offload" href="' . FluxUrlBuilder::add()->relative() . '">clic here</a></li>
    </ul>
    <br />To learn more, feel free to consult the documentation for the module on <a class="offload" href="https://www.phpboost.com">PHPBoost</a>.
';

// S.E.O.
$lang['flux.seo.description.member'] = 'All feeds published by :author.';
$lang['flux.seo.description.pending'] = 'All pending feeds.';

// Messages
$lang['flux.message.success.add']    = 'The feed <b>:name</b> has been added';
$lang['flux.message.success.edit']   = 'The feed <b>:name</b> has been modified';
$lang['flux.message.success.delete'] = 'The feed <b>:name</b> has been deleted';
?>
