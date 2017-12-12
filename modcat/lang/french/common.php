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
$lang['modcat.module.title'] = 'Modcat';
$lang['modcat.item'] = 'Item';
$lang['modcat.items'] = 'Items';
$lang['module.config.title'] = 'Configuration des items';
$lang['modcat.management'] = 'Gestion des items';
$lang['modcat.add'] = 'Ajouter un item';
$lang['modcat.edit'] = 'Modification d\'un item';
$lang['modcat.feed.name'] = 'Derniers items';
$lang['modcat.pending.items'] = 'Items en attente';
$lang['modcat.published.items'] = 'Items publiés';
$lang['modcat.print.item'] = 'Impression d\'un item';

//Modcat configuration
$lang['modcat.configuration.cats.icon.display'] = 'Afficher l\'icône des catégories';
$lang['modcat.configuration.sort.filter.display'] = 'Afficher les filtres de tri';
$lang['modcat.configuration.suggestions.display'] = 'Afficher les suggestions d\'items';
$lang['modcat.configuration.suggestions.nb'] = 'Nombre d\'items suggérés à afficher';
$lang['modcat.configuration.navigation.links.display'] = 'Afficher la navigation des items connexes';
$lang['modcat.configuration.navigation.links.display.desc'] = 'Lien précédent, lien suivant';
$lang['modcat.configuration.characters.number.to.cut'] = 'Nombre de caractères pour couper le condensé de l\'item';
$lang['modcat.configuration.display.type'] = 'Type d\'affichage des items';
$lang['modcat.configuration.mosaic.type.display'] = 'Mosaïque';
$lang['modcat.configuration.list.type.display'] = 'Liste';
$lang['modcat.configuration.table.type.display'] = 'Tableau';
$lang['modcat.configuration.display.descriptions.to.guests'] = 'Afficher le condensé des items aux visiteurs s\'ils n\'ont pas l\'autorisation de lecture';

//Form
$lang['modcat.form.description'] = 'Description (maximum :number caractères)';
$lang['modcat.form.enabled.description'] = 'Activer le condensé de l\'item';
$lang['modcat.form.enabled.description.description'] = 'ou laissez PHPBoost couper le contenu à :number caractères';
$lang['modcat.form.carousel'] = 'Ajouter un carousel d\'images';
$lang['modcat.form.image.description'] = 'Description';
$lang['modcat.form.image.url'] = 'Adresse image';
$lang['modcat.form.enabled.author.name.customisation'] = 'Personnaliser le nom de l\'auteur';
$lang['modcat.form.custom.author.name'] = 'Nom de l\'auteur personnalisé';

//Sort fields title and mode
$lang['modcat.sort.field.views'] = 'Vues';
$lang['admin.modcat.sort.field.published'] = 'Publié';

//SEO
$lang['modcat.seo.description.root'] = 'Tous les items du site :site.';
$lang['modcat.seo.description.tag'] = 'Tous les items sur le sujet :subject.';
$lang['modcat.seo.description.pending'] = 'Tous les items en attente.';

//Messages
$lang['modcat.message.success.add'] = 'L\'item <b>:title</b> a été ajouté';
$lang['modcat.message.success.edit'] = 'L\'item <b>:title</b> a été modifié';
$lang['modcat.message.success.delete'] = 'L\'item <b>:title</b> a été supprimé';
?>
