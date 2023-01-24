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
	private $category;

	protected function get_template_to_use()
	{
		return new FileTemplate('guide/GuideHomeController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_view();

		return $this->generate_response($request);
	}

	private function build_view()
	{
		$now = new Date();
		$categories = CategoriesService::get_categories_manager(self::$module_id)->get_categories_cache()->get_categories();
		$authorized_categories = CategoriesService::get_authorized_categories(Category::ROOT_CATEGORY, true, self::$module_id);

		foreach ($categories as $id => $category)
		{
			if ($id == Category::ROOT_CATEGORY)
			{
				$condition = 'WHERE id_category = :id_category
					AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))';
				$parameters = array(
					'timestamp_now' => $now->get_timestamp(),
					'id_category' => $category->get_id()
				);

				$root_description = FormatingHelper::second_parse($this->config->get_root_category_description());
				$this->view->put_all(array(
					'C_ROOT_CONTROLS'             => GuideAuthorizationsService::check_authorizations($id)->moderation(),
					'C_SEVERAL_ROOT_ITEMS'        => GuideService::count($condition, $parameters) > 1,
					'C_ROOT_CATEGORY_DESCRIPTION' => !empty($root_description),

					'ROOT_CATEGORY_DESCRIPTION' => $root_description,

					'U_REORDER_ROOT_ITEMS' => GuideUrlBuilder::reorder_items(0, 'root')->rel(),
				));

				$result = PersistenceContext::get_querier()->select('SELECT i.*, c.*, member.*, f.id AS fav_id, com.comments_number, notes.average_notes, notes.notes_number, note.note
				FROM ' . GuideSetup::$guide_table . ' i
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

			if ($id != Category::ROOT_CATEGORY && in_array($id, $authorized_categories))
			{
				$category_elements_number = isset($categories_elements_number[$id]) ? $categories_elements_number[$id] : $category->get_elements_number();
                $this->view->assign_block_vars('categories', array(
                    'C_CONTROLS'         => GuideAuthorizationsService::check_authorizations($this->get_category()->get_id())->moderation(),
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
				FROM ' . GuideSetup::$guide_table . ' i
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

	private function check_authorizations()
	{
        if (!GuideAuthorizationsService::check_authorizations($this->get_category()->get_id())->read())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
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
		$object = new self('guide');
		$object->check_authorizations();
		$object->build_view(AppContext::get_request());
		return $object->view;
	}
}
?>
