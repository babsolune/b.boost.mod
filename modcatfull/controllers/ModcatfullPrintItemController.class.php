<?php
/*##################################################
 *		       ModcatfullPrintItemController.class.php
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

class ModcatfullPrintItemController extends ModuleController
{
	private $lang;
	private $view;
	private $itemcatfull;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->init();

		$this->build_view($request);

		return new SiteNodisplayResponse($this->view);
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'modcatfull');
		$this->view = new FileTemplate('framework/content/print.tpl');
		$this->view->add_lang($this->lang);
	}

	private function get_itemcatfull()
	{
		if ($this->itemcatfull === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try
				{
					$this->itemcatfull = ModcatfullService::get_itemcatfull('WHERE modcatfull.id=:id', array('id' => $id));
				}
				catch (RowNotFoundException $e)
				{
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->itemcatfull = new Itemcatfull();
		}
		return $this->itemcatfull;
	}

	private function build_view()
	{
		$this->view->put_all(array(
			'PAGE_TITLE' => $this->lang['modcatfull.print.item'] . ' - ' . $this->itemcatfull->get_title() . ' - ' . GeneralConfig::load()->get_site_name(),
			'TITLE' => $this->itemcatfull->get_title(),
			'CONTENT' => FormatingHelper::second_parse($this->itemcatfull->get_contents())
		));
	}

	private function check_authorizations()
	{
		$itemcatfull = $this->get_itemcatfull();

		$not_authorized = !ModcatfullAuthorizationsService::check_authorizations($itemcatfull->get_category_id())->write() && (!ModcatfullAuthorizationsService::check_authorizations($itemcatfull->get_category_id())->moderation() && $itemcatfull->get_author_user()->get_id() != AppContext::get_current_user()->get_id());

		switch ($itemcatfull->get_publication_state())
		{
			case Itemcatfull::PUBLISHED_NOW:
				if (!ModcatfullAuthorizationsService::check_authorizations()->read() && $not_authorized)
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case Itemcatfull::NOT_PUBLISHED:
				if ($not_authorized)
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case Itemcatfull::PUBLICATION_DATE:
				if (!$itemcatfull->is_published() && $not_authorized)
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			default:
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			break;
		}
	}
}
?>
