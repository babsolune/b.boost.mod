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
$lang['modmix.module.title'] = 'Modmix';
$lang['modmix.item'] = 'Item';
$lang['modmix.items'] = 'Items';
$lang['module.config.title'] = 'Configuration des items';
$lang['modmix.management'] = 'Gestion des items';
$lang['modmix.add'] = 'Ajouter un item';
$lang['modmix.edit'] = 'Modification d\'un item';
$lang['modmix.feed.name'] = 'Derniers items';
$lang['modmix.pending.items'] = 'Items en attente';
$lang['modmix.published.items'] = 'Items publiés';
$lang['modmix.print.item'] = 'Impression d\'un item';

//Modmix configuration
$lang['modmix.items.number.per.category'] = 'Nombre d\'items par catégorie sur la page d\'accueil';
$lang['modmix.configuration.cats.icon.display'] = 'Afficher l\'icône des catégories';
$lang['modmix.configuration.sort.filter.display'] = 'Afficher les filtres de tri';
$lang['modmix.configuration.suggestions.display'] = 'Afficher les suggestions d\'items';
$lang['modmix.configuration.suggestions.nb'] = 'Nombre d\'items suggérés à afficher';
$lang['modcat.configuration.navigation.links.display'] = 'Afficher la navigation des items connexes';
$lang['modcat.configuration.navigation.links.display.desc'] = 'Lien précédent, lien suivant';
$lang['modmix.configuration.characters.number.to.cut'] = 'Nombre de caractères pour couper le condensé de l\'item';
$lang['modmix.configuration.display.type'] = 'Type d\'affichage des items';
$lang['modmix.configuration.mosaic.type.display'] = 'Mosaïque';
$lang['modmix.configuration.list.type.display'] = 'Liste';
$lang['modmix.configuration.table.type.display'] = 'Tableau';
$lang['modmix.configuration.display.descriptions.to.guests'] = 'Afficher le condensé des items aux visiteurs s\'ils n\'ont pas l\'autorisation de lecture';

//Form
$lang['modmix.form.description'] = 'Description (maximum :number caractères)';
$lang['modmix.form.enabled.description'] = 'Activer le condensé de l\'item';
$lang['modmix.form.enabled.description.description'] = 'ou laissez PHPBoost couper le contenu à :number caractères';
$lang['modmix.form.carousel'] = 'Ajouter un carousel d\'images';
$lang['modmix.form.image.description'] = 'Description';
$lang['modmix.form.image.url'] = 'Adresse image';
$lang['modmix.form.enabled.author.name.customisation'] = 'Personnaliser le nom de l\'auteur';
$lang['modmix.form.custom.author.name'] = 'Nom de l\'auteur personnalisé';

//Sort fields title and mode
$lang['modmix.sort.field.views'] = 'Vues';
$lang['admin.modmix.sort.field.published'] = 'Publié';

//SEO
$lang['modmix.seo.description.root'] = 'Tous les items du site :site.';
$lang['modmix.seo.description.tag'] = 'Tous les items sur le sujet :subject.';
$lang['modmix.seo.description.pending'] = 'Tous les items en attente.';

//Messages
$lang['modmix.message.success.add'] = 'L\'item <b>:title</b> a été ajouté';
$lang['modmix.message.success.edit'] = 'L\'item <b>:title</b> a été modifié';
$lang['modmix.message.success.delete'] = 'L\'item <b>:title</b> a été supprimé';
?>
