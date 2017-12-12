<?php
/*##################################################
 *                      ModcatDisplayCategoryController.class.php
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

class ModcatDisplayCategoryController extends ModuleController
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

		$this->build_view();

		return $this->generate_response();
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'modcat');
		$this->view = new FileTemplate('modcat/ModcatDisplayCategoryController.tpl');
		$this->view->add_lang($this->lang);
		$this->config = ModcatConfig::load();
		$this->comments_config = new ModcatComments();
		$this->notation_config = new ModcatNotation();
	}

	private function build_view()
	{
		$now = new Date();
		$request = AppContext::get_request();
		$mode = $request->get_getstring('sort', ModcatUrlBuilder::DEFAULT_SORT_MODE);
		$field = $request->get_getstring('field', ModcatUrlBuilder::DEFAULT_SORT_FIELD);
		$page = AppContext::get_request()->get_getint('page', 1);
		$subcategories_page = AppContext::get_request()->get_getint('subcategories_page', 1);

		$this->build_categories_listing_view($now, $field, $mode, $page, $subcategories_page);
		$this->build_items_listing_view($now, $field, $mode, $page, $subcategories_page);
		$this->build_sorting_form($field, $mode);
	}

	private function build_items_listing_view(Date $now, $field, $mode, $page, $subcategories_page)
	{
		$sort_mode = ($mode == 'asc') ? 'ASC' : 'DESC';
		switch ($field)
		{
			case 'title':
				$sort_field = 'title';
				break;
			case 'view':
				$sort_field = 'views_number';
				break;
			case 'com':
				$sort_field = 'number_comments';
				break;
			case 'note':
				$sort_field = 'average_notes';
				break;
			case 'author':
				$sort_field = 'display_name';
				break;
			default:
				$sort_field = 'creation_date';
				break;
		}

		$condition = 'WHERE category_id = :category_id
		AND (published = 1 OR (published = 2 AND publication_start_date < :timestamp_now AND (publication_end_date > :timestamp_now OR publication_end_date = 0)))';
		$parameters = array(
			'category_id' => $this->get_category()->get_id(),
			'timestamp_now' => $now->get_timestamp()
		);

		$pagination = $this->get_pagination($condition, $parameters, $field, $mode, $page, $subcategories_page);

		$result = PersistenceContext::get_querier()->select('SELECT modcat.*, member.*, com.number_comments, notes.average_notes, notes.number_notes, note.note
		FROM ' . ModcatSetup::$modcat_table . ' modcat
		LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = modcat.author_user_id
		LEFT JOIN ' . DB_TABLE_COMMENTS_TOPIC . ' com ON com.id_in_module = modcat.id AND com.module_id = \'modcat\'
		LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = modcat.id AND notes.module_name = \'modcat\'
		LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = modcat.id AND note.module_name = \'modcat\' AND note.user_id = :user_id
		' . $condition . '
		ORDER BY ' . $sort_field . ' ' . $sort_mode . '
		LIMIT :items_number_per_page OFFSET :display_from', array_merge($parameters, array(
			'user_id' => AppContext::get_current_user()->get_id(),
			'items_number_per_page' => $pagination->get_number_items_per_page(),
			'display_from' => $pagination->get_display_from()
		)));

		$columns_number_displayed_per_line = $this->config->get_cols_number_displayed_per_line();

		$this->view->put_all(array(
			'C_MOSAIC' => $this->config->get_display_type() == ModcatConfig::MOSAIC_DISPLAY,
			'C_LIST' => $this->config->get_display_type() == ModcatConfig::LIST_DISPLAY,
			'C_TABLE' => $this->config->get_display_type() == ModcatConfig::TABLE_DISPLAY,
			'C_COMMENTS_ENABLED' => $this->comments_config->are_comments_enabled(),
			'C_NOTATION_ENABLED' => $this->notation_config->is_notation_enabled(),
			'C_ITEMS_SORT_FILTERS' => $this->config->are_sort_filters_enabled(),
			'C_DISPLAY_CAT_ICONS' => $this->config->are_cat_icons_enabled(),
			'C_PAGINATION' => $pagination->has_several_pages(),
			'C_NO_ITEM_AVAILABLE' => $result->get_rows_count() == 0,
			'C_SEVERAL_COLUMNS' => $columns_number_displayed_per_line > 1,
			'C_MODERATION' => ModcatAuthorizationsService::check_authorizations($this->get_category()->get_id())->moderation(),
			'COLUMNS_NUMBER' => $columns_number_displayed_per_line,
			'C_ONE_ITEM_AVAILABLE' => $result->get_rows_count() == 1,
			'C_TWO_ITEMS_AVAILABLE' => $result->get_rows_count() == 2,
			'PAGINATION' => $pagination->display(),
			'CATEGORY_ID' => $this->get_category()->get_id(),
			'U_EDIT_CATEGORY' => $this->get_category()->get_id() == Category::ROOT_CATEGORY ? ModcatUrlBuilder::configuration()->rel() : ModcatUrlBuilder::edit_category($this->get_category()->get_id())->rel()
		));

		while($row = $result->fetch())
		{
			$itemcat = new Itemcat();
			$itemcat->set_properties($row);

			$this->build_keywords_view($itemcat);

			$this->view->assign_block_vars('items', $itemcat->get_array_tpl_vars());
			$this->build_sources_view($itemcat);
		}
		$result->dispose();
	}

	private function build_sources_view(Itemcat $itemcat)
	{
		$sources = $itemcat->get_sources();
		$nbr_sources = count($sources);
		if ($nbr_sources)
		{
			$this->view->put('items.C_SOURCES', $nbr_sources > 0);

			$i = 1;
			foreach ($sources as $name => $url)
			{
				$this->view->assign_block_vars('items.sources', array(
					'C_SEPARATOR' => $i < $nbr_sources,
					'NAME' => $name,
					'URL' => $url,
				));
				$i++;
			}
		}
	}

	private function build_categories_listing_view(Date $now, $field, $mode, $page, $subcategories_page)
	{
		$subcategories = ModcatService::get_categories_manager()->get_categories_cache()->get_children($this->get_category()->get_id(), ModcatService::get_authorized_categories($this->get_category()->get_id()));
		$subcategories_pagination = $this->get_subcategories_pagination(count($subcategories), $this->config->get_categories_number_per_page(), $field, $mode, $page, $subcategories_page);

		$cats_displayed_nbr = 0;
		foreach ($subcategories as $id => $category)
		{
			$cats_displayed_nbr++;

			if ($cats_displayed_nbr > $subcategories_pagination->get_display_from() && $cats_displayed_nbr <= ($subcategories_pagination->get_display_from() + $subcategories_pagination->get_number_items_per_page()))
			{
				$category_image = $category->get_image()->rel();

				$this->view->assign_block_vars('sub_categories', array(
					'C_CATEGORY_IMAGE' => !empty($category_image),
					'C_MORE_THAN_ONE_ITEM' => $category->get_elements_number() > 1,
					'CATEGORY_ID' => $category->get_id(),
					'CATEGORY_NAME' => $category->get_name(),
					'CATEGORY_IMAGE' => $category_image,
					'ITEMS_NUMBER' => $category->get_elements_number(),
					'U_CATEGORY' => ModcatUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel()
				));
			}
		}

		$cats_columns_nbr_per_line = ($cats_displayed_nbr > $this->config->get_cols_number_displayed_per_line()) ? $this->config->get_cols_number_displayed_per_line() : $cats_displayed_nbr;
		$cats_columns_nbr_per_line = !empty($cats_columns_nbr_per_line) ? $cats_columns_nbr_per_line : 1;

		$category_description = FormatingHelper::second_parse($this->get_category()->get_description());

		$this->view->put_all(array(
			'C_CATEGORY' => true,
			'C_ROOT_CATEGORY' => $this->get_category()->get_id() == Category::ROOT_CATEGORY,
			'C_HIDE_NO_ITEM_MESSAGE' => $this->get_category()->get_id() == Category::ROOT_CATEGORY && ($cats_displayed_nbr != 0 || !empty($category_description)),
			'C_CATEGORY_DESCRIPTION' => !empty($category_description),
			'C_SUB_CATEGORIES' => $cats_displayed_nbr > 0,
			'C_SUBCATEGORIES_PAGINATION' => $subcategories_pagination->has_several_pages(),
			'CATEGORY_NAME' => $this->get_category()->get_name(),
			'CATEGORY_IMAGE' => $this->get_category()->get_image()->rel(),
			'CATEGORY_DESCRIPTION' => $category_description,
			'SUBCATEGORIES_PAGINATION' => $subcategories_pagination->display(),
			'C_SEVERAL_CATS_COLUMNS' => $cats_columns_nbr_per_line > 1,
			'CATS_COLUMNS_NUMBER' => $cats_columns_nbr_per_line
		));
	}

	private function build_sorting_form($field, $mode)
	{
		$common_lang = LangLoader::get('common');

		$form = new HTMLForm(__CLASS__, '', false);
		$form->set_css_class('options');

		$fieldset = new FormFieldsetHorizontal('filters', array('description' => $common_lang['sort_by']));
		$form->add_fieldset($fieldset);

		$sort_options = array(
			new FormFieldSelectChoiceOption($common_lang['form.date.creation'], 'date'),
			new FormFieldSelectChoiceOption($common_lang['form.title'], 'title'),
			new FormFieldSelectChoiceOption($common_lang['sort_by.number_views'], 'view'),
			new FormFieldSelectChoiceOption($common_lang['author'], 'author')
		);

		if ($this->comments_config->are_comments_enabled())
			$sort_options[] = new FormFieldSelectChoiceOption($common_lang['sort_by.number_comments'], 'com');

		if ($this->notation_config->is_notation_enabled())
			$sort_options[] = new FormFieldSelectChoiceOption($common_lang['sort_by.best_note'], 'note');

		$fieldset->add_field(new FormFieldSimpleSelectChoice('sort_fields', '', $field, $sort_options,
			array('events' => array('change' => 'document.location = "'. ModcatUrlBuilder::display_category($this->category->get_id(), $this->category->get_rewrited_name())->rel() .'" + HTMLForms.getField("sort_fields").getValue() + "/" + HTMLForms.getField("sort_mode").getValue();'))
		));

		$fieldset->add_field(new FormFieldSimpleSelectChoice('sort_mode', '', $mode,
			array(
				new FormFieldSelectChoiceOption($common_lang['sort.asc'], 'asc'),
				new FormFieldSelectChoiceOption($common_lang['sort.desc'], 'desc')
			),
			array('events' => array('change' => 'document.location = "' . ModcatUrlBuilder::display_category($this->category->get_id(), $this->category->get_rewrited_name())->rel() . '" + HTMLForms.getField("sort_fields").getValue() + "/" + HTMLForms.getField("sort_mode").getValue();'))
		));

		$this->view->put('FORM', $form->display());
	}

	private function get_category()
	{
		if ($this->category === null)
		{
			$id = AppContext::get_request()->get_getstring('category_id', 0);
			if (!empty($id))
			{
				try {
					$this->category = ModcatService::get_categories_manager()->get_categories_cache()->get_category($id);
				} catch (CategoryNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->category = ModcatService::get_categories_manager()->get_categories_cache()->get_category(Category::ROOT_CATEGORY);
			}
		}
		return $this->category;
	}

	private function build_keywords_view(Itemcat $itemcat)
	{
		$keywords = $itemcat->get_keywords();
		$nbr_keywords = count($keywords);
		$this->view->put('C_KEYWORDS', $nbr_keywords > 0);

		$i = 1;
		foreach ($keywords as $keyword)
		{
			$this->view->assign_block_vars('keywords', array(
				'C_SEPARATOR' => $i < $nbr_keywords,
				'NAME' => $keyword->get_name(),
				'URL' => ModcatUrlBuilder::display_tag($keyword->get_rewrited_name())->rel(),
			));
			$i++;
		}
	}

	private function get_pagination($condition, $parameters, $field, $mode, $page, $subcategories_page)
	{
		$number_items = PersistenceContext::get_querier()->count(ModcatSetup::$modcat_table, $condition, $parameters);

		$pagination = new ModulePagination($page, $number_items, (int)ModcatConfig::load()->get_items_number_per_page());
		$pagination->set_url(ModcatUrlBuilder::display_category($this->category->get_id(), $this->category->get_rewrited_name(), $field, $mode, '%d', $subcategories_page));

		if ($pagination->current_page_is_empty() && $page > 1)
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}

		return $pagination;
	}

	private function get_subcategories_pagination($subcategories_number, $categories_number_per_page, $field, $mode, $page, $subcategories_page)
	{
		$pagination = new ModulePagination($subcategories_page, $subcategories_number, (int)$categories_number_per_page);
		$pagination->set_url(ModcatUrlBuilder::display_category($this->category->get_id(), $this->category->get_rewrited_name(), $field, $mode, $page, '%d'));

		if ($pagination->current_page_is_empty() && $subcategories_page > 1)
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
			if (($this->config->are_descriptions_displayed_to_guests() && !Authorizations::check_auth(RANK_TYPE, User::MEMBER_LEVEL, $this->get_category()->get_authorizations(), Category::READ_AUTHORIZATIONS)) || (!$this->config->are_descriptions_displayed_to_guests() && !ModcatAuthorizationsService::check_authorizations($this->get_category()->get_id())->read()))
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!ModcatAuthorizationsService::check_authorizations($this->get_category()->get_id())->read())
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
			$graphical_environment->set_page_title($this->category->get_name(), $this->lang['modcat.module.title']);
		else
			$graphical_environment->set_page_title($this->lang['modcat.module.title']);

		$graphical_environment->get_seo_meta_data()->set_description($this->category->get_description());
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ModcatUrlBuilder::display_category($this->category->get_id(), $this->category->get_rewrited_name(), AppContext::get_request()->get_getstring('field', 'date'), AppContext::get_request()->get_getstring('sort', 'desc'), AppContext::get_request()->get_getint('page', 1)));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['modcat.module.title'], ModcatUrlBuilder::home());

		$categories = array_reverse(ModcatService::get_categories_manager()->get_parents($this->category->get_id(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), ModcatUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name(), AppContext::get_request()->get_getstring('field', 'date'), AppContext::get_request()->get_getstring('sort', 'desc'), AppContext::get_request()->get_getint('page', 1)));
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
