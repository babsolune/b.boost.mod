<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 01 21
 * @since       PHPBoost 6.0 - 2023 01 21
*/

class WikiModuleMiniMenu extends ModuleMiniMenu
{
	public function get_default_block()
	{
		return self::BLOCK_POSITION__RIGHT;
	}

	public function admin_display()
	{
		return '';
	}

	public function get_menu_id()
	{
		return 'module-mini-wiki';
	}

	public function get_menu_title()
	{
		return WikiConfig::load()->get_menu_name();
	}

	public function is_displayed()
	{
		return ModulesManager::is_module_installed('wiki') && ModulesManager::is_module_activated('wiki');
	}

	public function get_menu_content()
	{
		$view = new FileTemplate('wiki/WikiModuleMiniMenu.tpl');
		$view->add_lang(LangLoader::get_all_langs('wiki'));
		MenuService::assign_positions_conditions($view, $this->get_block());
		Menu::assign_common_template_variables($view);
		$now = new Date();

		$categories = CategoriesService::get_categories_manager('wiki')->get_categories_cache()->get_categories();
		$authorized_categories = CategoriesService::get_authorized_categories(Category::ROOT_CATEGORY, true, 'wiki');

		foreach ($categories as $id => $category)
		{
			if ($id == Category::ROOT_CATEGORY)
			{
				$view->put_all(array(
					'C_ROOT_ITEMS' => $category->get_elements_number() > 0,
				));

				$result = PersistenceContext::get_querier()->select('SELECT i.*, c.*, member.*, com.comments_number, notes.average_notes, notes.notes_number, note.note
				FROM ' . WikiSetup::$wiki_articles_table . ' i
				LEFT JOIN ' . WikiSetup::$wiki_contents_table . ' c ON c.item_id = i.id
				LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = c.author_user_id
				LEFT JOIN ' . DB_TABLE_COMMENTS_TOPIC . ' com ON com.id_in_module = i.id AND com.module_id = \'wiki\'
				LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = i.id AND notes.module_name = \'wiki\'
				LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = i.id AND note.module_name = \'wiki\' AND note.user_id = :user_id
				WHERE id_category = 0
				AND c.active_content = 1
				AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))
				ORDER BY i.i_order', array(
					'user_id' => AppContext::get_current_user()->get_id(),
					'timestamp_now' => $now->get_timestamp()
				));

				while ($row = $result->fetch()) {
					$item = new WikiItem();
					$item->set_properties($row);

					$view->assign_block_vars('root_items', $item->get_template_vars());
				}
				$result->dispose();
			}

			if ($id != Category::ROOT_CATEGORY && in_array($id, $authorized_categories))
			{
				$view->assign_block_vars('categories', array(
					'C_ITEMS'		  => $category->get_elements_number() > 0,
					'C_SEVERAL_ITEMS' => $category->get_elements_number() > 1,

                    'CATEGORY_ID'        => $category->get_id(),
					'CATEGORY_SUB_ORDER' => $category->get_order(),
					'CATEGORY_PARENT_ID' => $category->get_id_parent(),
					'CATEGORY_NAME'      => $category->get_name(),
					'U_CATEGORY'         => WikiUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel()
				));

				$result = PersistenceContext::get_querier()->select('SELECT i.*, c.*, member.*, com.comments_number, notes.average_notes, notes.notes_number, note.note
				FROM ' . WikiSetup::$wiki_articles_table . ' i
				LEFT JOIN ' . WikiSetup::$wiki_contents_table . ' c ON c.item_id = i.id
				LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = c.author_user_id
				LEFT JOIN ' . DB_TABLE_COMMENTS_TOPIC . ' com ON com.id_in_module = i.id AND com.module_id = \'wiki\'
				LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = i.id AND notes.module_name = \'wiki\'
				LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = i.id AND note.module_name = \'wiki\' AND note.user_id = :user_id
				WHERE id_category = :id_category
                AND c.active_content = 1
                AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))
                ORDER BY i.i_order', array(
					'user_id' => AppContext::get_current_user()->get_id(),
                    'id_category' => $category->get_id(),
                    'timestamp_now' => $now->get_timestamp()
				));

				while ($row = $result->fetch()) {
					$item = new WikiItem();
					$item->set_properties($row);

					$view->assign_block_vars('categories.items', $item->get_template_vars());
				}
				$result->dispose();
			}
		}
        return $view->render();
	}
}
?>
