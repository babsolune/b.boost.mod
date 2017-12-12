<?php
/*##################################################
 *                            install.php
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
####################################################

$lang['default.category.name'] = 'Test category';
$lang['default.category.description'] = 'Demonstration items';
$lang['default.itemmix.title'] = 'ModMix, a generic module for PHPBoost ' . GeneralConfig::load()->get_phpboost_major_version();
$lang['default.itemmix.description'] = '';
$lang['default.itemmix.contents'] = 'This module has been developed to help you create modules with a display by categories. <br />
To adapt it to your needs, you have to modify all the occurrences in the files AND in the names of files <strong>in the respect of the case</strong> :
<br />
Module name to replace : <ul class="formatter-ul"><li class="formatter-li">Modmix</li><li class="formatter-li">modmix</li></ul>
Class name to replace : <ul class="formatter-ul"><li class="formatter-li">Itemmix</li><li class="formatter-li">itemmix</li></ul>
<br />
Take care of your name choice for obviously reasons of compatibility with php methods or functions of the phpboost core or with allready installed modules, or with css/html class and properties : class, item, clone, event ... . Make a global search on your name before processing.
<br />
<br />
This module offers a dual presentation : the full list of categories with their own items on its home page and the list of items in categories and sub-categories. You will also find many features you can use, modify, or delete to suit your needs (categories, contribution, comments, number of views, and so on...).
<br />
Enjoy.';

?>
