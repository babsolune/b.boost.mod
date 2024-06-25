<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

// namespace PHPBoost\Scm\Controllers\Divisions;

class ScmDivisionFormController extends DefaultModuleController
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
		$form->set_layout_title($this->get_division()->get_id_division() === null ? $this->lang['scm.add.division'] : ($this->lang['scm.edit.division']));

		$fieldset = new FormFieldsetHTML('scm', $this->lang['form.parameters']);
		$form->add_fieldset($fieldset);

        $fieldset->add_field(new FormFieldTextEditor('name', $this->lang['form.name'], $this->get_division()->get_division_name(), 
			array('required' => true)
		));

        $description = $this->lang['scm.championship.clue'];
        if ($this->get_division()->get_event_type() == ScmDivision::CHAMPIONSHIP)
            $description = $this->lang['scm.championship.clue'];
        elseif ($this->get_division()->get_event_type() == ScmDivision::CUP)
            $description = $this->lang['scm.cup.clue'];
        elseif ($this->get_division()->get_event_type() == ScmDivision::TOURNAMENT)
            $description = $this->lang['scm.tournament.clue'];
		$fieldset->add_field(new FormFieldSimpleSelectChoice('event_type', $this->lang['scm.event.type'], $this->get_division()->get_event_type(),
			array(
				new FormFieldSelectChoiceOption($this->lang['scm.championship'], ScmDivision::CHAMPIONSHIP),
				new FormFieldSelectChoiceOption($this->lang['scm.cup'], ScmDivision::CUP),
				new FormFieldSelectChoiceOption($this->lang['scm.tournament'], ScmDivision::TOURNAMENT)
            ),
            array(
                'description' => $description,
                'events' => array('click' => '
                if (HTMLForms.getField("event_type").getValue() == "'. ScmDivision::CHAMPIONSHIP .'") {
                    jQuery(this).closest(".form-element").find(".field-description").html("' . $this->lang['scm.championship.clue'] . '");
                } else if (HTMLForms.getField("event_type").getValue() == "'. ScmDivision::CUP .'") {
                    jQuery(this).closest(".form-element").find(".field-description").html("' . $this->lang['scm.cup.clue'] . '");
                } else if  (HTMLForms.getField("event_type").getValue() == "'. ScmDivision::TOURNAMENT .'") {
                    jQuery(this).closest(".form-element").find(".field-description").html("' . $this->lang['scm.tournament.clue'] . '");
                }
            '))
		));

		$fieldset->add_field(new FormFieldSimpleSelectChoice('game_type', $this->lang['scm.game.type'], $this->get_division()->get_game_type(),
			array(
				new FormFieldSelectChoiceOption($this->lang['scm.single.games'], ScmDivision::SINGLE_GAMES),
				new FormFieldSelectChoiceOption($this->lang['scm.return.games'], ScmDivision::RETURN_GAMES)
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
					$this->division = ScmDivisionService::get_division($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_division = true;
				$this->division = new ScmDivision();
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
        $division->set_event_type($this->form->get_value('event_type')->get_raw_value());
        $division->set_game_type($this->form->get_value('game_type')->get_raw_value());

		if ($this->is_new_division)
		{
			$id = ScmDivisionService::add_division($division);
			$division->set_id_division($id);
        }
		else
		{
			ScmDivisionService::update_division($division);
        }

		ScmEventService::clear_cache();
	}

	private function redirect()
	{
		$division = $this->get_division();

        if ($this->is_new_division)
            AppContext::get_response()->redirect(ScmUrlBuilder::manage_divisions());
        else
            AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : ScmUrlBuilder::manage_divisions()));
	}

	private function generate_response(View $view)
	{
		$division = $this->get_division();

		$location_id = $division->get_id_division() ? 'scm-edit-'. $division->get_id_division() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());

		if ($division->get_id_division() === null)
		{
			$breadcrumb->add($this->lang['scm.add.division'], ScmUrlBuilder::add_division($division->get_id_division()));
			$graphical_environment->set_page_title($this->lang['scm.add.division'], $this->lang['scm.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.add.division']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::add_division($division->get_id_division()));
		}
		else
		{
			if (!AppContext::get_session()->location_id_already_exists($location_id))
				$graphical_environment->set_location_id($location_id);

			$graphical_environment->set_page_title($this->lang['scm.edit.division'], $this->lang['scm.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.edit.division']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit_division($division->get_id_division()));

			$breadcrumb->add($division->get_division_name(), '');
			$breadcrumb->add($this->lang['scm.edit.division'], ScmUrlBuilder::edit_division($division->get_id_division()));
		}

		return $response;
	}
}
?>
