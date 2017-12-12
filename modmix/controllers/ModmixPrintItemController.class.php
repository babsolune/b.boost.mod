<?php
/*##################################################
 *		       ModmixPrintItemController.class.php
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

class ModmixPrintItemController extends ModuleController
{
	private $lang;
	private $view;
	private $itemmix;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->init();

		$this->build_view($request);

		return new SiteNodisplayResponse($this->view);
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'modmix');
		$this->view = new FileTemplate('framework/content/print.tpl');
		$this->view->add_lang($this->lang);
	}

	private function get_itemmix()
	{
		if ($this->itemmix === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try
				{
					$this->itemmix = ModmixService::get_itemmix('WHERE modmix.id=:id', array('id' => $id));
				}
				catch (RowNotFoundException $e)
				{
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->itemmix = new Itemmix();
		}
		return $this->itemmix;
	}

	private function build_view()
	{
		$this->view->put_all(array(
			'PAGE_TITLE' => $this->lang['modmix.print.item'] . ' - ' . $this->itemmix->get_title() . ' - ' . GeneralConfig::load()->get_site_name(),
			'TITLE' => $this->itemmix->get_title(),
			'CONTENT' => FormatingHelper::second_parse($this->itemmix->get_contents())
		));
	}

	private function check_authorizations()
	{
		$itemmix = $this->get_itemmix();

		$not_authorized = !ModmixAuthorizationsService::check_authorizations($itemmix->get_category_id())->write() && (!ModmixAuthorizationsService::check_authorizations($itemmix->get_category_id())->moderation() && $itemmix->get_author_user()->get_id() != AppContext::get_current_user()->get_id());

		switch ($itemmix->get_publication_state())
		{
			case Itemmix::PUBLISHED_NOW:
				if (!ModmixAuthorizationsService::check_authorizations()->read() && $not_authorized)
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case Itemmix::NOT_PUBLISHED:
				if ($not_authorized)
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case Itemmix::PUBLICATION_DATE:
				if (!$itemmix->is_published() && $not_authorized)
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
