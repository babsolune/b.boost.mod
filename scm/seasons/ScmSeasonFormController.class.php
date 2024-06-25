<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmSeasonFormController extends DefaultModuleController
{
	private $season;
	private $is_new_season;

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
		$form->set_layout_title($this->get_season()->get_id_season() === null ? $this->lang['scm.add.season'] : ($this->lang['scm.edit.season']));

		$fieldset = new FormFieldsetHTML('scm', $this->lang['form.parameters']);
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldSimpleSelectChoice('year', $this->lang['scm.season.year'], $this->is_new_season ? date("Y") : $this->get_season()->get_first_year(), $this->get_years_list()));

        $fieldset->add_field(new FormFieldCheckbox('calendar_year', $this->lang['scm.calendar.year'], $this->get_season()->get_calendar_year(),
            array('description' => $this->lang['scm.calendar.year.clue'])
        ));

        $fieldset->add_field(new FormFieldHidden('referrer', $request->get_url_referrer()));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

    private function get_years_list()
    {
		$options = array();
		$options[] = new FormFieldSelectChoiceOption('', '');
        for($i = 1950; $i <= 2140; $i++)
        {
            $options[] = new FormFieldSelectChoiceOption($i, $i);
        }
        return $options;
    }

	private function get_season()
	{
		if ($this->season === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try {
					$this->season = ScmSeasonService::get_season($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_season = true;
				$this->season = new ScmSeason();
				$this->season->init_default_properties();
			}
		}
		return $this->season;
	}

	private function check_authorizations()
	{
		$season = $this->get_season();

		if ($season->get_id_season() === null)
		{
			if (!$season->is_authorized_to_manage())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!$season->is_authorized_to_manage())
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
		$season = $this->get_season();

        $season->set_first_year($this->form->get_value('year')->get_raw_value());
        $season->set_calendar_year($this->form->get_value('calendar_year'));
        
        if ($this->form->get_value('calendar_year'))
            $season->set_season_name($season->get_first_year());
        else
        $season->set_season_name($season->get_first_year() . ' - ' . $season->get_first_year() + 1);

		if ($this->is_new_season)
		{
			$id = ScmSeasonService::add_season($season);
			$season->set_id_season($id);
        }
		else
		{
			ScmSeasonService::update_season($season);
        }

		ScmEventService::clear_cache();
	}

	private function redirect()
	{
		$season = $this->get_season();

        if ($this->is_new_season)
            AppContext::get_response()->redirect(ScmUrlBuilder::manage_seasons());
        else
            AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : ScmUrlBuilder::manage_seasons()));
	}

	private function generate_response(View $view)
	{
		$season = $this->get_season();

		$location_id = $season->get_id_season() ? 'scm-edit-'. $season->get_id_season() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());

		if ($season->get_id_season() === null)
		{
			$breadcrumb->add($this->lang['scm.add.season'], ScmUrlBuilder::add_season($season->get_id_season()));
			$graphical_environment->set_page_title($this->lang['scm.add.season'], $this->lang['scm.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.add.season']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::add_season($season->get_id_season()));
		}
		else
		{
			if (!AppContext::get_session()->location_id_already_exists($location_id))
				$graphical_environment->set_location_id($location_id);

			$graphical_environment->set_page_title($this->lang['scm.edit.season'], $this->lang['scm.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.edit.season']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit_season($season->get_id_season()));

            $breadcrumb->add($this->lang['scm.seasons.manager'], ScmUrlBuilder::manage_seasons());
			$breadcrumb->add($season->get_name(), '');
			$breadcrumb->add($this->lang['scm.edit.season'], ScmUrlBuilder::edit_season($season->get_id_season()));
		}

		return $response;
	}
}
?>
