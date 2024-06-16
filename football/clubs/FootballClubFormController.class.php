<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballClubFormController extends DefaultModuleController
{
	private $club;
	private $is_new_club;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

        $token = $request->get_string('token', '');

		$this->build_form($request);

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$this->redirect();
            $this->view->put('MESSAGE_HELPER', MessageHelper::display(StringVars::replace_vars($this->lang['football.warning.add.club'], array('name' => $this->get_club()->get_club_name())), MessageHelper::SUCCESS, 4));
		}

		$this->view->put('CONTENT', $this->form->display());

		return $this->generate_response($this->view);
	}

	private function build_form(HTTPRequestCustom $request)
	{
		$form = new HTMLForm(__CLASS__);
		$form->set_layout_title($this->get_club()->get_id_club() === null ? $this->lang['football.add.club'] : ($this->lang['football.edit.club']));

		$fieldset = new FormFieldsetHTML('football', $this->lang['form.parameters']);
		$form->add_fieldset($fieldset);

        $fieldset->add_field(new FormFieldTextEditor('name', $this->lang['football.club.name'], $this->get_club()->get_club_name(),
            array('required' => true,)
        ));

        $fieldset->add_field(new FormFieldTextEditor('full_name', $this->lang['football.club.full.name'], $this->get_club()->get_club_full_name()));

        $fieldset->add_field(new FormFieldMailEditor('email', $this->lang['football.club.email'], $this->get_club()->get_club_email()));

        $fieldset->add_field(new FormFieldTelEditor('phone', $this->lang['football.club.phone'], $this->get_club()->get_club_phone(),
            array('description' => $this->lang['football.club.phone.clue'])
        ));

		if ($this->config->is_googlemaps_available())
		{
			$fieldset->add_field(new GoogleMapsFormFieldMultipleMarkers('locations', $this->lang['football.club.locations'], $this->get_club()->get_club_locations(),
				array(
					'events' => array('blur' => '
						if (HTMLForms.getField("locations").getValue()) {
							HTMLForms.getField("map_displayed").enable();
						} else {
							HTMLForms.getField("map_displayed").disable();
						}'
					)
				)
			));

			$fieldset->add_field(new FormFieldCheckbox('map_display', $this->lang['football.club.display.map'], $this->get_club()->get_club_map_display(),
				array()
			));
		}
		else {
            $fieldset->add_field(new FormFieldShortMultiLineTextEditor('locations', $this->lang['football.club.locations'], $this->get_club()->get_club_locations()));
        }

        $fieldset->add_field(new FormFieldUploadFile('logo', $this->lang['football.club.logo'], $this->is_new_club ? FootballClub::CLUB_LOGO : $this->get_club()->get_club_logo()));

		$fieldset->add_field(new FormFieldHidden('referrer', $request->get_url_referrer()));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function save()
	{
		$club = $this->get_club();

        $club->set_club_name($this->form->get_value('name'));
		$club->set_club_slug(Url::encode_rewrite($club->get_club_name()));
        $club->set_club_full_name($this->form->get_value('full_name'));
        $club->set_club_email($this->form->get_value('email'));
        $club->set_club_phone($this->form->get_value('phone'));
        $club->set_club_logo($this->form->get_value('logo'));
        $club->set_club_locations($this->form->get_value('locations'));
        $club->set_club_map_display($this->form->get_value('map_display'));

		if ($this->is_new_club)
		{
			$id = FootballClubService::add_club($club);
			$club->set_id_club($id);
        }
		else
		{
			FootballClubService::update_club($club);
        }

		FootballCompetService::clear_cache();
	}

	private function get_club()
	{
		if ($this->club === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try {
					$this->club = FootballClubService::get_club($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_club = true;
				$this->club = new FootballClub();
				$this->club->init_default_properties();
			}
		}
		return $this->club;
	}

	private function check_authorizations()
	{
		$club = $this->get_club();

		if ($club->get_id_club() === null)
		{
			if (!$club->is_authorized_to_manage())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!$club->is_authorized_to_manage())
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

	private function redirect()
	{
		$club = $this->get_club();

        if ($this->is_new_club)
            AppContext::get_response()->redirect(FootballUrlBuilder::manage_clubs());
        else
            AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : FootballUrlBuilder::manage_clubs()));
	}

	private function generate_response(View $view)
	{
		$club = $this->get_club();

		$location_id = $club->get_id_club() ? 'football-edit-'. $club->get_id_club() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'], FootballUrlBuilder::home());

		if ($club->get_id_club() === null)
		{
			$breadcrumb->add($this->lang['football.add.club'], FootballUrlBuilder::add_club($club->get_id_club()));
			$graphical_environment->set_page_title($this->lang['football.add.club'], $this->lang['football.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['football.add.club']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::add_club($club->get_id_club()));
		}
		else
		{
			if (!AppContext::get_session()->location_id_already_exists($location_id))
				$graphical_environment->set_location_id($location_id);

			$graphical_environment->set_page_title($this->lang['football.edit.club'], $this->lang['football.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['football.edit.club']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::edit_club($club->get_id_club()));

            $breadcrumb->add($this->lang['football.clubs.manager'], FootballUrlBuilder::manage_clubs());
			$breadcrumb->add($club->get_club_full_name() ? $club->get_club_full_name() : $club->get_club_name(), FootballUrlBuilder::display_club($club->get_id_club()));
			$breadcrumb->add($this->lang['football.edit.club'], FootballUrlBuilder::edit_club($club->get_id_club()));
		}

		return $response;
	}
}
?>
