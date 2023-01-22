<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 01 09
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class GuideHomeController extends DefaultModuleController
{
	private $comments_config;
	private $content_management_config;

	private $category;

	protected function get_template_to_use()
	{
		return new FileTemplate('guide/GuideHomeController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();

		$this->check_authorizations();

		$this->build_root_view($request);
		$this->build_view($request);

		return $this->generate_response($request);
	}

	private function init()
	{
		$this->comments_config = CommentsConfig::load();
		$this->content_management_config = ContentManagementConfig::load();
	}

	private function build_root_view(HTTPRequestCustom $request)
	{
		$now = new Date();

        $condition = 'WHERE id_category = 0
            AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))';
        $parameters = array(
            'timestamp_now' => $now->get_timestamp()
        );
        $this->view->put_all(array(
            'C_ROOT_CONTROLS'           => GuideAuthorizationsService::check_authorizations($this->get_category()->get_id())->moderation(),
            'C_SEVERAL_ROOT_ITEMS' => GuideService::count($condition, $parameters) > 1,
            'U_REORDER_ROOT_ITEMS'    => GuideUrlBuilder::reorder_items(0, 'root')->rel(),
        ));

        $result = PersistenceContext::get_querier()->select('SELECT i.*, c.*, member.*, f.id AS fav_id, com.comments_number, notes.average_notes, notes.notes_number, note.note
        FROM ' . GuideSetup::$guide_items_table . ' i
        LEFT JOIN ' . GuideSetup::$guide_contents_table . ' c ON c.item_id = i.id
        LEFT JOIN ' . GuideSetup::$guide_favs_table . ' f ON f.item_id = i.id
        LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = c.author_user_id
        LEFT JOIN ' . DB_TABLE_COMMENTS_TOPIC . ' com ON com.id_in_module = i.id AND com.module_id = \'guide\'
        LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = i.id AND notes.module_name = \'guide\'
        LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = i.id AND note.module_name = \'guide\' AND note.user_id = :user_id
        ' . $condition . '
        ORDER BY i.i_order', array_merge($parameters, array(
            'user_id' => AppContext::get_current_user()->get_id()
        )));

        while ($row = $result->fetch()) {
            $item = new GuideItem();
            $item->set_properties($row);

            $this->view->assign_block_vars('root_items', $item->get_template_vars());
        }
        $result->dispose();
	}

	private function build_view(HTTPRequestCustom $request)
	{
		$now = new Date();
		$categories = CategoriesService::get_categories_manager(self::$module_id)->get_categories_cache()->get_categories();
		$authorized_categories = CategoriesService::get_authorized_categories(Category::ROOT_CATEGORY, true, self::$module_id);

		foreach ($categories as $id => $category)
		{
			if ($id != Category::ROOT_CATEGORY && in_array($id, $authorized_categories))
			{
				$category_elements_number = isset($categories_elements_number[$id]) ? $categories_elements_number[$id] : $category->get_elements_number();
				$this->view->assign_block_vars('categories', array(
                    'C_CONTROLS'           => GuideAuthorizationsService::check_authorizations($this->get_category()->get_id())->moderation(),
					'C_ITEMS'            => $category_elements_number > 0,
					'C_SEVERAL_ITEMS'    => $category_elements_number > 1,
					'ITEMS_NUMBER'       => $category->get_elements_number(),
					'CATEGORY_ID'        => $category->get_id(),
					'CATEGORY_SUB_ORDER' => $category->get_order(),
					'CATEGORY_PARENT_ID' => $category->get_id_parent(),
					'CATEGORY_NAME'      => $category->get_name(),
					'U_CATEGORY'         => GuideUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name(), self::$module_id)->rel(),
					'U_REORDER_ITEMS'    => GuideUrlBuilder::reorder_items($category->get_id(), $category->get_rewrited_name())->rel()
				));

				$result = PersistenceContext::get_querier()->select('SELECT i.*, c.*, member.*, f.id AS fav_id, com.comments_number, notes.average_notes, notes.notes_number, note.note
				FROM ' . GuideSetup::$guide_items_table . ' i
				LEFT JOIN ' . GuideSetup::$guide_contents_table . ' c ON c.item_id = i.id
				LEFT JOIN ' . GuideSetup::$guide_favs_table . ' f ON f.item_id = i.id
				LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = c.author_user_id
				LEFT JOIN ' . DB_TABLE_COMMENTS_TOPIC . ' com ON com.id_in_module = i.id AND com.module_id = \'guide\'
				LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = i.id AND notes.module_name = \'guide\'
				LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = i.id AND note.module_name = \'guide\' AND note.user_id = :user_id
				WHERE id_category = :id_category
                AND c.active_content = 1
                AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))
                ORDER BY i.i_order', array(
					'user_id' => AppContext::get_current_user()->get_id(),
                    'id_category' => $category->get_id(),
                    'timestamp_now' => $now->get_timestamp()
				));

				while ($row = $result->fetch()) {
					$item = new GuideItem();
					$item->set_properties($row);

					$this->view->assign_block_vars('categories.items', $item->get_template_vars());
				}
				$result->dispose();
			}
		}
	}

	private function get_pagination($condition, $parameters, $page, $subcategories_page)
	{
		$items_number = GuideService::count($condition, $parameters);

		$pagination = new ModulePagination($page, $items_number, (int)GuideConfig::load()->get_items_per_page());
		$pagination->set_url(GuideUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name(), '%d', $subcategories_page));

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
		$pagination->set_url(GuideUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name(), $page, '%d'));

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
					$this->category = CategoriesService::get_categories_manager('guide')->get_categories_cache()->get_category($id);
				} catch (CategoryNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->category = CategoriesService::get_categories_manager('guide')->get_categories_cache()->get_category(Category::ROOT_CATEGORY);
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
				'URL'  => GuideUrlBuilder::display_tag($keyword->get_rewrited_name())->rel(),
			));
			$i++;
		}
	}

	private function check_authorizations()
	{
		if (AppContext::get_current_user()->is_guest())
		{
			if (($this->config->is_summary_displayed_to_guests() && (!Authorizations::check_auth(RANK_TYPE, User::MEMBER_LEVEL, $this->get_category()->get_authorizations(), Category::READ_AUTHORIZATIONS) || $this->config->get_display_type() == GuideConfig::LIST_VIEW)) || (!$this->config->is_summary_displayed_to_guests() && !GuideAuthorizationsService::check_authorizations($this->get_category()->get_id())->read()))
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!GuideAuthorizationsService::check_authorizations($this->get_category()->get_id())->read())
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
			$graphical_environment->set_page_title($this->get_category()->get_name(), $this->lang['guide.module.title'], $page);
		else
			$graphical_environment->set_page_title($this->lang['guide.module.title'], '', $page);

		$description = $this->get_category()->get_description();
		if (empty($description))
			$description = StringVars::replace_vars($this->lang['guide.seo.description.root'], array('site' => GeneralConfig::load()->get_site_name())) . ($this->get_category()->get_id() != Category::ROOT_CATEGORY ? ' ' . $this->lang['category.category'] . ' ' . $this->get_category()->get_name() : '');
		$graphical_environment->get_seo_meta_data()->set_description($description, $page);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(GuideUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name(), $page));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['guide.module.title'], GuideUrlBuilder::home());

		$categories = array_reverse(CategoriesService::get_categories_manager('guide')->get_parents($this->get_category()->get_id(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), GuideUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name(), ($category->get_id() == $this->get_category()->get_id() ? $page : 1)));
		}

		return $response;
	}

	public static function get_view()
	{
		$object = new self();
		$object->init();
		$object->check_authorizations();
		$object->build_view(AppContext::get_request());
		return $object->view;
	}
}
?>
