<?php
/*##################################################
 *		       ModcatartDeleteItemController.class.php
 *                            -------------------
 *   begin                : Month XX, 2017
 *   copyright            : (C) 2017 Firstname LASTNAME
 *   email                : nickname@phpboost.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

/**
 * @author Firstname LASTNAME <nickname@phpboost.com>
 */

class ModcatartDeleteItemController extends ModuleController
{
	public function execute(HTTPRequestCustom $request)
	{
		AppContext::get_session()->csrf_get_protect();

		$itemcatart = $this->get_itemcatart($request);

		if (!$itemcatart->is_authorized_to_delete())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}

		if (AppContext::get_current_user()->is_readonly())
		{
			$controller = PHPBoostErrors::user_in_read_only();
			DispatchManager::redirect($controller);
		}

		ModcatartService::delete('WHERE id=:id', array('id' => $itemcatart->get_id()));
		ModcatartService::get_keywords_manager()->delete_relations($itemcatart->get_id());

		PersistenceContext::get_querier()->delete(DB_TABLE_EVENTS, 'WHERE module=:module AND id_in_module=:id', array('module' => 'modcatart', 'id' => $itemcatart->get_id()));

		CommentsService::delete_comments_topic_module('modcatart', $itemcatart->get_id());
		NotationService::delete_notes_id_in_module('modcatart', $itemcatart->get_id());

		Feed::clear_cache('modcatart');
		ModcatartCategoriesCache::invalidate();

		AppContext::get_response()->redirect(($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), ModcatartUrlBuilder::display_item($itemcatart->get_category()->get_id(), $itemcatart->get_category()->get_rewrited_name(), $itemcatart->get_id(), $itemcatart->get_rewrited_title())->rel()) ? $request->get_url_referrer() : ModcatartUrlBuilder::home()), StringVars::replace_vars(LangLoader::get_message('modcatart.message.success.delete', 'common', 'modcatart'), array('title' => $itemcatart->get_title())));
	}

	private function get_itemcatart(HTTPRequestCustom $request)
	{
		$id = $request->get_getint('id', 0);
		if (!empty($id))
		{
			try {
				return ModcatartService::get_itemcatart('WHERE modcatart.id=:id', array('id' => $id));
			} catch (RowNotFoundException $e) {
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}
		}
	}
}
?>
