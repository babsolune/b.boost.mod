<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballAuthorizationsService extends CategoriesAuthorizationsService
{
	const CLUBS_AUTHORIZATIONS = 32;
	const DIVISIONS_AUTHORIZATIONS = 64;
	const SEASONS_AUTHORIZATIONS = 128;
	const COMPETITION_AUTHORIZATIONS = 256;

	public function manage_divisions()
	{
		return $this->is_authorized(self::DIVISIONS_AUTHORIZATIONS);
	}

	public function manage_clubs()
	{
		return $this->is_authorized(self::CLUBS_AUTHORIZATIONS);
	}

	public function manage_seasons()
	{
		return $this->is_authorized(self::SEASONS_AUTHORIZATIONS);
	}

	public function manage_compets()
	{
		return $this->is_authorized(self::COMPETITION_AUTHORIZATIONS);
	}

	protected function is_authorized($bit, $mode = Authorizations::AUTH_CHILD_PRIORITY)
	{
		$auth = CategoriesService::get_categories_manager('football')->get_heritated_authorizations($this->id_category, $bit, $mode);
		return AppContext::get_current_user()->check_auth($auth, $bit);
	}
}
?>
