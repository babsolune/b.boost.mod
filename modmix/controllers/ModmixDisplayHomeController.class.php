<?php
/*##################################################
 *                               ModmixDisplayHomeController.class.php
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

class ModmixDisplayHomeController extends ModuleController
{
	private $lang;
	private $config;
	private $comments_config;
	private $notation_config;
	private $category;

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();

		$this->check_authorizations();

		$this->build_view($request);

		return $this->generate_response();
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'modmix');
		$this->view = new FileTemplate('modmix/ModmixDisplayHomeController.tpl');
		$this->view->add_lang($this->lang);
		$this->config = ModmixConfig::load();
		$this->comments_config = new ModmixComments();
		$this->notation_config = new ModmixNotation();

	}

	private function build_view(HTTPRequestCustom $request)
	{
		$now = new Date();

		// $home_categories = ModmixCategoriesCache::load()->get_categories();
		// var_dump($home_categories);

		$this->view->put_all(array(
			'U_CONFIG' => ModmixUrlBuilder::configuration()->rel(),
			'C_MOSAIC' => $this->config->get_display_type() == ModmixConfig::MOSAIC_DISPLAY,
			'C_LIST' => $this->config->get_display_type() == ModmixConfig::LIST_DISPLAY,
			'C_TABLE' => $this->config->get_display_type() == ModmixConfig::TABLE_DISPLAY,
			'COLUMNS_NUMBER' => $this->config->get_cols_number_displayed_per_line()
		));

		$result_cat = PersistenceContext::get_querier()->select('SELECT modmix_cat.*
		FROM '. ModmixSetup::$modmix_cats_table .' modmix_cat
		WHERE special_authorizations = 0
		ORDER BY id', array(
			// 'cat_order' => (int)modmix_cat.id_parent, (int)modmix_cat.c_order
		));

		while ($row_cat = $result_cat->fetch())
		{
			$this->view->assign_block_vars('categories', array(
				'ID' => $row_cat['id'],
				'ID_PARENT' => $row_cat['id_parent'],
				'SUB_ORDER' => $row_cat['c_order'],
				'C_MODERATION' => ModmixAuthorizationsService::check_authorizations($this->get_category()->get_id())->moderation(),
				'COLUMNS_NUMBER' => $this->config->get_cols_number_displayed_per_line(),
				'CATEGORY_ID' => $this->get_category()->get_id(),
				'U_EDIT_CATEGORY' => $this->get_category()->get_id() == Category::ROOT_CATEGORY ? ModmixUrlBuilder::configuration()->rel() : ModmixUrlBuilder::edit_category($this->get_category()->get_id())->rel(),
				'C_LEVEL_ONE_CATEGORY' => $row_cat['id_parent'] == 0,
				'CATEGORY_NAME' => $row_cat['name'],
				'U_CATEGORY' => ModmixUrlBuilder::display_category($row_cat['id'], $row_cat['rewrited_name'])->rel(),
				'C_NO_ITEM_AVAILABLE' => $result_cat->get_rows_count() == 0,
			));

			$category_id = $row_cat['id'];

			$result = PersistenceContext::get_querier()->select('SELECT modmix.*, member.*, com.number_comments, notes.average_notes, notes.number_notes, note.note
			FROM '. ModmixSetup::$modmix_table .' modmix
			LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = modmix.author_user_id
			LEFT JOIN ' . DB_TABLE_COMMENTS_TOPIC . ' com ON com.id_in_module = modmix.id AND com.module_id = \'modmix\'
			LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = modmix.id AND notes.module_name = \'modmix\'
			LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = modmix.id AND note.module_name = \'modmix\' AND note.user_id = :user_id
			WHERE modmix.category_id = :category_id
			AND (published = 1 OR (published = 2 AND publication_start_date < :timestamp_now AND (publication_end_date > :timestamp_now OR publication_end_date = 0)))
			ORDER BY creation_date DESC', array(
				'user_id' => AppContext::get_current_user()->get_id(),
				'category_id' => $category_id,
				'timestamp_now' => $now->get_timestamp()
			));

			while ($row = $result->fetch())
			{
				$itemcat = new Itemmix();
				$itemcat->set_properties($row);
				$this->view->assign_block_vars('categories.items', $itemcat->get_array_tpl_vars());
			}
			$result->dispose();
		}
		$result_cat->dispose();
	}

	private function get_category()
	{
		if ($this->category === null)
		{
			$id = AppContext::get_request()->get_getint('id_category', 0);
			if (!empty($id))
			{
				try {
					$this->category = ModmixService::get_categories_manager()->get_categories_cache()->get_category($id);
				} catch (CategoryNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
   					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->category = ModmixService::get_categories_manager()->get_categories_cache()->get_category(Category::ROOT_CATEGORY);
			}
		}
		return $this->category;
	}

	private function build_sources_view(Itemmix $itemmix)
	{
		$sources = $itemmix->get_sources();
		$nbr_sources = count($sources);
		if ($nbr_sources)
		{
			$this->view->put('categories.items.C_SOURCES', $nbr_sources > 0);

			$i = 1;
			foreach ($sources as $name => $url)
			{
				$this->view->assign_block_vars('categories.items.sources', array(
					'C_SEPARATOR' => $i < $nbr_sources,
					'NAME' => $name,
					'URL' => $url,
				));
				$i++;
			}
		}
	}

	private function build_keywords_view(Itemmix $itemmix)
	{
		$keywords = $itemmix->get_keywords();
		$nbr_keywords = count($keywords);
		$this->view->put('C_KEYWORDS', $nbr_keywords > 0);

		$i = 1;
		foreach ($keywords as $keyword)
		{
			$this->view->assign_block_vars('keywords', array(
				'C_SEPARATOR' => $i < $nbr_keywords,
				'NAME' => $keyword->get_name(),
				'URL' => ModmixUrlBuilder::display_tag($keyword->get_rewrited_name())->rel(),
			));
			$i++;
		}
	}

	private function get_pagination($condition, $parameters, $field, $mode, $page, $subcategories_page)
	{
		$number_items = PersistenceContext::get_querier()->count(ModmixSetup::$modmix_table, $condition, $parameters);

		$pagination = new ModulePagination($page, $number_items, (int)ModmixConfig::load()->get_items_number_per_page());
		$pagination->set_url(ModmixUrlBuilder::display_category($this->category->get_id(), $this->category->get_rewrited_name(), $field, $mode, '%d', $subcategories_page));

		if ($pagination->current_page_is_empty() && $page > 1)
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}

		return $pagination;
	}

	private function check_authorizations()
	{
		if (AppContext::get_current_user()->is_guest())
		{
			if (($this->config->are_descriptions_displayed_to_guests() && !Authorizations::check_auth(RANK_TYPE, User::MEMBER_LEVEL, $this->get_category()->get_authorizations(), Category::READ_AUTHORIZATIONS)) || (!$this->config->are_descriptions_displayed_to_guests() && !ModmixAuthorizationsService::check_authorizations($this->get_category()->get_id())->read()))
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!ModmixAuthorizationsService::check_authorizations($this->get_category()->get_id())->read())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
	}

	private function generate_response()
	{
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();

		if ($this->category->get_id() != Category::ROOT_CATEGORY)
			$graphical_environment->set_page_title($this->category->get_name(), $this->lang['modmix.module.title']);
		else
			$graphical_environment->set_page_title($this->lang['modmix.module.title']);

		$graphical_environment->get_seo_meta_data()->set_description($this->category->get_description());
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ModmixUrlBuilder::display_category($this->category->get_id(), $this->category->get_rewrited_name(), AppContext::get_request()->get_getstring('field', 'date'), AppContext::get_request()->get_getstring('sort', 'desc'), AppContext::get_request()->get_getint('page', 1)));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['modmix.module.title'], ModmixUrlBuilder::home());

		$categories = array_reverse(ModmixService::get_categories_manager()->get_parents($this->category->get_id(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), ModmixUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name(), AppContext::get_request()->get_getstring('field', 'date'), AppContext::get_request()->get_getstring('sort', 'desc'), AppContext::get_request()->get_getint('page', 1)));
		}

		return $response;
	}

	public static function get_view()
	{
		$object = new self();
		$object->init();
		$object->check_authorizations();
		$object->build_view();
		return $object->view;
	}
}
?>
