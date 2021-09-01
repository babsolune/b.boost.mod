<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 04 11
 * @since       PHPBoost 5.0 - 2017 06 21
*/

class ClubsExtensionPointProvider extends ItemsModuleExtensionPointProvider
{
	public function home_page()
	{
		return new DefaultHomePageDisplay($this->get_id(), ClubsCategoryController::get_view());
	}
}
?>
