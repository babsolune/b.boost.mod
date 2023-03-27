<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class GuideAuthorizationsService extends CategoriesAuthorizationsService
{
	const MANAGE_ARCHIVES_AUTHORIZATIONS = 32;

	public function manage_archives()
	{
		return $this->is_authorized(self::MANAGE_ARCHIVES_AUTHORIZATIONS);
	}

	protected function is_authorized($bit, $mode = Authorizations::AUTH_CHILD_PRIORITY)
	{
		$auth = CategoriesService::get_categories_manager('guide')->get_heritated_authorizations($this->id_category, $bit, $mode);

		return AppContext::get_current_user()->check_auth($auth, $bit);
	}
}
?>
