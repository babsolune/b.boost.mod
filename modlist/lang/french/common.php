<?php
/*##################################################
 *                        common.php
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
 #                      French					    #
 ####################################################

// Titles
$lang['modlist.module.title'] = 'Modlist';
$lang['modlist.item'] = 'Item';
$lang['modlist.items'] = 'Items';
$lang['module.config.title'] = 'Configuration des items';
$lang['modlist.management'] = 'Gestion des items';
$lang['modlist.add'] = 'Ajouter un item';
$lang['modlist.edit'] = 'Modification d\'un item';
$lang['modlist.feed.name'] = 'Derniers items';
$lang['modlist.pending.items'] = 'Items en attente';
$lang['modlist.published.items'] = 'Items publiés';
$lang['modlist.print.item'] = 'Impression d\'un item';
$lang['modlist.category.menu'] = 'Liste des catégories';

//Modlist configuration
$lang['modlist.configuration.cats.icon.display'] = 'Afficher l\'icône des catégories';
$lang['modlist.configuration.sort.filter.display'] = 'Afficher les filtres de tri';
$lang['modlist.configuration.suggestions.display'] = 'Afficher les suggestions d\'items';
$lang['modlist.configuration.suggestions.nb'] = 'Nombre d\'items suggérés à afficher';
$lang['modcat.configuration.navigation.links.display'] = 'Afficher la navigation des items connexes';
$lang['modcat.configuration.navigation.links.display.desc'] = 'Lien précédent, lien suivant';
$lang['modlist.configuration.characters.number.to.cut'] = 'Nombre de caractères pour couper le condensé de l\'item';
$lang['modlist.configuration.display.type'] = 'Type d\'affichage des items';
$lang['modlist.configuration.mosaic.type.display'] = 'Mosaïque';
$lang['modlist.configuration.list.type.display'] = 'Liste';
$lang['modlist.configuration.table.type.display'] = 'Tableau';
$lang['modlist.configuration.display.descriptions.to.guests'] = 'Afficher le condensé des items aux visiteurs s\'ils n\'ont pas l\'autorisation de lecture';

//Form
$lang['modlist.form.description'] = 'Description (maximum :number caractères)';
$lang['modlist.form.enabled.description'] = 'Activer le condensé de l\'item';
$lang['modlist.form.enabled.description.description'] = 'ou laissez PHPBoost couper le contenu à :number caractères';
$lang['modlist.form.carousel'] = 'Ajouter un carousel d\'images';
$lang['modlist.form.image.description'] = 'Description';
$lang['modlist.form.image.url'] = 'Adresse image';
$lang['modlist.form.enabled.author.name.customisation'] = 'Personnaliser le nom de l\'auteur';
$lang['modlist.form.custom.author.name'] = 'Nom de l\'auteur personnalisé';

//Sort fields title and mode
$lang['modlist.sort.field.views'] = 'Vues';
$lang['admin.modlist.sort.field.published'] = 'Publié';

//SEO
$lang['modlist.seo.description.root'] = 'Tous les items du site :site.';
$lang['modlist.seo.description.tag'] = 'Tous les items sur le sujet :subject.';
$lang['modlist.seo.description.pending'] = 'Tous les items en attente.';

//Messages
$lang['modlist.message.success.add'] = 'L\'item <b>:title</b> a été ajouté';
$lang['modlist.message.success.edit'] = 'L\'item <b>:title</b> a été modifié';
$lang['modlist.message.success.delete'] = 'L\'item <b>:title</b> a été supprimé';
?>
