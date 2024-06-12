<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballDaysFormController extends DefaultModuleController
{
    private $compet;
    private $day;
    private $days_number;

	public function execute(HTTPRequestCustom $request)
	{
        $this->init();
		$this->check_authorizations();

        $this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
            $this->view->put('MESSAGE_HELPER', MessageHelper::display($this->lang['football.warning.bracket.update'], MessageHelper::SUCCESS, 4));
		}

		$this->view->put_all(array(
            'MENU' => FootballMenuService::build_compet_menu($this->compet_id()),
            'CONTENT' => $this->form->display(),
            'HAS_MATCHES_WARNING' => FootballMatchService::has_matches($this->compet_id()) ? MessageHelper::display($this->lang['football.warning.has.matches'], MessageHelper::NOTICE) : MessageHelper::display('', '')
        ));

		return $this->generate_response($this->view);
	}

    private function init()
    {
        $teams_number = FootballTeamService::get_teams_number($this->compet_id());
        $c_return_matches = FootballCompetService::get_compet_match_type($this->compet_id()) == FootballDivision::RETURN_MATCHES;
        $this->days_number = $c_return_matches ? ($teams_number - 1) * 2 : $teams_number - 1;
    }

	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);
		$form->set_layout_title('<div class="align-center small">' . $this->lang['football.days.management'] . '</div>');

        $fieldset = new FormFieldsetHTML('days', $this->lang['football.days.date']);
        $fieldset->set_css_class('days-form');
        $form->add_fieldset($fieldset);
        for ($i = 1; $i <= $this->days_number; $i++)
        {
            $day_date = $this->get_day($i) ? $this->get_day($i)->get_day_date() : new Date();
            $fieldset->add_field(new FormFieldDateTime('day_date_' . $i, $this->lang['football.day'] . ' ' . $i, $day_date,
                array('class' => 'groups-select')
            ));
        }

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function save()
	{
        if (!FootballMatchService::has_matches($this->compet_id()))
        {
            FootballDayService::set_days_matches($this->compet_id());
        }
        else
        {
            FootballMatchService::delete_matches($this->compet_id());
            FootballDayService::set_days_matches($this->compet_id());
        }

        for ($i = 1; $i <= $this->days_number; $i++)
        {
            $day = $this->get_day($i);
            $day->set_day_compet_id($this->compet_id());
            $day->set_day_round($i);
            $day->set_day_date($this->form->get_value('day_date_' . $i));

            if ($day->get_id_day() == null)
            {
                $id = FootballDayService::add_day($day);
                $day->set_id_day($id);
            }
            else {
                FootballDayService::update_day($day, $day->get_id_day());
            }
            FootballMatchService::update_match_date($this->compet_id(), $i, $day->get_day_date()->get_timestamp());
        }

		FootballCompetService::clear_cache();
	}

	private function get_day($day_round)
	{
        $compet_id = $this->compet_id();
        $id = FootballDayService::get_day($compet_id, $day_round) ? FootballDayService::get_day($compet_id, $day_round)->get_id_day() : null;

        if($id !== null)
        {
            try {
                $this->day = FootballDayService::get_day($compet_id, $day_round);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        }
        else
        {
            $this->day = new FootballDay();
        }
		return $this->day;
	}

	private function get_compet()
	{
		$id = AppContext::get_request()->get_getint('compet_id', 0);
		try {
            $this->compet = FootballCompetService::get_compet($id);
        } catch (RowNotFoundException $e) {
            $error_controller = PHPBoostErrors::unexisting_page();
            DispatchManager::redirect($error_controller);
        }
		return $this->compet;
	}

    private function compet_id()
    {
        return $this->get_compet()->get_id_compet();
    }

	private function check_authorizations()
	{
		if (!$this->get_compet()->is_authorized_to_manage_compets())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
        }

		if (AppContext::get_current_user()->is_readonly())
		{
			$controller = PHPBoostErrors::user_in_read_only();
			DispatchManager::redirect($controller);
		}
	}

	protected function get_template_string_content()
	{
		return '
            # INCLUDE MESSAGE_HELPER #
            # INCLUDE MENU #
            # INCLUDE HAS_MATCHES_WARNING #
            # INCLUDE CONTENT #
        ';
	}

	private function generate_response(View $view)
	{
		$compet = $this->get_compet();

		$location_id = $compet->get_id_compet() ? 'football-edit-'. $compet->get_id_compet() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'], FootballUrlBuilder::home());

		if (!AppContext::get_session()->location_id_already_exists($location_id))
            $graphical_environment->set_location_id($location_id);

        $graphical_environment->set_page_title($this->lang['football.days.matches.creation'], $this->lang['football.module.title']);
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['football.days.matches.creation']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::edit_days($compet->get_id_compet()));

        $categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($compet->get_id_category(), true));
        foreach ($categories as $id => $category)
        {
            if ($category->get_id() != Category::ROOT_CATEGORY)
                $breadcrumb->add($category->get_name(), FootballUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
        }
        $category = $compet->get_category();
        $breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::compet_home($compet->get_id_compet()));
        $breadcrumb->add($this->lang['football.days.matches.creation'], FootballUrlBuilder::edit_days($compet->get_id_compet()));

		return $response;
	}
}
?>
