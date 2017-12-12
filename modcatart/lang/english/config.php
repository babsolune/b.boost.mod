<?php
/*##################################################
 *                               config.php
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

 ####################################################
 #						English						#
 ####################################################

$lang['root_category_description'] = 'Welcome to the modcatart section of the website!
<br /><br />
One category and one item were created to show you how this module works. Here are some tips to get started on this module.
<br /><br />
<ul class="formatter-ul">
	<li class="formatter-li"> To configure or customize the module homepage your module, go into the <a href="' . ModcatartUrlBuilder::configuration()->relative() . '">module administration</a></li>
	<li class="formatter-li"> To create categories, <a href="' . ModcatartUrlBuilder::add_category()->relative() . '">clic here</a></li>
	<li class="formatter-li"> To create items, <a href="' . ModcatartUrlBuilder::add_item()->relative() . '">clic here</a></li>
</ul>
<br />To learn more, please consult the documentation for the module on <a href="http://www.labsoweb.fr">Labsoweb</a> website.';
?>
