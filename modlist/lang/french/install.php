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
#                      French			    #
####################################################

$lang['default.category.name'] = 'Catégorie de test';
$lang['default.category.description'] = 'Items de démonstration';
$lang['default.itemlist.title'] = 'Modlist, un module générique pour PHPBoost ' . GeneralConfig::load()->get_phpboost_major_version();
$lang['default.itemlist.description'] = '';
$lang['default.itemlist.contents'] = 'Ce module a été conçu pour vous aider à créer des modules avec un affichage par liste d\'items. <br />
Afin de l\'adapter à vos besoins, vous devez modifier toutes les occurences dans les fichiers ET dans les noms de fichiers <strong>en respectant bien la casse</strong> :
<br />
Le nom du module à remplacer : <ul class="formatter-ul"><li class="formatter-li">Modlist</li><li class="formatter-li">modlist</li></ul>
Le nom de la classe à remplacer : <ul class="formatter-ul"><li class="formatter-li">Itemlist</li><li class="formatter-li">itemlist</li></ul>
<br />
Certains noms sont à utiliser avec précaution pour des raisons évidentes de compatibilité avec les méthodes ou les fonctions php du noyau de PHPBoost ou des modules déjà installés, ou les classes et propriétés Css et Html : class, item, clone, event ... (liste non-exaustive). Faîtes une vérification de nom dans l\'ensemble du cms avant de vous lancer.
<br />
<br />
Ce module propose une présentation par liste, affichant la liste de tous les items sur sa page d\'accueil. Vous y retrouverez également de nombreuses fonctionnalités que vous pourrez utiliser, modifier, supprimer en fonction de vos besoins (catégories, contribution, commentaires, nb de vue, etc...).
<br />
Bon clonage.
';

?>
