<?php
/*##################################################
 *                        ModcatfullExtensionPointProvider.class.php
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

class ModcatfullExtensionPointProvider extends ExtensionPointProvider
{
	public function __construct()
	{
		parent::__construct('modcatfull');
	}

	public function comments()
	{
		return new CommentsTopics(array(new ModcatfullCommentsTopic()));
	}

	public function css_files()
	{
		$module_css_files = new ModuleCssFiles();
		$module_css_files->adding_running_module_displayed_file('modcatfull.css');
		return $module_css_files;
	}

	public function feeds()
	{
		return new ModcatfullFeedProvider();
	}

	public function home_page()
	{
		return new ModcatfullHomePageExtensionPoint();
	}

	public function newcontent()
	{
		return new ModcatfullNewContent();
	}

	public function notation()
	{
		return new ModcatfullNotation();
	}

	public function scheduled_jobs()
	{
		return new ModcatfullScheduledJobs();
	}

	public function search()
	{
		return new ModcatfullSearchable();
	}

	public function sitemap()
	{
		return new ModcatfullSitemapExtensionPoint();
	}

	public function tree_links()
	{
		return new ModcatfullTreeLinks();
	}

	public function url_mappings()
	{
		return new UrlMappings(array(new DispatcherUrlMapping('/modcatfull/index.php')));
	}
}
?>
