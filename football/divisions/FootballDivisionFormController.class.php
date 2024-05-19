<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

// namespace PHPBoost\Football\Controllers\Divisions;

class FootballDivisionFormController extends DefaultModuleController
{
	private $division;
	private $is_new_division;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_form($request);

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$this->redirect();
		}

		$this->view->put('CONTENT', $this->form->display());

		return $this->generate_response($this->view);
	}

	private function build_form(HTTPRequestCustom $request)
	{
		$form = new HTMLForm(__CLASS__);
		$form->set_layout_title($this->get_division()->get_id_division() === null ? $this->lang['football.add.division'] : ($this->lang['football.edit.division']));

		$fieldset = new FormFieldsetHTML('football', $this->lang['form.parameters']);
		$form->add_fieldset($fieldset);

        $fieldset->add_field(new FormFieldTextEditor('name', $this->lang['form.name'], $this->get_division()->get_division_name(), 
			array('required' => true)
		));

		$fieldset->add_field(new FormFieldSimpleSelectChoice('compet_type', $this->lang['football.compet.type'], $this->get_division()->get_division_compet_type(),
			array(
				new FormFieldSelectChoiceOption($this->lang['football.championship'], FootballDivision::CHAMPIONSHIP),
				new FormFieldSelectChoiceOption($this->lang['football.cup'], FootballDivision::CUP),
				new FormFieldSelectChoiceOption($this->lang['football.tournament'], FootballDivision::TOURNAMENT)
			)
		));

		$fieldset->add_field(new FormFieldSimpleSelectChoice('match_type', $this->lang['football.match.type'], $this->get_division()->get_division_match_type(),
			array(
				new FormFieldSelectChoiceOption($this->lang['football.single.matches'], FootballDivision::SINGLE_MATCHES),
				new FormFieldSelectChoiceOption($this->lang['football.return.matches'], FootballDivision::RETURN_MATCHES)
			)
		));

		$fieldset->add_field(new FormFieldHidden('referrer', $request->get_url_referrer()));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function get_division()
	{
		if ($this->division === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try {
					$this->division = FootballDivisionService::get_division($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_division = true;
				$this->division = new FootballDivision();
				$this->division->init_default_properties();
			}
		}
		return $this->division;
	}

	private function check_authorizations()
	{
		$division = $this->get_division();

		if ($division->get_id_division() === null)
		{
			if (!$division->is_authorized_to_manage())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!$division->is_authorized_to_manage())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		if (AppContext::get_current_user()->is_readonly())
		{
			$controller = PHPBoostErrors::user_in_read_only();
			DispatchManager::redirect($controller);
		}
	}

	private function save()
	{
		$division = $this->get_division();

        $division->set_division_name($this->form->get_value('name'));
        $division->set_division_compet_type($this->form->get_value('compet_type')->get_raw_value());
        $division->set_division_match_type($this->form->get_value('match_type')->get_raw_value());

		if ($this->is_new_division)
		{
			$id = FootballDivisionService::add_division($division);
			$division->set_id_division($id);
        }
		else
		{
			FootballDivisionService::update_division($division);
        }

		FootballCompetService::clear_cache();
	}

	private function redirect()
	{
		$division = $this->get_division();

        if ($this->is_new_division)
            AppContext::get_response()->redirect(FootballUrlBuilder::manage_divisions());
        else
            AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : FootballUrlBuilder::manage_divisions()));
	}

	private function generate_response(View $view)
	{
		$division = $this->get_division();

		$location_id = $division->get_id_division() ? 'football-edit-'. $division->get_id_division() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'], FootballUrlBuilder::home());

		if ($division->get_id_division() === null)
		{
			$breadcrumb->add($this->lang['football.add.division'], FootballUrlBuilder::add_division($division->get_id_division()));
			$graphical_environment->set_page_title($this->lang['football.add.division'], $this->lang['football.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['football.add.division']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::add_division($division->get_id_division()));
		}
		else
		{
			if (!AppContext::get_session()->location_id_already_exists($location_id))
				$graphical_environment->set_location_id($location_id);

			$graphical_environment->set_page_title($this->lang['football.edit.division'], $this->lang['football.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['football.edit.division']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::edit_division($division->get_id_division()));

			$breadcrumb->add($division->get_division_name(), '');
			$breadcrumb->add($this->lang['football.edit.division'], FootballUrlBuilder::edit_division($division->get_id_division()));
		}

		return $response;
	}
}
?>
