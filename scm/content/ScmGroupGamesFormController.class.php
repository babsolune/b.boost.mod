<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmGroupGamesFormController extends DefaultModuleController
{
    private $event;
    private $params;
    private $hat_ranking;
    private $game;
    private $teams_number;
    private $teams_per_group;
    private $return_games;

	public function execute(HTTPRequestCustom $request)
	{
        $this->init();
		$this->check_authorizations();

        $this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
            $this->view->put('MESSAGE_HELPER', MessageHelper::display($this->lang['scm.warning.games.update'], MessageHelper::SUCCESS, 4));
		}

		$this->view->put_all([
            'MENU' => ScmMenuService::build_event_menu($this->event_id()),
            'CONTENT' => $this->form->display(),
        ]);

		return $this->generate_response($this->view);
	}

    private function init()
    {
        $this->hat_ranking     = $this->get_params()->get_hat_ranking();
        $this->teams_number    = ScmTeamService::get_teams_number($this->event_id());
        $this->teams_per_group = $this->get_params()->get_teams_per_group();
        $this->return_games    = ScmEventService::get_event_game_type($this->event_id()) == ScmDivision::RETURN_GAMES;
    }

	private function build_form()
	{
        $i = AppContext::get_request()->get_getint('round', 0);
		$form = new HTMLForm(__CLASS__);
        $form->set_css_class('floating-submit');
		$form->set_layout_title('<div class="align-center small">' . $this->lang['scm.games.management'] . '</div>');

		$groups_fieldset = new FormFieldsetHTML('groups_bracket', $this->lang['scm.games.groups.stage']);
        $form->add_fieldset($groups_fieldset);

        $odd_filled = $this->teams_number % 2 != 0 && $this->get_params()->get_fill_games();

        if ($this->hat_ranking)
        {
            $fieldset = new FormFieldsetHTML('group_' . $i, $this->lang['scm.day'] . ' ' . $i);
            $fieldset->set_css_class('grouped-fields');
            $form->add_fieldset($fieldset);
            for ($j = 1; $j <= ($this->teams_number / 2); $j++)
            {
                $groups_fieldset = new FormFieldsetHTML('round_' . $i, '');
                $groups_fieldset->set_css_class('grouped-fields round-fields');
                $form->add_fieldset($groups_fieldset);
                $game_number = '<strong>G' . $i . $j . '</strong>';
                $game_date = $this->get_game('G', $i, $j) ? $this->get_game('G', $i, $j)->get_game_date() : $this->get_event()->get_start_date();
                $game_playground = $this->get_game('G', $i, $j) ? $this->get_game('G', $i, $j)->get_game_playground() : '';
                $game_home_id = $this->get_game('G', $i, $j) ? $this->get_game('G', $i, $j)->get_game_home_id() : 0;
                $game_home_score = $this->get_game('G', $i, $j) ? $this->get_game('G', $i, $j)->get_game_home_score() : '';
                $game_away_score = $this->get_game('G', $i, $j) ? $this->get_game('G', $i, $j)->get_game_away_score() : '';
                $game_away_id = $this->get_game('G', $i, $j) ? $this->get_game('G', $i, $j)->get_game_away_id() : 0;

                $groups_fieldset->add_field(new FormFieldFree('group_game_number_' . $i . $j, '', $game_number,
                    ['class' => 'game-name small text-italic form-G' . $i . $j]
                ));
                $groups_fieldset->add_field(new FormFieldDateTime('group_game_date_' . $i . $j, '', $game_date,
                    ['class' => 'game-date date-select']
                ));
                if($this->get_params()->get_display_playgrounds())
                    $groups_fieldset->add_field(new FormFieldTextEditor('group_game_playground_' . $i . $j, '', $game_playground,
                        ['class' => 'game-playground', 'placeholder' => $this->lang['scm.field']]
                    ));
                $groups_fieldset->add_field(new FormFieldSimpleSelectChoice('group_home_team_' . $i . $j, '', $game_home_id,
                    $this->get_teams_list(),
                    ['class' => 'home-team game-team']
                ));
                $groups_fieldset->add_field(new FormFieldTextEditor('group_home_score_' . $i . $j, '', $game_home_score,
                    ['class' => 'home-team game-score', 'pattern' => '[0-9]*']
                ));
                $groups_fieldset->add_field(new FormFieldTextEditor('group_away_score_' . $i . $j, '', $game_away_score,
                    ['class' => 'away-team game-score', 'pattern' => '[0-9]*']
                ));
                $groups_fieldset->add_field(new FormFieldSimpleSelectChoice('group_away_team_' . $i . $j, '', $game_away_id,
                    $this->get_teams_list(),
                    ['class' => 'away-team game-team']
                ));
            }
        }
        else
        {
            if ($this->return_games)
                $games_number = $this->teams_per_group * ($this->teams_per_group - 1);
            else
                $games_number = $this->teams_per_group * ($this->teams_per_group - 1) / 2;

            $fieldset = new FormFieldsetHTML('group_' . $i, $this->lang['scm.group'] . ' ' . ScmGroupService::ntl($i));
            $fieldset->set_css_class('grouped-fields');
            $form->add_fieldset($fieldset);

            for ($j = 1; $j <= $games_number; $j++)
            {
                $c_one_day = ScmGameService::one_day_event($this->event_id());
                $round_title = $c_one_day ? $this->lang['scm.round'] : $this->lang['scm.day'];
                $groups_fieldset = new FormFieldsetHTML('round_' . $i, '');
                $groups_fieldset->set_css_class('grouped-fields round-fields');
                $form->add_fieldset($groups_fieldset);
                $game_number = '<strong>G' . $i . $j . '</strong>'. ' - ' . $round_title . ' ' . $this->get_game('G', $i, $j)->get_game_round();
                $game_date = $this->get_game('G', $i, $j) ? $this->get_game('G', $i, $j)->get_game_date() : $this->get_event()->get_start_date();
                $game_playground = $this->get_game('G', $i, $j) ? $this->get_game('G', $i, $j)->get_game_playground() : '';
                $game_home_id = $this->get_game('G', $i, $j) ? $this->get_game('G', $i, $j)->get_game_home_id() : 0;
                $game_home_score = $this->get_game('G', $i, $j) ? $this->get_game('G', $i, $j)->get_game_home_score() : '';
                $game_away_score = $this->get_game('G', $i, $j) ? $this->get_game('G', $i, $j)->get_game_away_score() : '';
                $game_away_id = $this->get_game('G', $i, $j) ? $this->get_game('G', $i, $j)->get_game_away_id() : 0;

                if ($this->return_games && $j == 1)
                    $groups_fieldset->add_field(new FormFieldSpacer('group_first_leg_' . $i, $this->lang['scm.first.leg']));
                $groups_fieldset->add_field(new FormFieldFree('group_game_number_' . $i . $j, '', $game_number,
                    ['class' => 'game-name small text-italic align-right form-G' . $i . $j]
                ));
                $groups_fieldset->add_field(new FormFieldDateTime('group_game_date_' . $i . $j, '', $game_date,
                    ['class' => 'game-date']
                ));
                if($this->get_params()->get_display_playgrounds())
                    $groups_fieldset->add_field(new FormFieldTextEditor('group_game_playground_' . $i . $j, '', $game_playground,
                        ['class' => 'game-playground', 'placeholder' => $this->lang['scm.field']]
                    ));
                $groups_fieldset->add_field(new FormFieldSimpleSelectChoice('group_home_team_' . $i . $j, '', $game_home_id,
                    $odd_filled && $game_home_id == 0 ? [] : $this->get_group_teams_list($i),
                    ['class' => 'home-team game-team']
                ));
                $groups_fieldset->add_field(new FormFieldTextEditor('group_home_score_' . $i . $j, '', $game_home_score,
                    ['class' => 'home-team game-score', 'pattern' => '[0-9]*']
                ));
                $groups_fieldset->add_field(new FormFieldTextEditor('group_away_score_' . $i . $j, '', $game_away_score,
                    ['class' => 'away-team game-score', 'pattern' => '[0-9]*']
                ));
                $groups_fieldset->add_field(new FormFieldSimpleSelectChoice('group_away_team_' . $i . $j, '', $game_away_id,
                    $odd_filled && $game_away_id == 0 ? [] : $this->get_group_teams_list($i),
                    ['class' => 'away-team game-team']
                ));
                if ($this->return_games && $j == $games_number / 2)
                    $groups_fieldset->add_field(new FormFieldSpacer('group_second_leg_' . $i, '<hr />' . $this->lang['scm.second.leg']));
            }
        }

        $this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);

		$this->form = $form;
	}

	private function save()
	{
        $i = AppContext::get_request()->get_getint('round', 0);
        if ($this->hat_ranking)
        {
            for ($j = 1; $j <= ($this->teams_number / 2); $j++)
            {
                $game = $this->get_game('G', $i, $j);
                $game->set_game_event_id($this->event_id());
                $game->set_game_type('G');
                $game->set_game_group($i);
                $game->set_game_order($j);
                $game->set_game_date($this->form->get_value('group_game_date_' . $i . $j));
                if($this->get_params()->get_display_playgrounds())
                    $game->set_game_playground($this->form->get_value('group_game_playground_' . $i . $j));
                $game->set_game_home_id((int)$this->form->get_value('group_home_team_' . $i . $j)->get_raw_value());
                $game->set_game_home_score($this->form->get_value('group_home_score_' . $i . $j));
                $game->set_game_away_score($this->form->get_value('group_away_score_' . $i . $j));
                $game->set_game_away_id((int)$this->form->get_value('group_away_team_' . $i . $j)->get_raw_value());

                if ($game->get_id_game() == null)
                {
                    $id = ScmGameService::add_game($game);
                    $game->set_id_game($id);
                }
                else {
                    ScmGameService::update_game($game, $game->get_id_game());
                }
            }
        }
        else
        {
            if ($this->return_games)
                $games_number = $this->teams_per_group * ($this->teams_per_group - 1);
            else
                $games_number = $this->teams_per_group * ($this->teams_per_group - 1) / 2;

            for ($j = 1; $j <= $games_number; $j++)
            {
                $game = $this->get_game('G', $i, $j);
                $game->set_game_event_id($this->event_id());
                $game->set_game_type('G');
                $game->set_game_group($i);
                $game->set_game_order($j);
                $game->set_game_date($this->form->get_value('group_game_date_' . $i . $j));
                if($this->get_params()->get_display_playgrounds())
                    $game->set_game_playground($this->form->get_value('group_game_playground_' . $i . $j));
                $game->set_game_home_id((int)$this->form->get_value('group_home_team_' . $i . $j)->get_raw_value());
                $game->set_game_home_score($this->form->get_value('group_home_score_' . $i . $j));
                $game->set_game_away_score($this->form->get_value('group_away_score_' . $i . $j));
                $game->set_game_away_id((int)$this->form->get_value('group_away_team_' . $i . $j)->get_raw_value());

                if ($game->get_id_game() == null)
                {
                    $id = ScmGameService::add_game($game);
                    $game->set_id_game($id);
                }
                else {
                    ScmGameService::update_game($game, $game->get_id_game());
                }
            }
        }

		ScmEventService::clear_cache();
	}

	private function get_game($type, $group, $order)
	{
        $event_id = $this->event_id();
        $id = ScmGameService::get_game($event_id, $type, $group, $order) ? ScmGameService::get_game($event_id, $type, $group, $order)->get_id_game() : null;

        if($id !== null)
        {
            try {
                $this->game = ScmGameService::get_game($event_id, $type, $group, $order);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        }
        else
        {
            $this->game = new ScmGame();
        }
		return $this->game;
	}

	private function get_event()
	{
		$id = AppContext::get_request()->get_getint('event_id', 0);
		try {
            $this->event = ScmEventService::get_event($id);
        } catch (RowNotFoundException $e) {
            $error_controller = PHPBoostErrors::unexisting_page();
            DispatchManager::redirect($error_controller);
        }
		return $this->event;
	}

    private function event_id()
    {
        return $this->get_event()->get_id();
    }

    private function get_group_teams_list($group)
    {
        $teams_list = [];
        foreach (ScmTeamService::get_teams($this->event_id()) as $team)
        {
            $team_group = $team['team_group'];
            $team_group = $team_group ? TextHelper::substr($team_group, 0, 1) : '';
            if ($team_group == $group)
                $teams_list[] = $team;
        }
        $options = [];

        $clubs = ScmClubCache::load();
        $options[] = new FormFieldSelectChoiceOption('', 0);
		foreach($teams_list as $team)
		{
			$options[] = new FormFieldSelectChoiceOption($clubs->get_club_name($team['team_club_id']), $team['id_team']);
		}

		return $options;
    }

    private function get_teams_list()
    {
        $options = [];

        $clubs = ScmClubCache::load();
        $options[] = new FormFieldSelectChoiceOption('', '');
		foreach(ScmTeamService::get_teams($this->event_id()) as $team)
		{
			$options[] = new FormFieldSelectChoiceOption($clubs->get_club_name($team['team_club_id']), $team['id_team']);
		}

		return $options;
    }

    private function get_params()
	{
        $id = AppContext::get_request()->get_getint('event_id', 0);
        if (!empty($id))
        {
            try {
                $this->params = ScmParamsService::get_params($id);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        }
		return $this->params;
	}

	protected function get_template_string_content()
	{
		return '
            # INCLUDE MESSAGE_HELPER #
            # INCLUDE MENU #
            # INCLUDE CONTENT #
        ';
	}

	private function check_authorizations()
	{
		if (!$this->get_event()->is_authorized_to_manage_events())
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

	private function generate_response(View $view)
	{
		$event = $this->get_event();

		// $location_id = $event->get_id() ? 'scm-edit-'. $event->get_id() : '';

		// $response = new SiteDisplayResponse($view, $location_id);
		$response = new SiteDisplayResponse($view);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());

		// if (!AppContext::get_session()->location_id_already_exists($location_id))
        //     $graphical_environment->set_location_id($location_id);

        $graphical_environment->set_page_title($this->lang['scm.games.management'], $this->lang['scm.module.title']);
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.games.management']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit_groups_games($event->get_id(), $event->get_event_slug()));

        $categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($event->get_id_category(), true));
        foreach ($categories as $id => $category)
        {
            if ($category->get_id() != Category::ROOT_CATEGORY)
                $breadcrumb->add($category->get_name(), ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
        }
        $category = $event->get_category();
        $breadcrumb->add($event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));
        $breadcrumb->add($this->lang['scm.games.management'], ScmUrlBuilder::edit_groups_games($event->get_id(), $event->get_event_slug()));

		return $response;
	}
}
?>
