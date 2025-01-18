<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmPracticeGamesFormController extends DefaultModuleController
{
    private $event;
    private $params;
    private $game;
    private $games;
    private $return_games;

	public function execute(HTTPRequestCustom $request)
	{
        $this->init();
		$this->check_authorizations();
        $this->build_form();

        if ($this->submit_button->has_been_submited() && $this->form->validate())
        {
            $this->save();
            AppContext::get_response()->redirect(ScmUrlBuilder::edit_practice_games($this->event_id(), $this->get_event()->get_event_slug()), $this->lang['scm.warning.games.update']);
        }

		$this->view->put_all([
            'MENU' => ScmMenuService::build_event_menu($this->event_id()),
            'CONTENT' => $this->form->display(),
        ]);

		return $this->generate_response($this->view);
	}

    private function init()
    {
        $this->games = ScmGameService::get_games($this->event_id());
        $this->return_games = ScmEventService::get_event_game_type($this->event_id()) == ScmDivision::RETURN_GAMES;
    }

	private function build_form()
	{
        $form = new HTMLForm(__CLASS__);
        $form->set_css_class('floating-submit');
        $form->set_layout_title(
            '<div class="align-center small">' . $this->lang['scm.games.management'] . '</div>'
        );

        $games_number = count($this->games);

        foreach ($this->games as $game)
        {
            $cluster = $game['game_cluster'];
            $round = $game['game_round'];
            $order = $game['game_order'];
            $field = $cluster . $round . $order;

            $fieldset = new FormFieldsetHTML('practice', '');
            $fieldset->set_css_class('grouped-fields cluster-fields');
            $form->add_fieldset($fieldset);

            $empty_teams = '';
            if ($this->get_game('P', $cluster, $round, $order)->get_game_home_empty())
                $empty_teams = ' - ' . $this->get_game('P', $cluster, $round, $order)->get_game_home_empty() . '|' . $this->get_game('P', $cluster, $round, $order)->get_game_away_empty();
            $game_number     = '<strong>P' . $cluster . $round . $order . '</strong>' . $empty_teams;
            $game_date       = $this->get_game('P', $cluster, $round, $order)->get_game_date();
            $game_playground = $this->get_game('P', $cluster, $round, $order)->get_game_playground();
            $game_home_id    = $this->get_game('P', $cluster, $round, $order)->get_game_home_id();
            $game_home_score = $this->get_game('P', $cluster, $round, $order)->get_game_home_score();
            $game_away_score = $this->get_game('P', $cluster, $round, $order)->get_game_away_score();
            $game_away_id    = $this->get_game('P', $cluster, $round, $order)->get_game_away_id();
            $game_status     = $this->get_game('P', $cluster, $round, $order)->get_game_status();
            $bonus = $this->get_params()->get_bonus() &&
                ($game->get_game_home_off_bonus() ||
                $game->get_game_home_def_bonus() ||
                $game->get_game_away_off_bonus() ||
                $game->get_game_away_def_bonus()) ? ' ' . $this->lang['scm.bonus.param'] 
                : '';
            $c_has_details = ScmGameService::has_details($this->event_id(), 'P', $cluster, $round, $order);
            $details_class = $c_has_details ? ' success' : '';

            if ($this->return_games && $order == 1 && $round != 1)
                $fieldset->add_field(new FormFieldSpacer('first_leg_' . $field, $this->lang['scm.first.leg'],
                    ['class' => 'bgc notice align-center']
                ));
            $fieldset->add_field(new FormFieldFree('game_number_' . $field, '', $game_number . $bonus,
                ['class' => 'label-top game-name small text-italic form-P-' . $field]
            ));
            $fieldset->add_field(new FormFieldActionLink('details_' . $field, '<span aria-label="' . $this->lang['scm.game.event.details'] . '"><i class="far fa-square-plus" aria-hidden="true"></i></span>' , ScmUrlBuilder::edit_details_game($this->event_id(), $this->get_event()->get_event_slug(), 'P', $cluster, $round, $order), 'd-inline-block game-details align-right' . $details_class));

            $fieldset->add_field(new FormFieldDateTime('game_date_' . $field, $this->lang['scm.game.form.date'], $game_date,
                ['class' => 'label-top game-date']
            ));
            $fieldset->add_field(new FormFieldSimpleSelectChoice('home_team_' . $field, $this->lang['scm.game.form.home.team'], $game_home_id,
                $this->get_teams_list(),
                ['class' => 'label-top home-team game-team']
            ));
            $fieldset->add_field(new FormFieldNumberEditor('home_score_' . $field, $this->lang['scm.game.form.home.score'], $game_home_score,
                ['class' => 'label-top home-team game-score', 'pattern' => '[0-9]*']
            ));
            $fieldset->add_field(new FormFieldNumberEditor('away_score_' . $field, $this->lang['scm.game.form.away.score'], $game_away_score,
                ['class' => 'label-top away-team game-score', 'pattern' => '[0-9]*']
            ));
            $fieldset->add_field(new FormFieldSimpleSelectChoice('away_team_' . $field, $this->lang['scm.game.form.away.team'], $game_away_id,
                $this->get_teams_list(),
                ['class' => 'label-top away-team game-team']
            ));
            $fieldset->add_field(new FormFieldSimpleSelectChoice('status_' . $field, $this->lang['scm.game.form.status'], $game_status,
                [
                    new FormFieldSelectChoiceOption('', ''),
                    // new FormFieldSelectChoiceOption($this->lang['scm.game.form.status.completed'], ScmGame::COMPLETED),
                    new FormFieldSelectChoiceOption($this->lang['scm.game.form.status.delayed'], ScmGame::DELAYED),
                    new FormFieldSelectChoiceOption($this->lang['scm.game.form.status.stopped'], ScmGame::STOPPED)
                ],
                ['class' => 'label-top game-status portable-full']
            ));
            if($this->get_params()->get_display_playgrounds())
                $fieldset->add_field(new FormFieldTextEditor('game_playground_' . $field, '', $game_playground,
                    ['class' => 'game-playground', 'placeholder' => $this->lang['scm.field']]
                ));
            $fieldset->add_field(new FormFieldSpacer('separator_' . $field, '<hr />',
                ['class' => 'game-hr']
            ));
            if ($this->return_games && $order == ($games_number / 2) + 1)
                $fieldset->add_field(new FormFieldSpacer('winner_second_leg_' . $field, $this->lang['scm.second.leg'],
                    ['class' => 'bgc notice align-center']
                ));
        }

        $this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);

		$this->form = $form;
	}

	private function save()
	{
        foreach($this->games as $game)
        {
            $cluster = $game['game_cluster'];
            $round = $game['game_round'];
            $order = $game['game_order'];
            $field = $cluster . $round . $order;
            $item = $this->get_game('P', $cluster, $round, $order);

            $item->set_game_date($this->form->get_value('game_date_' . $field));
            if($this->get_params()->get_display_playgrounds())
                $item->set_game_playground($this->form->get_value('game_playground_' . $field));
            $item->set_game_home_id((int)$this->form->get_value('home_team_' . $field)->get_raw_value());
            $item->set_game_home_score($this->form->get_value('home_score_' . $field));
            $item->set_game_home_pen($this->form->get_value('home_pen_' . $field));
            $item->set_game_away_pen($this->form->get_value('away_pen_' . $field));
            $item->set_game_away_score($this->form->get_value('away_score_' . $field));
            $item->set_game_away_id((int)$this->form->get_value('away_team_' . $field)->get_raw_value());
            $item->set_game_status($this->form->get_value('status_' . $field)->get_raw_value());

            ScmGameService::update_game($item, $item->get_id_game());
        }

		ScmEventService::clear_cache();
	}

	private function get_game($type, $cluster, $round, $order)
	{
        try {
            $this->game = ScmGameService::get_game($this->event_id(), $type, $cluster, $round, $order);
        } catch (RowNotFoundException $e) {
            $error_controller = PHPBoostErrors::unexisting_page();
            DispatchManager::redirect($error_controller);
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

    private function get_teams_list()
    {
        $options = [];
        $clubs = ScmClubCache::load();
        $options[] = new FormFieldSelectChoiceOption('', 0);
        foreach (ScmTeamService::get_teams($this->event_id()) as $team)
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
        $category = $event->get_category();
		$location_id = $event->get_id() ? 'scm-bracket-games-'. $event->get_id() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		if (!AppContext::get_session()->location_id_already_exists($location_id))
            $graphical_environment->set_location_id($location_id);

        $graphical_environment->set_page_title($this->lang['scm.games.management'], $event->get_event_name() . ($category->get_id() != Category::ROOT_CATEGORY ? ' - ' . $category->get_name() : '') . ' - ' . $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name());
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.games.management']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit_practice_games($event->get_id(), $event->get_event_slug()));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());
        $categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($event->get_id_category(), true));
        foreach ($categories as $id => $category)
        {
            if ($category->get_id() != Category::ROOT_CATEGORY)
                $breadcrumb->add($category->get_name(), ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
        }
        if ($event->get_is_sub())
            $breadcrumb->add(ScmEventService::get_master_name($event->get_id()), ScmEventService::get_master_url($event->get_id()));
		$breadcrumb->add($event->get_is_sub() ? ScmDivisionService::get_division($event->get_division_id())->get_division_name() : $event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug(), AppContext::get_request()->get_getint('cluster', 0)));
		$breadcrumb->add($this->lang['scm.games.management'], ScmUrlBuilder::edit_practice_games($event->get_id(), $event->get_event_slug()));

		return $response;
	}
}
?>
