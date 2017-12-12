<?php
/*##################################################
 *		       ModcatPrintItemController.class.php
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

class ModcatPrintItemController extends ModuleController
{
	private $lang;
	private $view;
	private $itemcat;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->init();

		$this->build_view($request);

		return new SiteNodisplayResponse($this->view);
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'modcat');
		$this->view = new FileTemplate('framework/content/print.tpl');
		$this->view->add_lang($this->lang);
	}

	private function get_itemcat()
	{
		if ($this->itemcat === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try
				{
					$this->itemcat = ModcatService::get_itemcat('WHERE modcat.id=:id', array('id' => $id));
				}
				catch (RowNotFoundException $e)
				{
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->itemcat = new Itemcat();
		}
		return $this->itemcat;
	}

	private function build_view()
	{
		$this->view->put_all(array(
			'PAGE_TITLE' => $this->lang['modcat.print.item'] . ' - ' . $this->itemcat->get_title() . ' - ' . GeneralConfig::load()->get_site_name(),
			'TITLE' => $this->itemcat->get_title(),
			'CONTENT' => FormatingHelper::second_parse($this->itemcat->get_contents())
		));
	}

	private function check_authorizations()
	{
		$itemcat = $this->get_itemcat();

		$not_authorized = !ModcatAuthorizationsService::check_authorizations($itemcat->get_category_id())->write() && (!ModcatAuthorizationsService::check_authorizations($itemcat->get_category_id())->moderation() && $itemcat->get_author_user()->get_id() != AppContext::get_current_user()->get_id());

		switch ($itemcat->get_publication_state())
		{
			case Itemcat::PUBLISHED_NOW:
				if (!ModcatAuthorizationsService::check_authorizations()->read() && $not_authorized)
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case Itemcat::NOT_PUBLISHED:
				if ($not_authorized)
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case Itemcat::PUBLICATION_DATE:
				if (!$itemcat->is_published() && $not_authorized)
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
