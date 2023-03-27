<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 01 09
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class GuideDeleteItemController extends DefaultModuleController
{
	public function execute(HTTPRequestCustom $request)
	{
		AppContext::get_session()->csrf_get_protect();

		$this->item = $this->get_item($request);
		$this->check_authorizations();

		if($request->get_int('content_id') == 0)
		{
			foreach($this->get_item_contents($request) as $content)
			{
				PersistenceContext::get_querier()->delete(GuideSetup::$guide_contents_table, 'WHERE item_id = :id', array('id' => $this->item->get_id()));
			}
		}
		GuideService::delete($this->item->get_id(), $request->get_int('content_id'));

		if (!GuideAuthorizationsService::check_authorizations()->write() && GuideAuthorizationsService::check_authorizations()->contribution())
			ContributionService::generate_cache();

		GuideService::clear_cache();
		HooksService::execute_hook_action('delete', self::$module_id, $this->item->get_properties());

		AppContext::get_response()->redirect((
			$request->get_url_referrer() && !TextHelper::strstr(
				$request->get_url_referrer(),
				GuideUrlBuilder::display($this->item->get_category()->get_id(), $this->item->get_category()->get_rewrited_name(), $this->item->get_id(), $this->item->get_rewrited_title())->rel()) ? $request->get_url_referrer() : GuideUrlBuilder::home()),
				$request->get_int('content_id') === 0 ?
					StringVars::replace_vars($this->lang['guide.message.success.delete'], array('title' => $this->item->get_item_content()->get_title())) :
					StringVars::replace_vars($this->lang['guide.message.success.delete.content'], array('content' => $request->get_int('content_id'),'title' => $this->item->get_item_content()->get_title())
		));
	}

	private function check_authorizations()
	{
		if (!$this->item->is_authorized_to_delete()) {
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
		if (AppContext::get_current_user()->is_readonly()) {
			$error_controller = PHPBoostErrors::user_in_read_only();
			DispatchManager::redirect($error_controller);
		}
	}

	private function get_item(HTTPRequestCustom $request)
	{
		$id = $request->get_getint('id', 0);
		if (!empty($id))
		{
			try {
				return GuideService::get_item($id);
			} catch (RowNotFoundException $e) {
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}
		}
	}

	private function get_item_contents(HTTPRequestCustom $request)
	{
		$id = $request->get_getint('id', 0);
		if (!empty($id))
		{
			try {
				return GuideService::get_item_content($id);
			} catch (RowNotFoundException $e) {
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}
		}
	}
}
?>
