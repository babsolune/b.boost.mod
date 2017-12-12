<?php
/*##################################################
 *		       ModcatartPrintItemController.class.php
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

class ModcatartPrintItemController extends ModuleController
{
	private $lang;
	private $view;
	private $itemcatart;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->init();

		$this->build_view($request);

		return new SiteNodisplayResponse($this->view);
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'modcatart');
		$this->view = new FileTemplate('framework/content/print.tpl');
		$this->view->add_lang($this->lang);
	}

	private function get_itemcatart()
	{
		if ($this->itemcatart === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try
				{
					$this->itemcatart = ModcatartService::get_itemcatart('WHERE modcatart.id=:id', array('id' => $id));
				}
				catch (RowNotFoundException $e)
				{
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->itemcatart = new Itemcatart();
		}
		return $this->itemcatart;
	}

	private function build_view()
	{
		$this->view->put_all(array(
			'PAGE_TITLE' => $this->lang['modcatart.print.item'] . ' - ' . $this->itemcatart->get_title() . ' - ' . GeneralConfig::load()->get_site_name(),
			'TITLE' => $this->itemcatart->get_title(),
			'CONTENT' => FormatingHelper::second_parse($this->itemcatart->get_contents())
		));
	}

	private function check_authorizations()
	{
		$itemcatart = $this->get_itemcatart();

		$not_authorized = !ModcatartAuthorizationsService::check_authorizations($itemcatart->get_category_id())->write() && (!ModcatartAuthorizationsService::check_authorizations($itemcatart->get_category_id())->moderation() && $itemcatart->get_author_user()->get_id() != AppContext::get_current_user()->get_id());

		switch ($itemcatart->get_publication_state())
		{
			case Itemcatart::PUBLISHED_NOW:
				if (!ModcatartAuthorizationsService::check_authorizations()->read() && $not_authorized)
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case Itemcatart::NOT_PUBLISHED:
				if ($not_authorized)
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case Itemcatart::PUBLICATION_DATE:
				if (!$itemcatart->is_published() && $not_authorized)
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
