<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 04 08
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class DocsheetExplorerController extends DefaultModuleController
{
	private $category;

	protected function get_template_to_use()
	{
		return new FileTemplate('docsheet/DocsheetExplorerController.tpl');
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

        $this->view->put('MODULE_NAME', $this->config->get_module_name());

		foreach ($categories as $id => $category)
		{
			if ($id == Category::ROOT_CATEGORY)
			{
				$root_description = FormatingHelper::second_parse($this->config->get_root_category_description());
				$this->view->put_all(array(
					'C_ROOT_CONTROLS'               => DocsheetAuthorizationsService::check_authorizations($id)->moderation(),
                    'C_HOMEPAGE'                    => $this->config->get_homepage() == DocsheetConfig::EXPLORER,
					'C_ROOT_CATEGORY_DESCRIPTION'   => !empty($root_description),
					'C_ROOT_ITEMS'                  => $category->get_elements_number() > 0,
					'C_SEVERAL_ROOT_ITEMS'          => $category->get_elements_number() > 1,

					'ROOT_CATEGORY_DESCRIPTION' => $root_description,

					'U_REORDER_ROOT_ITEMS' => DocsheetUrlBuilder::reorder_items(0, 'root')->rel(),
				));

				$result = PersistenceContext::get_querier()->select('SELECT i.*, c.*, member.*, com.comments_number, notes.average_notes, notes.notes_number, note.note
				FROM ' . DocsheetSetup::$docsheet_articles_table . ' i
				LEFT JOIN ' . DocsheetSetup::$docsheet_contents_table . ' c ON c.item_id = i.id
				LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = c.author_user_id
				LEFT JOIN ' . DB_TABLE_COMMENTS_TOPIC . ' com ON com.id_in_module = i.id AND com.module_id = \'docsheet\'
				LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = i.id AND notes.module_name = \'docsheet\'
				LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = i.id AND note.module_name = \'docsheet\' AND note.user_id = :user_id
				WHERE i.id_category = :id_category
				AND c.active_content = 1
				AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))
                ORDER BY i.i_order', array(
					'user_id' => AppContext::get_current_user()->get_id(),
					'timestamp_now' => $now->get_timestamp(),
					'id_category' => $category->get_id()
				));

				while ($row = $result->fetch()) {
					$item = new DocsheetItem();
					$item->set_properties($row);

					$this->view->assign_block_vars('root_items', $item->get_template_vars());
				}
				$result->dispose();
			}

			if ($id != Category::ROOT_CATEGORY && in_array($id, $authorized_categories))
			{
				$category_elements_number = isset($categories_elements_number[$id]) ? $categories_elements_number[$id] : $category->get_elements_number();
                $this->view->assign_block_vars('categories', array(
                    'C_CONTROLS'            => DocsheetAuthorizationsService::check_authorizations()->moderation(),
					'C_ITEMS'               => $category_elements_number > 0,
					'C_SEVERAL_ITEMS'       => $category_elements_number > 1,
                    'C_CATEGORY_THUMBNAIL'  => !empty($category->get_thumbnail()),
					'ITEMS_NUMBER'          => $category->get_elements_number(),
					'CATEGORY_ID'           => $category->get_id(),
					'CATEGORY_SUB_ORDER'    => $category->get_order(),
					'CATEGORY_PARENT_ID'    => $category->get_id_parent(),
					'CATEGORY_NAME'         => $category->get_name(),

                    'U_CATEGORY_THUMBNAIL' => $category->get_thumbnail()->rel(),
					'U_CATEGORY'           => DocsheetUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name(), self::$module_id)->rel(),
					'U_REORDER_ITEMS'      => DocsheetUrlBuilder::reorder_items($category->get_id(), $category->get_rewrited_name())->rel()
				));

				$result = PersistenceContext::get_querier()->select('SELECT i.*, c.*, member.*, com.comments_number, notes.average_notes, notes.notes_number, note.note
				FROM ' . DocsheetSetup::$docsheet_articles_table . ' i
				LEFT JOIN ' . DocsheetSetup::$docsheet_contents_table . ' c ON c.item_id = i.id
				LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = c.author_user_id
				LEFT JOIN ' . DB_TABLE_COMMENTS_TOPIC . ' com ON com.id_in_module = i.id AND com.module_id = \'docsheet\'
				LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = i.id AND notes.module_name = \'docsheet\'
				LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = i.id AND note.module_name = \'docsheet\' AND note.user_id = :user_id
				WHERE id_category = :id_category
                AND c.active_content = 1
                AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))
                ORDER BY i.i_order', array(
					'user_id' => AppContext::get_current_user()->get_id(),
                    'id_category' => $category->get_id(),
                    'timestamp_now' => $now->get_timestamp()
				));

				while ($row = $result->fetch()) {
					$item = new DocsheetItem();
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

	private function check_authorizations()
	{
        if (!DocsheetAuthorizationsService::check_authorizations($this->get_category()->get_id())->read())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
        }
	}

	private function generate_response(HTTPRequestCustom $request)
	{
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
        $graphical_environment->set_page_title($this->lang['docsheet.explorer'], $this->config->get_module_name());
		$description = StringVars::replace_vars($this->lang['docsheet.seo.description.root'], array('site' => GeneralConfig::load()->get_site_name()));
		$graphical_environment->get_seo_meta_data()->set_description($description);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(DocsheetUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name()));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->config->get_module_name(), DocsheetUrlBuilder::home());
		$breadcrumb->add($this->lang['docsheet.explorer'], DocsheetUrlBuilder::explorer());

		return $response;
	}
}
?>
