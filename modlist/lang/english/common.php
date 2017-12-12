<?php
/*##################################################
 *                            common.php
 *                            -------------------
 *   begin                : Month XX, 2017
 *   copyright            : (C) 2017 Firstname LASTNAME
 *   email                : nickname@phpboost.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

/**
 * @author Firstname LASTNAME <nickname@phpboost.com>
 */

#####################################################
#                      English			    #
#####################################################

// Titles
$lang['modlist.module.title'] = 'Modlist';
$lang['modlist.item'] = 'Item';
$lang['modlist.items'] = 'Items';
$lang['module.config.title'] = 'Modlist configuration';
$lang['modlist.management'] = 'Modlist management';
$lang['modlist.add'] = 'Add an item';
$lang['modlist.edit'] = 'Item edition';
$lang['modlist.feed.name'] = 'Last items';
$lang['modlist.pending.items'] = 'Pending items';
$lang['modlist.published.items'] = 'Published items';
$lang['modlist.print.item'] = 'Print an item';

//Modlist configuration
$lang['modlist.configuration.cats.icon.display'] = 'Categories icon dipslay';
$lang['modlist.configuration.sort.filters.display'] = 'Sort filters display';
$lang['modlist.configuration.suggestions.display'] = 'Items suggestions display';
$lang['modlist.configuration.suggestions.nb'] = 'Suggested items number';
$lang['modcat.configuration.navigation.links.display'] = 'Navigation links display';
$lang['modcat.configuration.navigation.links.display.desc'] = 'Previous link, next link';
$lang['modlist.configuration.characters.number.to.cut'] = 'Maximum number of characters to cut the item\'s description';
$lang['modlist.configuration.display.type'] = 'Display type';
$lang['modlist.configuration.mosaic.type.display'] = 'Mosaic';
$lang['modlist.configuration.list.type.display'] = 'List';
$lang['modlist.configuration.table.type.display'] = 'Table';
$lang['modlist.configuration.display.descriptions.to.guests'] = 'Display condensed items to guests if they don\'t have read authorization';

//Form
$lang['modlist.form.description'] = 'Description (maximum :number characters)';
$lang['modlist.form.enabled.description'] = 'Enable item description';
$lang['modlist.form.enabled.description.description'] = 'or let PHPBoost cut the content at :number characters';
$lang['modlist.form.carousel'] = 'Ajouter un carousel d\'images';
$lang['modlist.form.image.description'] = 'Description';
$lang['modlist.form.image.url'] = 'Adresse image';
$lang['modlist.form.enabled.author.name.customisation'] = 'Personalize author name';
$lang['modlist.form.custom.author.name'] = 'Custom author name';

//Sort fields title and mode
$lang['modlist.sort.field.views'] = 'Views';
$lang['admin.modlist.sort.field.published'] = 'Published';

//SEO
$lang['modlist.seo.description.root'] = 'All :site\'s items.';
$lang['modlist.seo.description.tag'] = 'All :subject\'s items.';
$lang['modlist.seo.description.pending'] = 'All pending items.';

//Messages
$lang['modlist.message.success.add'] = 'The item <b>:title</b> has been added';
$lang['modlist.message.success.edit'] = 'The item <b>:title</b> has been modified';
$lang['modlist.message.success.delete'] = 'The item <b>:title</b> has been deleted';
?>
