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
$lang['modcat.module.title'] = 'Modcat';
$lang['modcat.item'] = 'Item';
$lang['modcat.items'] = 'Items';
$lang['module.config.title'] = 'Modcat configuration';
$lang['modcat.management'] = 'Modcat management';
$lang['modcat.add'] = 'Add an item';
$lang['modcat.edit'] = 'Item edition';
$lang['modcat.feed.name'] = 'Last items';
$lang['modcat.pending.items'] = 'Pending items';
$lang['modcat.published.items'] = 'Published items';
$lang['modcat.print.item'] = 'Print an item';

//Modcat configuration
$lang['modcat.configuration.cats.icon.display'] = 'Categories icon dipslay';
$lang['modcat.configuration.sort.filters.display'] = 'Sort filters display';
$lang['modcat.configuration.suggestions.display'] = 'Items suggestions display';
$lang['modcat.configuration.suggestions.nb'] = 'Suggested items number';
$lang['modcat.configuration.navigation.links.display'] = 'Navigation links display';
$lang['modcat.configuration.navigation.links.display.desc'] = 'Previous link, next link';
$lang['modcat.configuration.characters.number.to.cut'] = 'Maximum number of characters to cut the item\'s description';
$lang['modcat.configuration.display.type'] = 'Display type';
$lang['modcat.configuration.mosaic.type.display'] = 'Mosaic';
$lang['modcat.configuration.list.type.display'] = 'List';
$lang['modcat.configuration.table.type.display'] = 'Table';
$lang['modcat.configuration.display.descriptions.to.guests'] = 'Display condensed items to guests if they don\'t have read authorization';

//Form
$lang['modcat.form.description'] = 'Description (maximum :number characters)';
$lang['modcat.form.enabled.description'] = 'Enable item description';
$lang['modcat.form.enabled.description.description'] = 'or let PHPBoost cut the content at :number characters';
$lang['modcat.form.carousel'] = 'Ajouter un carousel d\'images';
$lang['modcat.form.image.description'] = 'Description';
$lang['modcat.form.image.url'] = 'Adresse image';
$lang['modcat.form.enabled.author.name.customisation'] = 'Personalize author name';
$lang['modcat.form.custom.author.name'] = 'Custom author name';

//Sort fields title and mode
$lang['modcat.sort.field.views'] = 'Views';
$lang['admin.modcat.sort.field.published'] = 'Published';

//SEO
$lang['modcat.seo.description.root'] = 'All :site\'s items.';
$lang['modcat.seo.description.tag'] = 'All :subject\'s items.';
$lang['modcat.seo.description.pending'] = 'All pending items.';

//Messages
$lang['modcat.message.success.add'] = 'The item <b>:title</b> has been added';
$lang['modcat.message.success.edit'] = 'The item <b>:title</b> has been modified';
$lang['modcat.message.success.delete'] = 'The item <b>:title</b> has been deleted';
?>
