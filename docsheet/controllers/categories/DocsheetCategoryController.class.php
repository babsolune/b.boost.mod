<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 03 27
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class DocsheetCategoryController extends DefaultModuleController
{
	private $comments_config;
	private $content_management_config;

	private $category;

	protected function get_template_to_use()
	{
		return new FileTemplate('docsheet/DocsheetSeveralItemsController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();

		$this->check_authorizations();

		$this->build_view($request);

		return $this->generate_response($request);
	}

	private function init()
	{
		$this->comments_config = CommentsConfig::load();
		$this->content_management_config = ContentManagementConfig::load();
	}

	private function build_view(HTTPRequestCustom $request)
	{
		$now = new Date();
		$page = $request->get_getint('page', 1);
		$subcategories_page = $request->get_getint('subcategories_page', 1);

		$subcategories = CategoriesService::get_categories_manager('docsheet')->get_categories_cache()->get_children($this->get_category()->get_id(), CategoriesService::get_authorized_categories($this->get_category()->get_id(), $this->config->is_summary_displayed_to_guests(), 'docsheet'));
		$subcategories_pagination = $this->get_subcategories_pagination(count($subcategories), $this->config->get_categories_per_page(), $page, $subcategories_page);

		$categories_number_displayed = 0;
		foreach ($subcategories as $id => $category)
		{
			$categories_number_displayed++;

			if ($categories_number_displayed > $subcategories_pagination->get_display_from() && $categories_number_displayed <= ($subcategories_pagination->get_display_from() + $subcategories_pagination->get_number_items_per_page()))
			{
				$category_thumbnail = $category->get_thumbnail()->rel();
				$category_description = FormatingHelper::second_parse($category->get_description());

				$this->view->assign_block_vars('sub_categories_list', array(
					'C_CATEGORY_THUMBNAIL' 	 => !empty($category_thumbnail),
					'C_SEVERAL_ITEMS'      	 => $category->get_elements_number() > 1,
					'C_CATEGORY_DESCRIPTION' => !empty($category_description),

					'CATEGORY_ID'          => $category->get_id(),
					'CATEGORY_NAME'        => $category->get_name(),
					'CATEGORY_DESCRIPTION' => $category_description,
					'U_CATEGORY_THUMBNAIL' => $category_thumbnail,
					'ITEMS_NUMBER'         => $category->get_elements_number(),
					'U_CATEGORY'           => DocsheetUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel()
				));
			}
		}

		$condition = 'WHERE id_category = :id_category';
		$parameters = array(
			'id_category' => $this->get_category()->get_id(),
			'timestamp_now' => $now->get_timestamp()
		);

		$pagination = $this->get_pagination($condition, $parameters, $page, $subcategories_page);

		$result = PersistenceContext::get_querier()->select('SELECT i.*, c.*, member.*, com.comments_number, notes.average_notes, notes.notes_number, note.note
		FROM ' . DocsheetSetup::$docsheet_articles_table . ' i
		LEFT JOIN ' . DocsheetSetup::$docsheet_contents_table . ' c ON c.item_id = i.id
		LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = c.author_user_id
		LEFT JOIN ' . DB_TABLE_COMMENTS_TOPIC . ' com ON com.id_in_module = i.id AND com.module_id = \'docsheet\'
		LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = i.id AND notes.module_name = \'docsheet\'
		LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = i.id AND note.module_name = \'docsheet\' AND note.user_id = :user_id
		' . $condition . '
		AND c.active_content = 1
		AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))
		ORDER BY i.i_order
		LIMIT :number_items_per_page OFFSET :display_from', array_merge($parameters, array(
			'user_id' => AppContext::get_current_user()->get_id(),
			'number_items_per_page' => $pagination->get_number_items_per_page(),
			'display_from' => $pagination->get_display_from()
		)));

		$category_description = FormatingHelper::second_parse($this->get_category()->get_description());

		$this->view->put_all(array(
            'MODULE_NAME' => $this->config->get_module_name(),

			'C_ITEMS'                    => $result->get_rows_count() > 0,
			'C_SEVERAL_ITEMS'            => $result->get_rows_count() > 1,
			'C_GRID_VIEW'                => $this->config->get_display_type() == DocsheetConfig::GRID_VIEW,
			'C_LIST_VIEW'                => $this->config->get_display_type() == DocsheetConfig::LIST_VIEW,
			'C_TABLE_VIEW'               => $this->config->get_display_type() == DocsheetConfig::TABLE_VIEW,
			'C_CATEGORY_DESCRIPTION'     => !empty($category_description),
			'C_AUTHOR_DISPLAYED'         => $this->config->is_author_displayed(),
			'C_ENABLED_COMMENTS'         => $this->comments_config->module_comments_is_enabled('docsheet'),
			'C_ENABLED_NOTATION'         => $this->content_management_config->module_notation_is_enabled('docsheet'),
			'C_ENABLED_VIEWS_NUMBER'     => $this->config->get_enabled_views_number(),
			'C_CONTROLS'                 => DocsheetAuthorizationsService::check_authorizations($this->get_category()->get_id())->write(),
			'C_PAGINATION'               => $pagination->has_several_pages(),
			'C_CATEGORY'                 => true,
			'C_ROOT_CATEGORY'            => $this->get_category()->get_id() == Category::ROOT_CATEGORY,
			'C_HIDE_NO_ITEM_MESSAGE'     => $this->get_category()->get_id() == Category::ROOT_CATEGORY && ($categories_number_displayed != 0 || !empty($category_description)),
			'C_SUB_CATEGORIES'           => $categories_number_displayed > 0,
			'C_SUBCATEGORIES_PAGINATION' => $subcategories_pagination->has_several_pages(),
			'C_DISPLAY_REORDER_LINK'     => $result->get_rows_count() > 1 && DocsheetAuthorizationsService::check_authorizations($this->get_category()->get_id())->moderation(),

			'CATEGORIES_PER_ROW'       => $this->config->get_categories_per_row(),
			'ITEMS_PER_ROW'            => $this->config->get_items_per_row(),
			'SUBCATEGORIES_PAGINATION' => $subcategories_pagination->display(),
			'PAGINATION'               => $pagination->display(),
			'TABLE_COLSPAN'            => 4 + (int)$this->comments_config->module_comments_is_enabled('docsheet') + (int)$this->content_management_config->module_notation_is_enabled('docsheet'),
			'ID_CAT'                   => $this->get_category()->get_id(),
			'CATEGORY_NAME'            => $this->get_category()->get_name(),
			'CATEGORY_DESCRIPTION'     => $category_description,

			'U_CATEGORY_THUMBNAIL' => $this->get_category()->get_thumbnail()->rel(),
			'U_EDIT_CATEGORY'      => $this->get_category()->get_id() == Category::ROOT_CATEGORY ? DocsheetUrlBuilder::configuration()->rel() : CategoriesUrlBuilder::edit($this->get_category()->get_id(), 'docsheet')->rel(),
			'U_REORDER_ITEMS'      => DocsheetUrlBuilder::reorder_items($this->get_category()->get_id(), $this->get_category()->get_rewrited_name())->rel(),
		));

		while ($row = $result->fetch())
		{
			$item = new DocsheetItem();
			$item->set_properties($row);

			$keywords = $item->get_keywords();
			$has_keywords = count($keywords) > 0;

			$this->view->assign_block_vars('items', array_merge($item->get_template_vars(), array(
				'C_KEYWORDS' => $has_keywords
			)));

			if ($has_keywords)
				$this->build_keywords_view($keywords);

			foreach ($item->get_item_content()->get_sources() as $name => $url)
			{
				$this->view->assign_block_vars('items.sources', $item->get_array_tpl_source_vars($name));
			}
		}
		$result->dispose();
	}

	private function get_pagination($condition, $parameters, $page, $subcategories_page)
	{
		$items_number = DocsheetService::count($condition, $parameters);

		$pagination = new ModulePagination($page, $items_number, (int)DocsheetConfig::load()->get_items_per_page());
		$pagination->set_url(DocsheetUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name(), '%d', $subcategories_page));

		if ($pagination->current_page_is_empty() && $page > 1)
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}

		return $pagination;
	}

	private function get_subcategories_pagination($subcategories_number, $categories_per_page, $page, $subcategories_page)
	{
		$pagination = new ModulePagination($subcategories_page, $subcategories_number, (int)$categories_per_page);
		$pagination->set_url(DocsheetUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name(), $page, '%d'));

		if ($pagination->current_page_is_empty() && $subcategories_page > 1)
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}

		return $pagination;
	}

	private function get_category()
	{
		if ($this->category === null)
		{
			$id = AppContext::get_request()->get_getint('id_category', 0);
			if (!empty($id))
			{
				try {
					$this->category = CategoriesService::get_categories_manager('docsheet')->get_categories_cache()->get_category($id);
				} catch (CategoryNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->category = CategoriesService::get_categories_manager('docsheet')->get_categories_cache()->get_category(Category::ROOT_CATEGORY);
			}
		}
		return $this->category;
	}

	private function build_keywords_view($keywords)
	{
		$nbr_keywords = count($keywords);

		$i = 1;
		foreach ($keywords as $keyword)
		{
			$this->view->assign_block_vars('items.keywords', array(
				'C_SEPARATOR' => $i < $nbr_keywords,
				'NAME' => $keyword->get_name(),
				'URL'  => DocsheetUrlBuilder::display_tag($keyword->get_rewrited_name())->rel(),
			));
			$i++;
		}
	}

	private function check_authorizations()
	{
		if (AppContext::get_current_user()->is_guest())
		{
			if (($this->config->is_summary_displayed_to_guests() && (!Authorizations::check_auth(RANK_TYPE, User::MEMBER_LEVEL, $this->get_category()->get_authorizations(), Category::READ_AUTHORIZATIONS) || $this->config->get_display_type() == DocsheetConfig::LIST_VIEW)) || (!$this->config->is_summary_displayed_to_guests() && !DocsheetAuthorizationsService::check_authorizations($this->get_category()->get_id())->read()))
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!DocsheetAuthorizationsService::check_authorizations($this->get_category()->get_id())->read())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
	}

	private function generate_response(HTTPRequestCustom $request)
	{
		$page = $request->get_getint('page', 1);
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();

		if ($this->get_category()->get_id() != Category::ROOT_CATEGORY)
			$graphical_environment->set_page_title($this->get_category()->get_name(), $this->config->get_module_name(), $page);
		else
			$graphical_environment->set_page_title($this->config->get_module_name(), '', $page);

		$description = $this->get_category()->get_description();
		if (empty($description))
			$description = StringVars::replace_vars($this->lang['docsheet.seo.description.root'], array('site' => GeneralConfig::load()->get_site_name())) . ($this->get_category()->get_id() != Category::ROOT_CATEGORY ? ' ' . $this->lang['category.category'] . ' ' . $this->get_category()->get_name() : '');
		$graphical_environment->get_seo_meta_data()->set_description($description, $page);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(DocsheetUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name(), $page));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->config->get_module_name(), DocsheetUrlBuilder::home());

		$categories = array_reverse(CategoriesService::get_categories_manager('docsheet')->get_parents($this->get_category()->get_id(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), DocsheetUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name(), ($category->get_id() == $this->get_category()->get_id() ? $page : 1)));
		}

		return $response;
	}

	public static function get_view()
	{
		$object = new self('docsheet');
		$object->init();
		$object->check_authorizations();
		$object->build_view(AppContext::get_request());
		return $object->view;
	}
}
?>
