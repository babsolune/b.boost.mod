<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballMatchesFormController extends DefaultModuleController
{
    private $compet;
    private $params;
    private $match;
    private $groups_form;
    private $groups_button;
    private $final_form;
    private $final_button;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_groups_form($request);
		$this->build_final_form($request);

		if ($this->groups_button->has_been_submited() && $this->groups_form->validate())
		{
			$this->save_groups();
			$this->redirect($request);
		}

		if ($this->final_button->has_been_submited() && $this->final_form->validate())
		{
			$this->save_final();
			$this->redirect($request);
		}

		$this->view->put_all(array(
            'MENU' => FootballCompetMenuService::build_compet_menu($this->id_compet()),
            'GROUPS_CONTENT' => $this->groups_form->display(),
            'FINAL_CONTENT' => $this->final_form->display(),
        ));

		return $this->generate_response($this->view);
	}

	private function build_groups_form(HTTPRequestCustom $request)
	{
		$groups_form = new HTMLForm(__CLASS__);
		$groups_form->set_layout_title('<div class="align-center small">' . $this->lang['football.matches.management'] . ' : ' . $this->lang['football.matches.groups.management'] . '</div>');

        if ($this->compet_type() == 'tournament')
        {
            $teams_number = FootballTeamService::get_compet_teams_number($this->id_compet());
            $teams_per_group = $this->get_params()->get_teams_per_group();
            $groups_number = (int)($teams_number / $teams_per_group);
            $matches_number = $teams_per_group * ($teams_per_group - 1) / 2;

            for ($i = 1; $i <= $groups_number; $i++)
            {
                $fieldset = new FormFieldsetHTML('group_' . $i, $this->lang['football.group'] . ' ' . FootballGroupService::ntl($i));
                $fieldset->set_css_class('grouped-selects');
                $groups_form->add_fieldset($fieldset);

                for ($j = 1; $j <= $matches_number; $j++)
                {
                    $match_number = FootballMatchService::get_match_in_group($this->id_compet(), 'G' . $i . $j) ? $this->get_match('G' . $i . $j)->get_match_number() : '';
                    $match_date = FootballMatchService::get_match_in_group($this->id_compet(), 'G' . $i . $j) ? $this->get_match('G' . $i . $j)->get_match_date() : new Date();
                    $match_field = FootballMatchService::get_match_in_group($this->id_compet(), 'G' . $i . $j) ? $this->get_match('G' . $i . $j)->get_match_field() : '';
                    $match_home_team_id = FootballMatchService::get_match_in_group($this->id_compet(), 'G' . $i . $j) ? $this->get_match('G' . $i . $j)->get_match_home_team_id() : 0;
                    $match_visit_team_id = FootballMatchService::get_match_in_group($this->id_compet(), 'G' . $i . $j) ? $this->get_match('G' . $i . $j)->get_match_visit_team_id() : 0;

                    $fieldset->add_field(new FormFieldFree('match_number_' . $i . $j, '', $match_number,
                        array('class' => 'match-select free-select small text-italic')//, 'five_minutes_step' => true
                    ));
                    $fieldset->add_field(new FormFieldDateTime('match_date_' . $i . $j, '', $match_date,
                        array('class' => 'match-select date-select')//, 'five_minutes_step' => true
                    ));
                    $fieldset->add_field(new FormFieldTextEditor('match_field_' . $i . $j, '', $match_field,
                        array('class' => 'match-select playground', 'placeholder' => $this->lang['football.field'])
                    ));
                    $fieldset->add_field(new FormFieldSimpleSelectChoice('home_team_' . $i . $j, '', $match_home_team_id,
                        $this->get_group_teams_list($i),
                        array('class' => 'home-team match-select home-select')
                    ));
                    $fieldset->add_field(new FormFieldSimpleSelectChoice('visit_team_' . $i . $j, '', $match_visit_team_id,
                        $this->get_group_teams_list($i),
                        array('class' => 'visit-team match-select visit-select')
                    ));
                }
            }
        }

		$this->groups_button = new FormButtonDefaultSubmit();
		$groups_form->add_button($this->groups_button);
		$groups_form->add_button(new FormButtonReset());

		$this->groups_form = $groups_form;
	}

	private function save_groups()
	{
        $teams_number = FootballTeamService::get_compet_teams_number($this->id_compet());
        $teams_per_group = $this->get_params()->get_teams_per_group();
        $groups_number = (int)($teams_number / $teams_per_group);
        $matches_number = $teams_per_group * ($teams_per_group - 1) / 2;

        for ($i = 1; $i <= $groups_number; $i++)
        {
            for ($j = 1; $j <= $matches_number; $j++)
            {
                $match = $this->get_match('G' . $i . $j);
                $match->set_match_compet_id($this->id_compet());
                $match->set_match_number('G' . $i . $j);
                $match->set_match_field($this->groups_form->get_value('match_field_' . $i . $j));
                $match->set_match_home_team_id((int)$this->groups_form->get_value('home_team_' . $i . $j)->get_raw_value());
                $match->set_match_visit_team_id((int)$this->groups_form->get_value('visit_team_' . $i . $j)->get_raw_value());
                $match->set_match_date($this->groups_form->get_value('match_date_' . $i . $j));

                if ($match->get_id_match() == null)
                {
                    $id = FootballMatchService::add_match($match);
                    $match->set_id_match($id);
                }
                else {
                    FootballMatchService::update_match($match, $match->get_id_match());
                }
            }
        }

		FootballCompetService::clear_cache();
	}

	private function build_final_form(HTTPRequestCustom $request)
	{
		$final_form = new HTMLForm(__CLASS__);
		$final_form->set_layout_title('<div class="align-center small">' . $this->lang['football.matches.management'] . ' : ' . $this->lang['football.matches.final.management'] . '</div>');

        // if ($this->compet_type() == 'tournament')
        // {
        //     $teams_number = FootballTeamService::get_compet_teams_number($this->id_compet());
        //     $teams_per_group = $this->get_params()->get_teams_per_group();
        //     $groups_number = (int)($teams_number / $teams_per_group);
        //     $matches_number = $teams_per_group * ($teams_per_group - 1) / 2;
        //     $i = 1;
        //     for ($i; $i <= $groups_number; $i++)
        //     {
        //         $fieldset = new FormFieldsetHTML('compet_' . $i, 'Groupe ' . $i);
        //         $fieldset->set_css_class('grouped-selects');
        //         $final_form->add_fieldset($fieldset);
        //         $j = 1;
        //         for($j; $j <= $matches_number; $j++)
        //         {
        //             $team_id = FootballTeamService::get_team_in_group($this->id_compet(), $i . $j) ? FootballTeamService::get_team_in_group($this->id_compet(), $i . $j)->get_id_team() : '';
        //             $fieldset->add_field(new FormFieldDateTime('match_time_' . $i . $j, '', new Date(),
        //                 array('class' => 'match-select', 'five_minutes_step' => true)
        //             ));
        //             $fieldset->add_field(new FormFieldSimpleSelectChoice('home_team_' . $i . $j, '', $team_id,
        //                 $this->get_group_teams_list($i),
        //                 array('class' => 'home-team match-select')
        //             ));
        //             $fieldset->add_field(new FormFieldSimpleSelectChoice('visit_team_' . $i . $j, '', $team_id,
        //                 $this->get_group_teams_list($i),
        //                 array('class' => 'visit-team match-select')
        //             ));
        //         }
        //     }
        // }

		$this->final_button = new FormButtonDefaultSubmit();
		$final_form->add_button($this->final_button);
		$final_form->add_button(new FormButtonReset());

		$this->final_form = $final_form;
	}

	private function save_final()
	{
        // $teams_number = FootballTeamService::get_compet_teams_number($this->id_compet());
        // $teams_per_group = $this->get_params()->get_teams_per_group();
        // $groups_number = (int)($teams_number / $teams_per_group);
        // // unselect all teams to manage changes
        // foreach(FootballTeamService::get_teams($this->id_compet()) as $team)
        // {
        //     FootballTeamService::update_team_group($team['id_team'], 0);
        // }
        // $i = 1;
        // for ($i; $i <= $groups_number; $i++)
        // {
        //     $j = 1;
        //     for($j; $j <= $teams_per_group; $j++)
        //     {
        //         $id = $this->groups_form->get_value('group_teams_' . $i . $j)->get_raw_value();
        //         FootballTeamService::update_team_group($id, $i.$j);
        //     }
        // }

		// FootballCompetService::clear_cache();
	}

	private function get_match($group)
	{
        $compet_id = AppContext::get_request()->get_getint('id', 0);
        $id = FootballMatchService::get_match_in_group($compet_id, $group) ? FootballMatchService::get_match_in_group($compet_id, $group)->get_id_match() : null;

        if($id !== null)
            try {
                $this->match = FootballMatchService::get_match($id, $group);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        else
        {
            $this->match = new FootballMatch();
        }
		return $this->match;
	}

    private function id_compet()
    {
        return $this->get_compet()->get_id_compet();
    }

    private function compet_type()
    {
        $division = FootballDivisionCache::load()->get_division($this->get_compet()->get_compet_division_id());
        return $division['division_compet_type'];
    }

    private function get_group_teams_list($group)
    {
        $teams_list = [];
        foreach (FootballTeamService::get_teams($this->id_compet()) as $team)
        {
            $team_group = $team['team_group'];
            $team_group = $team_group ? TextHelper::substr($team_group, 0, 1) : '';
            if ($team_group == $group)
                $teams_list[] = $team;
        }
        $options = array();

        $options[] = new FormFieldSelectChoiceOption('', 0);
		foreach($teams_list as $team)
		{
			$options[] = new FormFieldSelectChoiceOption($team['team_club_name'], $team['id_team']);
		}

		return $options;
    }

    private function get_params()
	{
        $id = AppContext::get_request()->get_getint('id', 0);
        if (!empty($id))
        {
            try {
                $this->params = FootballParamsService::get_params($id);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        }
		return $this->params;
	}

	private function get_compet()
	{
		$id = AppContext::get_request()->get_getint('id', 0);
		try {
            $this->compet = FootballCompetService::get_compet($id);
        } catch (RowNotFoundException $e) {
            $error_controller = PHPBoostErrors::unexisting_page();
            DispatchManager::redirect($error_controller);
        }
		return $this->compet;
	}

    private function get_team_ids()
    {
        $teams = FootballTeamService::get_teams($this->id_compet());
        $team_ids = [];
        foreach($teams as $id => $team_id)
        {
            $team_ids[] = $team_id['team_club_id'];
        }
        return $team_ids;
    }

	private function redirect()
	{
		AppContext::get_response()->redirect(FootballUrlBuilder::matches($this->id_compet()));
	}

	protected function get_template_string_content()
	{
		return '
            # INCLUDE MESSAGE_HELPER #
            # INCLUDE MENU #
            # INCLUDE GROUPS_CONTENT #
            # INCLUDE FINAL_CONTENT #
        ';
	}

	private function check_authorizations()
	{
		// $compet = $this->get_compet();

		// if ($compet->get_id_compet() === null)
		// {
		// 	if (!$compet->is_authorized_to_manage())
		// 	{
		// 		$error_controller = PHPBoostErrors::user_not_authorized();
		// 		DispatchManager::redirect($error_controller);
		// 	}
		// }
		// else
		// {
		// 	if (!$compet->is_authorized_to_manage())
		// 	{
		// 		$error_controller = PHPBoostErrors::user_not_authorized();
		// 		DispatchManager::redirect($error_controller);
		// 	}
		// }
		// if (AppContext::get_current_user()->is_readonly())
		// {
		// 	$controller = PHPBoostErrors::user_in_read_only();
		// 	DispatchManager::redirect($controller);
		// }
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

        $graphical_environment->set_page_title($this->lang['football.matches.management'], $this->lang['football.module.title']);
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['football.matches.management']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::matches($compet->get_id_compet()));

        $categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($compet->get_id_category(), true));
        foreach ($categories as $id => $category)
        {
            if ($category->get_id() != Category::ROOT_CATEGORY)
                $breadcrumb->add($category->get_name(), RecipeUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
        }
        $category = $compet->get_category();
        $breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $compet->get_id_compet(), $compet->get_compet_slug()));
        $breadcrumb->add($this->lang['football.matches.management'], FootballUrlBuilder::matches($compet->get_id_compet()));

		return $response;
	}
}
?>
