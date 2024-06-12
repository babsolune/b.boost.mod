<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballTeamsFormController extends DefaultModuleController
{
    private $compet;
    private $team;
    private $compet_type;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
            $compet_name = $this->get_compet()->get_compet_name();
            $teams_number = FootballTeamService::get_teams_number($this->compet_id());
            $compet_type = FootballCompetService::get_compet_type($this->compet_id());
            if ($compet_type == FootballDivision::TOURNAMENT || $compet_type == FootballDivision::CUP)
            {
                if (is_numeric($teams_number) && $teams_number % 2 == 0)
                    $this->view->put('MESSAGE_HELPER', MessageHelper::display(StringVars::replace_vars($this->lang['football.warning.add.teams'], array('teams_number' => $teams_number, 'compet_name' => $compet_name)), MessageHelper::SUCCESS, 4));
                else
                    $this->view->put('MESSAGE_HELPER', MessageHelper::display(StringVars::replace_vars($this->lang['football.warning.add.teams.odd'], array('teams_number' => $teams_number)), MessageHelper::WARNING, 10));
            }
            else
                $this->view->put('MESSAGE_HELPER', MessageHelper::display(StringVars::replace_vars($this->lang['football.warning.add.teams'], array('teams_number' => $teams_number, 'compet_name' => $compet_name)), MessageHelper::SUCCESS, 4));
        }

        $this->view->put_all(array(
            'MENU' => FootballMenuService::build_compet_menu($this->compet_id()),
            'CONTENT' => $this->form->display(),
            'HAS_MATCHES_WARNING' => FootballMatchService::has_matches($this->compet_id()) ? MessageHelper::display($this->lang['football.warning.teams.has.matches'], MessageHelper::NOTICE) : MessageHelper::display('', '')
        ));

		return $this->generate_response($this->view);
	}

	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);
        $form->set_css_class('teams-checkbox floating-submit');
		$form->set_layout_title('<div class="align-center small">' . $this->lang['football.associate.clubs'] . '</div>');

		$fieldset = new FormFieldsetHTML('compet', '');
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldMultipleCheckbox('teams', '', $this->get_teams_list(), $this->get_clubs_list()));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);

		$this->form = $form;
	}

	private function save()
	{
        $team_list = [];
        // Add clubs in teams list
        foreach($this->form->get_value('teams') as $key => $options)
        {
            $team = new FootballTeam;
            $team_list[] = $options->get_id();

            if (!in_array($options->get_id(), $this->get_team_ids()))
            {
                $team->set_team_compet_id($this->compet_id());
                $team->set_team_club_id($options->get_id());
                $team->set_team_club_name($options->get_label());
                $id = FootballTeamService::add_team($team);
                $team->set_id_team($id);
            }
        }

        // Delete team if it's unchecked
        foreach(array_diff($this->get_team_ids(), $team_list) as $club_id)
        {
            FootballTeamService::delete_team($this->compet_id(), $club_id);
        }

        FootballCompetService::clear_cache();
	}

    private function get_clubs_list()
    {
        $options = array();
		$cache = FootballClubCache::load();
		$clubs_list = $cache->get_clubs();

		$i = 1;
		foreach($clubs_list as $club)
		{
			$options[] = new FormFieldMultipleCheckboxOption($club['id_club'], ($club['club_name'] ? $club['club_name'] : $club['club_place']));
			$i++;
		}

		return $options;
    }

    private function get_teams_list()
    {
        $teams = array();
        foreach(FootballTeamService::get_teams($this->compet_id()) as $team)
        {
            $teams[] = $team['team_club_id'];
        }
		return $teams;
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

    private function get_team_ids()
    {
        $teams = FootballTeamService::get_teams($this->compet_id());
        $team_ids = [];
        foreach($teams as $id => $team_id)
        {
            $team_ids[] = $team_id['team_club_id'];
        }
        return $team_ids;
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

        $graphical_environment->set_page_title($this->lang['football.teams.management'], $this->lang['football.module.title']);
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['football.teams.management']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::edit_teams($this->compet_id()));

        $categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($compet->get_id_category(), true));
        foreach ($categories as $id => $category)
        {
            if ($category->get_id() != Category::ROOT_CATEGORY)
                $breadcrumb->add($category->get_name(), RecipeUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
        }
        $category = $compet->get_category();
        $breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::compet_home($this->compet_id()));
        $breadcrumb->add($this->lang['football.teams.management'], FootballUrlBuilder::edit_teams($this->compet_id()));

		return $response;
	}
}
?>
