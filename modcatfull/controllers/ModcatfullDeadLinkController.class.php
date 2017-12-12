<?php
/*##################################################
 *                          ModcatfullDeadLinkController.class.php
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

class ModcatfullDeadLinkController extends AbstractController
{
	private $itemcatfull;

	public function execute(HTTPRequestCustom $request)
	{
		$id = $request->get_getint('id', 0);

		if (!empty($id) && AppContext::get_current_user()->check_level(User::MEMBER_LEVEL))
		{
			try {
				$this->itemcatfull = ModcatfullService::get_itemcatfull('WHERE modcatfull.id = :id', array('id' => $id));
			} catch (RowNotFoundException $e) {
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}
		}

		if ($this->itemcatfull !== null)
		{
			if (!PersistenceContext::get_querier()->row_exists(PREFIX . 'events', 'WHERE id_in_module=:id_in_module AND module=\'modcatfull\' AND current_status = 0', array('id_in_module' => $this->itemcatfull->get_id())))
			{
				$contribution = new Contribution();
				$contribution->set_id_in_module($this->itemcatfull->get_id());
				$contribution->set_entitled(StringVars::replace_vars(LangLoader::get_message('contribution.deadlink', 'common'), array('link_name' => $this->itemcatfull->get_title())));
				$contribution->set_fixing_url(ModcatfullUrlBuilder::edit_item($this->itemcatfull->get_id())->relative());
				$contribution->set_description(LangLoader::get_message('contribution.deadlink_explain', 'common'));
				$contribution->set_poster_id(AppContext::get_current_user()->get_id());
				$contribution->set_module('modcatfull');
				$contribution->set_type('alert');
				$contribution->set_auth(
					Authorizations::capture_and_shift_bit_auth(
						ModcatfullService::get_categories_manager()->get_heritated_authorizations($this->itemcatfull->get_category_id(), Category::MODERATION_AUTHORIZATIONS, Authorizations::AUTH_CHILD_PRIORITY),
						Category::MODERATION_AUTHORIZATIONS, Contribution::CONTRIBUTION_AUTH_BIT
					)
				);

				ContributionService::save_contribution($contribution);
			}

			DispatchManager::redirect(new UserContributionSuccessController());
		}
		else
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}
	}
}
?>
