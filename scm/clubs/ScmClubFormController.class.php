<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmClubFormController extends DefaultModuleController
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
            $this->view->put('MESSAGE_HELPER', MessageHelper::display(StringVars::replace_vars($this->lang['scm.warning.add.club'], ['name' => $this->get_club()->get_club_name()]), MessageHelper::SUCCESS, 4));
		}

		$this->view->put('CONTENT', $this->form->display());

		return $this->generate_response($this->view);
	}

	private function build_form(HTTPRequestCustom $request)
	{
		$form = new HTMLForm(__CLASS__);
		$form->set_layout_title($this->get_club()->get_id_club() === null ? $this->lang['scm.add.club'] : ($this->lang['scm.edit.club']));

		$fieldset = new FormFieldsetHTML('scm', $this->lang['form.parameters']);
		$form->add_fieldset($fieldset);

        $fieldset->add_field(new FormFieldTextEditor('name', $this->lang['scm.club.name'], $this->get_club()->get_club_name(),
            ['required' => true],
			[$this->is_new_club ? new ScmConstraintClubNameExists() : '']
        ));

        $fieldset->add_field(new FormFieldTextEditor('full_name', $this->lang['scm.club.full.name'], $this->get_club()->get_club_full_name()));

        $fieldset->add_field(new FormFieldMailEditor('email', $this->lang['scm.club.email'], $this->get_club()->get_club_email()));

        $fieldset->add_field(new FormFieldTelEditor('phone', $this->lang['scm.club.phone'], $this->get_club()->get_club_phone(),
            ['description' => $this->lang['scm.club.phone.clue']]
        ));

        $fieldset->add_field(new ScmFormFieldColors('colors', $this->lang['scm.club.colors'], $this->get_club()->get_club_colors(),
			['description' => $this->lang['scm.club.colors.clue']]
		));

		if ($this->config->is_googlemaps_available())
		{
			$fieldset->add_field(new GoogleMapsFormFieldMultipleMarkers('locations', $this->lang['scm.club.locations'], $this->get_club()->get_club_locations(),
				[
					'events' => ['blur' => '
						if (HTMLForms.getField("locations").getValue()) {
							HTMLForms.getField("map_displayed").enable();
						} else {
							HTMLForms.getField("map_displayed").disable();
						}'
					]
				]
			));

			$fieldset->add_field(new FormFieldCheckbox('map_display', $this->lang['scm.club.display.map'], $this->get_club()->get_club_map_display()));
		}
		else {
            $fieldset->add_field(new FormFieldShortMultiLineTextEditor('locations', $this->lang['scm.club.locations'], $this->get_club()->get_club_locations()));
        }

        $fieldset->add_field(new FormFieldSimpleSelectChoice('flag', $this->lang['scm.club.flag'], $this->get_club()->get_club_flag(), $this->flag_list()));

        $fieldset->add_field(new FormFieldUploadFile('logo', $this->lang['scm.club.logo'], $this->is_new_club ? ScmClub::CLUB_LOGO : $this->get_club()->get_club_logo()));

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
        $club->set_club_colors($this->form->get_value('colors'));
        $club->set_club_flag($this->form->get_value('flag')->get_raw_value());
        $club->set_club_logo($this->form->get_value('logo'));
        $club->set_club_locations($this->form->get_value('locations'));
        $club->set_club_map_display($this->form->get_value('map_display'));

		if ($this->is_new_club)
		{
			$id = ScmClubService::add_club($club);
			$club->set_id_club($id);
            HooksService::execute_hook_action('club_add', 'scm', array_merge($club->get_properties(), [
                'action' => $this->lang['scm.hook.add.club'],
                'id' => $club->get_id_club(),
                'title' => $club->get_club_name(),
                'url' => $club->get_club_url()
            ]));
        }
		else
		{
			ScmClubService::update_club($club);
            HooksService::execute_hook_action('club_edit', 'scm', array_merge($club->get_properties(), [
                'action' => $this->lang['scm.hook.edit.club'],
                'id' => $club->get_id_club(),
                'title' => $club->get_club_name(),
                'url' => $club->get_club_url()
            ]));
        }

		ScmEventService::clear_cache();
	}

    private function flag_list()
    {
        $options = [];
        $options[] = new FormFieldSelectChoiceOption('', '');
		foreach(Countries::get_countries() as $country)
		{
            $file = new File($country['picture']);
            $id = $file->get_name_without_extension();
			$options[] = new FormFieldSelectChoiceOption($country['name'], $id);
		}

		return $options;
    }

	private function get_club()
	{
		if ($this->club === null)
		{
			$id = AppContext::get_request()->get_getint('club_id', 0);
			if (!empty($id))
			{
				try {
					$this->club = ScmClubService::get_club($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_club = true;
				$this->club = new ScmClub();
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
            AppContext::get_response()->redirect(ScmUrlBuilder::manage_clubs());
        else
            AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : ScmUrlBuilder::manage_clubs()));
	}

	private function generate_response(View $view)
	{
		$club = $this->get_club();
        $cache = ScmClubCache::load();

		$location_id = $club->get_id_club() ? 'scm-edit-'. $club->get_id_club() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());

		if ($club->get_id_club() === null)
		{
			$breadcrumb->add($this->lang['scm.add.club'], ScmUrlBuilder::add_club($club->get_id_club()));
			$graphical_environment->set_page_title($this->lang['scm.add.club'], $this->lang['scm.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.add.club']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::add_club());
		}
		else
		{
			if (!AppContext::get_session()->location_id_already_exists($location_id))
				$graphical_environment->set_location_id($location_id);

			$graphical_environment->set_page_title($this->lang['scm.edit.club'], $this->lang['scm.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.edit.club']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit_club($club->get_id_club(), $club->get_club_slug()));

            $breadcrumb->add($this->lang['scm.clubs.manager'], ScmUrlBuilder::manage_clubs());
			$breadcrumb->add($cache->get_club_name($club->get_id_club()), ScmUrlBuilder::display_club($club->get_id_club(), $club->get_club_slug()));
			$breadcrumb->add($this->lang['scm.edit.club'], ScmUrlBuilder::edit_club($club->get_id_club(), $club->get_club_slug()));
		}

		return $response;
	}
}
?>
