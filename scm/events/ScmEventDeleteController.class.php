<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmEventDeleteController extends DefaultModuleController
{
	public function execute(HTTPRequestCustom $request)
	{
		AppContext::get_session()->csrf_get_protect();

		$event = $this->get_event($request);

		if (!$event->is_authorized_to_delete() || AppContext::get_current_user()->is_readonly())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}

		ScmEventService::delete($event->get_id());

		if (!ScmAuthorizationsService::check_authorizations()->write() && ScmAuthorizationsService::check_authorizations()->contribution())
			ContributionService::generate_cache();

		ScmEventService::clear_cache();
		HooksService::execute_hook_action('delete', self::$module_id, $event->get_properties());

		AppContext::get_response()->redirect(($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug())->rel()) ? $request->get_url_referrer() : ScmUrlBuilder::home()), StringVars::replace_vars($this->lang['scm.message.success.delete'], array('title' => $event->get_event_name())));
	}

	private function get_event(HTTPRequestCustom $request)
	{
		$id = $request->get_getint('id', 0);
		if (!empty($id))
		{
			try {
				return ScmEventService::get_event($id);
			} catch (RowNotFoundException $e) {
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}
		}
	}
}
?>
