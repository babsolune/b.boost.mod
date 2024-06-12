<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballDeleteCompetController extends DefaultModuleController
{
	public function execute(HTTPRequestCustom $request)
	{
		AppContext::get_session()->csrf_get_protect();

		$compet = $this->get_compet($request);

		if (!$compet->is_authorized_to_delete() || AppContext::get_current_user()->is_readonly())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}

		FootballCompetService::delete($compet->get_id_compet());

		if (!FootballAuthorizationsService::check_authorizations()->write() && FootballAuthorizationsService::check_authorizations()->contribution())
			ContributionService::generate_cache();

		FootballCompetService::clear_cache();
		HooksService::execute_hook_action('delete', self::$module_id, $compet->get_properties());

		AppContext::get_response()->redirect(($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), FootballUrlBuilder::compet_home($compet->get_id_compet())->rel()) ? $request->get_url_referrer() : FootballUrlBuilder::home()), StringVars::replace_vars($this->lang['football.message.success.delete'], array('title' => $compet->get_compet_name())));
	}

	private function get_compet(HTTPRequestCustom $request)
	{
		$id = $request->get_getint('id', 0);
		if (!empty($id))
		{
			try {
				return FootballCompetService::get_compet($id);
			} catch (RowNotFoundException $e) {
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}
		}
	}
}
?>
