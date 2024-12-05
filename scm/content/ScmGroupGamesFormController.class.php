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
    private $teams_number;
    private $return_games;

	public function execute(HTTPRequestCustom $request)
	{
        $this->init();
		$this->check_authorizations();

        $this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
            AppContext::get_response()->redirect(ScmUrlBuilder::edit_groups_games($this->get_event()->get_id(), $this->get_event()->get_event_slug(), AppContext::get_request()->get_getint('round', 0)));
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
        $this->return_games    = ScmEventService::get_event_game_type($this->event_id()) == ScmDivision::RETURN_GAMES;
    }

	private function build_form()
	{
        $gr = AppContext::get_request()->get_getint('round', 0);
        $form = new HTMLForm(__CLASS__);
        $form->set_css_class('floating-submit modal-container');
		$form->set_layout_title(
            '<div class="align-center small">' . $this->lang['scm.games.management'] . '</div>'
            . '<div class="align-center smaller">' . $this->lang['scm.games.groups.stage'] . ' - ' . ($this->hat_ranking ? $this->lang['scm.day'] . ' ' . $gr : $this->lang['scm.group'] . ' ' . ScmGroupService::ntl($gr)) . '</div>'
        );

        if ($this->hat_ranking)
        {
            $games_number = count($this->get_hat_games($gr));
            foreach ($this->get_hat_games($gr) as $group_game)
            {
                $game = new ScmGame();
                $game->set_properties($group_game);
                $or = $game->get_game_order();
                $groups_fieldset = new FormFieldsetHTML('round_' . $gr . $or, '');
                $groups_fieldset->set_css_class('grouped-fields round-fields');
                $form->add_fieldset($groups_fieldset);
                $bonus = $this->get_params()->get_bonus() &&
                    ($game->get_game_home_off_bonus() ||
                    $game->get_game_home_def_bonus() ||
                    $game->get_game_away_off_bonus() ||
                    $game->get_game_away_def_bonus())
                        ? ' ' . $this->lang['scm.bonus.param'] : '';

                switch ($game->get_game_status()) {
                    case ScmGame::DELAYED :
                        $status = ' ' . $this->lang['scm.game.event.status.delayed'];
                        break;
                    case ScmGame::STOPPED :
                        $status = ' ' . $this->lang['scm.game.event.status.stopped'];
                        break;
                    case '' :
                        $status = '';
                        break;
                }

                $groups_fieldset->add_field(new FormFieldSpacer('separator_' . $gr . $or, '<hr />'));
                $groups_fieldset->add_field(new FormFieldFree('game_number_' . $gr . $or, '', '<strong>G' . $gr . $or . '</strong><span class="warning">' . $bonus . $status . '</span>     ',
                    ['class' => 'game-name small text-italic form-G' . $gr . $or]
                ));

                $groups_fieldset->add_field(new FormFieldActionLink('details_' . $gr . $or, '<span aria-label="' . $this->lang['scm.game.event.details'] . '"><i class="far fa-square-plus" aria-hidden="true"></i></span>' , ScmUrlBuilder::edit_details_game($this->event_id(), $this->get_event()->get_event_slug(), 'G', $gr, 0, $or), 'd-inline-block game-details align-right'));

                $groups_fieldset->add_field(new FormFieldDateTime('game_date_' . $gr . $or, '', $game->get_game_date(),
                    ['class' => 'game-date date-select']
                ));
                $groups_fieldset->add_field(new FormFieldSimpleSelectChoice('home_team_' . $gr . $or, '', $game->get_game_home_id(),
                    $this->get_teams_list(),
                    ['class' => 'home-team game-team']
                ));
                $groups_fieldset->add_field(new FormFieldNumberEditor('home_score_' . $gr . $or, '', $game->get_game_home_score(),
                    ['class' => 'home-team game-score', 'pattern' => '[0-9]*']
                ));
                $groups_fieldset->add_field(new FormFieldNumberEditor('away_score_' . $gr . $or, '', $game->get_game_away_score(),
                    ['class' => 'away-team game-score', 'pattern' => '[0-9]*']
                ));
                $groups_fieldset->add_field(new FormFieldSimpleSelectChoice('away_team_' . $gr . $or, '', $game->get_game_away_id(),
                    $this->get_teams_list(),
                    ['class' => 'away-team game-team']
                ));
                if($this->get_params()->get_display_playgrounds())
                    $groups_fieldset->add_field(new FormFieldTextEditor('game_playground_' . $gr . $or, '', $game->get_game_playground(),
                        ['class' => 'game-playground', 'placeholder' => $this->lang['scm.field']]
                    ));
            }
        }
        else
        {
            // Empty list of teams if odd number
            $odd_filled = $this->teams_number % 2 != 0 && $this->get_params()->get_fill_games();

            $c_one_day = ScmGameService::one_day_event($this->event_id());
            $round_title = $c_one_day ? $this->lang['scm.round'] : $this->lang['scm.day'];
            $total_games = [];
            foreach($this->get_group_games($gr) as $round => $games)
            {
                foreach ($games as $group_game)
                {
                    $total_games[] = $group_game;
                }
            }
            $games_number = count($total_games);

            foreach($this->get_group_games($gr) as $round => $games)
            {
                foreach ($games as $group_game)
                {
                    $game = new ScmGame();
                    $game->set_properties($group_game);
                    $or = $game->get_game_order();
                    ${'groups_fieldset' . $or} = new FormFieldsetHTML('round_' . $or, '');
                    ${'groups_fieldset' . $or}->set_css_class('grouped-fields round-fields');
                    $form->add_fieldset(${'groups_fieldset' . $or});

                    ${'groups_fieldset' . $or}->add_field(new FormFieldSpacer('separator_' . $gr, '<hr />'));
                    if ($this->return_games && $or == 1)
                        ${'groups_fieldset' . $or}->add_field(new FormFieldSpacer('first_leg_' . $gr, $this->lang['scm.first.leg']));
                    ${'groups_fieldset' . $or}->add_field(new FormFieldFree('game_number_' . $gr . $or, '', '<strong>G' . $gr . $or . '</strong>'. ' - ' . $round_title . ' ' . $round,
                        ['class' => 'game-name small text-italic form-G' . $gr . $or]
                    ));
                    ${'groups_fieldset' . $or}->add_field(new FormFieldActionLink('details_' . $gr . $or, '<span aria-label="' . $this->lang['scm.game.event.details'] . '"><i class="far fa-square-plus" aria-hidden="true"></i></span>' , ScmUrlBuilder::edit_details_game($this->event_id(), $this->get_event()->get_event_slug(), 'G', $gr, $round, $or), 'd-inline-block game-details align-right'));

                    ${'groups_fieldset' . $or}->add_field(new FormFieldDateTime('game_date_' . $gr . $or, '', $game->get_game_date(),
                        ['class' => 'game-date']
                    ));
                    ${'groups_fieldset' . $or}->add_field(new FormFieldSimpleSelectChoice('home_team_' . $gr . $or, '', $game->get_game_home_id(),
                        $odd_filled && $game->get_game_home_id() == 0 ? [] : $this->get_group_teams_list($gr),
                        ['class' => 'home-team game-team']
                    ));
                    ${'groups_fieldset' . $or}->add_field(new FormFieldNumberEditor('home_score_' . $gr . $or, '', $game->get_game_home_score(),
                        ['class' => 'home-team game-score', 'pattern' => '[0-9]*']
                    ));
                    ${'groups_fieldset' . $or}->add_field(new FormFieldNumberEditor('away_score_' . $gr . $or, '', $game->get_game_away_score(),
                        ['class' => 'away-team game-score', 'pattern' => '[0-9]*']
                    ));
                    ${'groups_fieldset' . $or}->add_field(new FormFieldSimpleSelectChoice('away_team_' . $gr . $or, '', $game->get_game_away_id(),
                        $odd_filled && $game->get_game_away_id() == 0 ? [] : $this->get_group_teams_list($gr),
                        ['class' => 'away-team game-team']
                    ));
                    if($this->get_params()->get_display_playgrounds())
                        ${'groups_fieldset' . $or}->add_field(new FormFieldTextEditor('game_playground_' . $gr . $or, '', $game->get_game_playground(),
                            ['class' => 'game-playground', 'placeholder' => $this->lang['scm.field']]
                        ));

                    if ($this->return_games && $or == $games_number / 2)
                        ${'groups_fieldset' . $or}->add_field(new FormFieldSpacer('second_leg_' . $gr, '<hr />' . $this->lang['scm.second.leg']));
                }
            }
        }

        $this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);

		$this->form = $form;
	}

    private function get_stadium(int $club_id)
    {
        $team = ScmTeamService::get_team($club_id);
        $club = ScmClubCache::load()->get_club($team->get_team_club_id());
        $real_id = $club['club_affiliate'] ? $club['club_affiliation'] : $club['id_club'];
        $real_club = new ScmClub();
        $real_club->set_properties(ScmClubCache::load()->get_club($real_id));

        $options = [];
        $options[] = new FormFieldSelectChoiceOption('', 0);
        $stadiums = 0;
		$i = 1;
		foreach(TextHelper::deserialize($real_club->get_club_locations()) as $club)
		{
            if ($club['name'])
                $stadiums++;
            $options[] = new FormFieldSelectChoiceOption($club['name'], $i);
			$i++;
		}

        return $stadiums ? $options : [new FormFieldSelectChoiceOption(StringVars::replace_vars($this->lang['scm.club.no.stadium'], ['club' => $real_club->get_club_name()]), 0)];
    }

	private function save()
	{
        $gr = AppContext::get_request()->get_getint('round', 0);
        if ($this->hat_ranking)
        {
            $games = ScmGroupService::games_list_from_group($this->event_id(), 'G', $gr);

            foreach($games as $hat_game)
            {
                $game = new ScmGame();
                $game->set_properties($hat_game);
                $or = $game->get_game_order();
                $game->set_game_event_id($this->event_id());
                $game->set_game_date($this->form->get_value('game_date_' . $gr . $or));
                if($this->get_params()->get_display_playgrounds())
                    $game->set_game_playground($this->form->get_value('game_playground_' . $gr . $or));
                $game->set_game_home_id((int)$this->form->get_value('home_team_' . $gr . $or)->get_raw_value());
                $game->set_game_home_score($this->form->get_value('home_score_' . $gr . $or));
                $game->set_game_away_score($this->form->get_value('away_score_' . $gr . $or));
                $game->set_game_away_id((int)$this->form->get_value('away_team_' . $gr . $or)->get_raw_value());

                ScmGameService::update_game($game, $game->get_id_game());
            }
        }
        else
        {
            foreach($this->get_group_games($gr) as $games)
            {
                foreach ($games as $group_game)
                {
                    $game = new ScmGame();
                    $game->set_properties($group_game);
                    $or = $game->get_game_order();
                    $game->set_game_event_id($this->event_id());
                    $game->set_game_date($this->form->get_value('game_date_' . $gr . $or));
                    if($this->get_params()->get_display_playgrounds())
                        $game->set_game_playground($this->form->get_value('game_playground_' . $gr . $or));
                    $game->set_game_home_id((int)$this->form->get_value('home_team_' . $gr . $or)->get_raw_value());
                    $game->set_game_home_score($this->form->get_value('home_score_' . $gr . $or));
                    $game->set_game_away_score($this->form->get_value('away_score_' . $gr . $or));
                    $game->set_game_away_id((int)$this->form->get_value('away_team_' . $gr . $or)->get_raw_value());

                    ScmGameService::update_game($game, $game->get_id_game());
                }
            }
        }

		ScmEventService::clear_cache();
	}

    private function get_hat_games($gr)
    {
        $games = ScmGroupService::games_list_from_group($this->event_id(), 'G', $gr);

        usort($games, function($a, $b) {
            return $a['game_order'] - $b['game_order'];
        });

        return $games;
    }

    private function get_group_games($gr)
    {
        $games = ScmGroupService::games_list_from_group($this->event_id(), 'G', $gr);

        usort($games, function($a, $b) {
            if ($a['game_round'] == $b['game_round']) {
                return $a['game_order'] - $b['game_order'];
            } else {
                return $a['game_round'] - $b['game_round'];
            }
        });

        $rounds = [];
        foreach($games as $game)
        {
            $rounds[$game['game_round']][] = $game;
        }

        return $rounds;
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
        $graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit_groups_games($event->get_id(), $event->get_event_slug(), AppContext::get_request()->get_getint('round', 0)));

        $categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($event->get_id_category(), true));
        foreach ($categories as $id => $category)
        {
            if ($category->get_id() != Category::ROOT_CATEGORY)
                $breadcrumb->add($category->get_name(), ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
        }
        if ($event->get_is_sub())
            $breadcrumb->add(ScmEventService::get_master_name($event->get_id()), ScmEventService::get_master_url($event->get_id()));
		$breadcrumb->add($event->get_is_sub() ? ScmDivisionService::get_division($event->get_division_id())->get_division_name() : $event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug(), AppContext::get_request()->get_getint('round', 0)));
		$breadcrumb->add($this->lang['scm.games.management'], ScmUrlBuilder::edit_groups_games($event->get_id(), $event->get_event_slug(), AppContext::get_request()->get_getint('round', 0)));

		return $response;
	}
}
?>
