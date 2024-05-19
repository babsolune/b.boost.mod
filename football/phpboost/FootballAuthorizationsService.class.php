<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballAuthorizationsService extends CategoriesAuthorizationsService
{
	const MANAGE_DIVISIONS_AUTHORIZATIONS = 32;
	const MANAGE_CLUBS_AUTHORIZATIONS = 64;
	const MANAGE_SEASONS_AUTHORIZATIONS = 128;
	const MANAGE_PARAMETERS_AUTHORIZATIONS = 256;
	const MANAGE_TEAMS_AUTHORIZATIONS = 512;
	const MANAGE_MATCHES_AUTHORIZATIONS = 1024;
	const MANAGE_RESULTS_AUTHORIZATIONS = 2048;

	public function manage_divisions()
	{
		return $this->is_authorized(self::MANAGE_DIVISIONS_AUTHORIZATIONS);
	}

	public function manage_clubs()
	{
		return $this->is_authorized(self::MANAGE_CLUBS_AUTHORIZATIONS);
	}

	public function manage_seasons()
	{
		return $this->is_authorized(self::MANAGE_SEASONS_AUTHORIZATIONS);
	}

	public function manage_params()
	{
		return $this->is_authorized(self::MANAGE_PARAMETERS_AUTHORIZATIONS);
	}

	public function manage_matches()
	{
		return $this->is_authorized(self::MANAGE_MATCHES_AUTHORIZATIONS);
	}

	public function manage_results()
	{
		return $this->is_authorized(self::MANAGE_RESULTS_AUTHORIZATIONS);
	}

	protected function is_authorized($bit, $mode = Authorizations::AUTH_CHILD_PRIORITY)
	{
		$auth = CategoriesService::get_categories_manager('football')->get_heritated_authorizations($this->id_category, $bit, $mode);
		return AppContext::get_current_user()->check_auth($auth, $bit);
	}
}
?>
