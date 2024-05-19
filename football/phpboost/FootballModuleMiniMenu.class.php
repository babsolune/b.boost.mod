<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballModuleMiniMenu extends ModuleMiniMenu
{
	public function get_default_block()
	{
		return self::BLOCK_POSITION__RIGHT;
	}

	public function get_menu_id()
	{
		return 'module-mini-football';
	}

	public function get_menu_title()
	{
		return LangLoader::get_message('football.last.items', 'common', 'football');
	}

	public function get_formated_title()
	{
		return LangLoader::get_message('football.module.title', 'common', 'football');
	}

	public function is_displayed()
	{
		return FootballAuthorizationsService::check_authorizations()->read();
	}

	public function get_menu_content()
	{
		// Create file template
		$view = new FileTemplate('football/FootballModuleMiniMenu.tpl');

		// Assign the lang file to the tpl
		$view->add_lang(LangLoader::get_all_langs('football'));

		// Assign common menu variables to the tpl
		MenuService::assign_positions_conditions($view, $this->get_block());

		// Load module config
		$config = FootballConfig::load();

		// Load module cache
		// $football_cache = FootballCache::load();

		// Load categories cache
		$categories_cache = CategoriesService::get_categories_manager('football')->get_categories_cache();

		// $items = $football_cache->get_items();

		$view->put_all(array(
			// 'C_ITEMS'                    => !empty($items),
			// 'C_SORT_BY_DATE'             => $config->is_sort_type_date(),
			// 'C_SORT_BY_NOTATION'         => $config->is_sort_type_notation(),
			// 'C_SORT_BY_DOWNLOADS_NUMBER' => $config->is_sort_type_footballs_number(),
			// 'C_SORT_BY_VIEWS_NUMBERS'    => $config->is_sort_type_views_numbers()
		));

		$displayed_position = 1;
		// foreach ($items as $file)
		// {
		// 	$item = new FootballCompet();
		// 	$item->set_properties($file);

		// 	$view->assign_block_vars('items', array_merge($item->get_template_vars(), array(
		// 		'DISPLAYED_POSITION' => $displayed_position
		// 	)));

		// 	$displayed_position++;
		// }

		return $view->render();
	}
}
?>
