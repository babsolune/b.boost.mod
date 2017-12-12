<?php
/*##################################################
 *                        ModlistExtensionPointProvider.class.php
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

class ModlistExtensionPointProvider extends ExtensionPointProvider
{
	public function __construct()
	{
		parent::__construct('modlist');
	}

	public function comments()
	{
		return new CommentsTopics(array(new ModlistCommentsTopic()));
	}

	public function css_files()
	{
		$module_css_files = new ModuleCssFiles();
		$module_css_files->adding_running_module_displayed_file('modlist.css');
		return $module_css_files;
	}

	public function feeds()
	{
		return new ModlistFeedProvider();
	}

	public function home_page()
	{
		return new ModlistHomePageExtensionPoint();
	}

	public function newcontent()
	{
		return new ModlistNewContent();
	}

	public function notation()
	{
		return new ModlistNotation();
	}

	public function scheduled_jobs()
	{
		return new ModlistScheduledJobs();
	}

	public function search()
	{
		return new ModlistSearchable();
	}

	public function sitemap()
	{
		return new ModlistSitemapExtensionPoint();
	}

	public function tree_links()
	{
		return new ModlistTreeLinks();
	}

	public function url_mappings()
	{
		return new UrlMappings(array(new DispatcherUrlMapping('/modlist/index.php')));
	}
}
?>
