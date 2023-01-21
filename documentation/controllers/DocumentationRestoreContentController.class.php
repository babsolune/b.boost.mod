<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 01 09
 * @since       PHPBoost 6.0 - 2022 12 02
 */

class DocumentationRestoreContentController extends DefaultModuleController
{
	public function execute(HTTPRequestCustom $request)
	{
		AppContext::get_session()->csrf_get_protect();

		$this->item = $this->get_item($request);
		$item_content = $this->get_item_content($request);
		$this->check_authorizations();

		foreach($item_content as $content)
		{
			PersistenceContext::get_querier()->update(DocumentationSetup::$documentation_contents_table, array('active_content' => 0), 'WHERE item_id = :id', array('id' => $this->item->get_id()));
		}
		DocumentationService::restore_content($this->item->get_id(), $request->get_int('content_id'));

		if (!DocumentationAuthorizationsService::check_authorizations()->write() && DocumentationAuthorizationsService::check_authorizations()->contribution())
			ContributionService::generate_cache();

		DocumentationService::clear_cache();
		HooksService::execute_hook_action('restore', self::$module_id, $this->item->get_properties());

		AppContext::get_response()->redirect(($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), DocumentationUrlBuilder::display($this->item->get_category()->get_id(), $this->item->get_category()->get_rewrited_name(), $this->item->get_id(), $this->item->get_rewrited_title())->rel()) ? $request->get_url_referrer() : DocumentationUrlBuilder::home()), StringVars::replace_vars($this->lang['documentation.message.success.restore'], array('content' => $request->get_int('content_id'),'title' => $this->item->get_item_content()->get_title())));
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
				return DocumentationService::get_item($id);
			} catch (RowNotFoundException $e) {
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}
		}
	}

	private function get_item_content(HTTPRequestCustom $request)
	{
		$id = $request->get_getint('id', 0);
		if (!empty($id))
		{
			try {
				return DocumentationService::get_item_content($id);
			} catch (RowNotFoundException $e) {
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}
		}
	}
}
?>
