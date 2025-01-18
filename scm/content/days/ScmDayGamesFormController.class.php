<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmDayGamesFormController extends DefaultModuleController
{
    private $event;
    private $params;
    private $game;
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
            AppContext::get_response()->redirect(ScmUrlBuilder::edit_days_games($this->event_id(), $this->get_event()->get_event_slug(), AppContext::get_request()->get_getint('cluster', 0)), $this->lang['scm.warning.games.update']);
        }

		$this->view->put_all([
            'MENU' => ScmMenuService::build_event_menu($this->event_id()),
            'CONTENT' => $this->form->display(),
        ]);

		return $this->generate_response($this->view);
	}

    private function init()
    {
        $this->teams_number = ScmTeamService::get_teams_number($this->event_id());
        $this->return_games = ScmEventService::get_event_game_type($this->event_id()) == ScmDivision::RETURN_GAMES;
    }

	private function build_form()
	{
        $cluster = AppContext::get_request()->get_getint('cluster', 0);
        $until_last_day = ScmDayService::get_last_day($this->event_id()) > $cluster;

        $form = new HTMLForm(self::class);
        $form->set_css_class('modal-container floating-submit form-loader');
		$form->set_layout_title('<div class="align-center small">' . $this->lang['scm.games.management'] . '</div>');

        $fieldset = new FormFieldsetHTML('days', $this->lang['scm.day'] . ' ' . $cluster);
		$form->add_fieldset($fieldset);

        $fieldset->add_field(new FormFieldFree( 'waiting_cover', '', $this->lang['scm.waiting.ranking'] . ($until_last_day ? '<br />' . $this->lang['scm.waiting.ranking.next'] : '') . '<br />' . $this->lang['scm.waiting.please'],
            ['class' => 'waiting-cover']
        ));

        $fieldset->add_field(new FormFieldFree( 'waiting_loader', '', '',
            ['class' => 'waiting-loader']
        ));

        foreach($this->get_day_games($cluster) as $game)
        {
            $item = new ScmGame();
            $item->set_properties($game);

            $round = $item->get_game_round();
            $order = $item->get_game_order();
            $bonus = $this->get_params()->get_bonus() &&
                ($item->get_game_home_off_bonus() ||
                $item->get_game_home_def_bonus() ||
                $item->get_game_away_off_bonus() ||
                $item->get_game_away_def_bonus())
                    ? ' ' . $this->lang['scm.bonus.param']
                    : '';
            $forfeit = $item->get_game_home_forfeit() || $item->get_game_away_forfeit() ?  ' ' . $this->lang['scm.game.event.forfeit'] : '';

            // ScmGameFormService::get_details_field_list($form, self::class, $this->event_id(), $item, 'D', $cluster, 0, $order, $this->lang);
            // ScmGameFormService::get_field_list($form, self::class, $this->event_id(), $item, 'D', $cluster, 0, $order, $this->lang);

            $field = $cluster . $round . $order;
            $c_has_details = ScmGameService::has_details($this->event_id(), 'D', $cluster, $round, $order);
            $details_class = $c_has_details ? ' success' : '';

            $fieldset = new FormFieldsetHTML('game_' . $field, '');
            $fieldset->set_css_class('grouped-fields matchdays-game');
            $form->add_fieldset($fieldset);

            $fieldset->add_field(new FormFieldFree(
                'game_number_' . $field,
                '',
                '<strong>D' . $field . '</strong><span class="warning">' . $bonus . $forfeit . '</span>',
                ['class' => 'game-name small text-italic form-D' . $field]
            ));
            $fieldset->add_field(new FormFieldActionLink('details_' . $field, '<span aria-label="' . $this->lang['scm.game.event.details'] . '"><i class="far fa-square-plus" aria-hidden="true"></i></span>' , ScmUrlBuilder::edit_details_game($this->event_id(), $this->get_event()->get_event_slug(), 'D', $cluster, 0, $order), 'd-inline-block game-details align-right' . $details_class));

            $fieldset->add_field(new FormFieldDateTime('game_date_' . $field, $this->lang['scm.game.form.date'], $item->get_game_date(),
                ['class' => 'game-date label-top']
            ));
            $fieldset->add_field(new FormFieldSimpleSelectChoice('home_team_' . $field, $this->lang['scm.game.form.home.team'], $item->get_game_home_id(),
                $this->get_teams_list(),
                ['class' => 'home-team game-team label-top']
            ));
            $fieldset->add_field(new FormFieldNumberEditor('home_score_' . $field, $this->lang['scm.game.form.home.score'], $item->get_game_home_score(),
                [
                    'min' => 0,
                    'class' => 'home-team game-score label-top',
                    // 'pattern' => '[0-9]*'
                ]
            ));
            $fieldset->add_field(new FormFieldNumberEditor('away_score_' . $field, $this->lang['scm.game.form.away.score'], $item->get_game_away_score(),
                [
                    'min' => 0,
                    'class' => 'away-team game-score label-top',
                    // 'pattern' => '[0-9]*'
                ]
            ));
            $fieldset->add_field(new FormFieldSimpleSelectChoice('away_team_' . $field, $this->lang['scm.game.form.away.team'], $item->get_game_away_id(),
                $this->get_teams_list(),
                ['class' => 'away-team game-team label-top']
            ));
            $fieldset->add_field(new FormFieldSimpleSelectChoice('status_' . $field, $this->lang['scm.game.form.status'], $item->get_game_status(),
                [
                    new FormFieldSelectChoiceOption('', ''),
                    // new FormFieldSelectChoiceOption($this->lang['scm.game.form.status.completed'], ScmGame::COMPLETED),
                    new FormFieldSelectChoiceOption($this->lang['scm.game.form.status.delayed'], ScmGame::DELAYED),
                    new FormFieldSelectChoiceOption($this->lang['scm.game.form.status.stopped'], ScmGame::STOPPED)
                ],
                ['class' => 'game-status portable-full label-top']
            ));
            if($this->get_params()->get_display_playgrounds())
                $fieldset->add_field(new FormFieldTextEditor('game_playground_' . $field, '', $item->get_game_playground(),
                    ['class' => 'game-playground', 'placeholder' => $this->lang['scm.field']]
                ));
            $fieldset->add_field(new FormFieldSpacer('separator_' . $field, '<hr />',
                ['class' => 'game-hr']
            ));
        }

        $this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);

		$this->form = $form;
	}

	private function save()
	{
        $cluster = AppContext::get_request()->get_getint('cluster', 0);

        $games = [];
        foreach($this->get_day_games($cluster) as $game)
        {
            $item = new ScmGame();
            $item->set_properties($game);
            $round = $item->get_game_round();
            $order = $item->get_game_order();
            $field = $cluster . $round . $order;
            $item->set_game_event_id($this->event_id());
            $item->set_game_date($this->form->get_value('game_date_' . $field));
            if($this->get_params()->get_display_playgrounds())
                $item->set_game_playground($this->form->get_value('game_playground_' . $field));
            $item->set_game_home_id((int)$this->form->get_value('home_team_' . $field)->get_raw_value());
            $item->set_game_home_score($this->form->get_value('home_score_' . $field));
            $item->set_game_away_score($this->form->get_value('away_score_' . $field));
            $item->set_game_away_id((int)$this->form->get_value('away_team_' . $field)->get_raw_value());
            $item->set_game_status($this->form->get_value('status_' . $field)->get_raw_value());

            ScmGameService::update_game($item, $item->get_id_game());
            $games[] = $item->get_game_home_score();
        }

        foreach($this->get_day_games($cluster) as $game)
        {
            $item = new ScmGame();
            $item->set_properties($game);

            if (ScmDayService::day_has_scores($games))
                ScmDayService::update_day_played($this->event_id(), $item->get_game_cluster(), '1');
            else
                ScmDayService::update_day_played($this->event_id(), $item->get_game_cluster(), '0');
        }

		ScmEventService::clear_cache();
        ScmRankingCache::set_cache_file_ranking($this->event_id(), $cluster);
        ScmRankingContentService::set_ranking_content($this->event_id(), $cluster);
	}

    private function get_teams_list()
    {
        $options = [];
        $options[] = new FormFieldSelectChoiceOption('', 0);
        foreach (ScmTeamService::get_teams($this->event_id()) as $team)
        {
			$options[] = new FormFieldSelectChoiceOption($team['club_name'], $team['id_team']);
        }

		return $options;
    }

    private function get_day_games($cluster)
    {
        $games = ScmGroupService::games_list_from_group($this->event_id(), 'D', $cluster);

        return $games;
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
            <script src="{PATH_TO_ROOT}/scm/templates/js/scm.loader# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
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
		$location_id = $event->get_id() ? 'scm-day-games-'. $event->get_id() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		if (!AppContext::get_session()->location_id_already_exists($location_id))
            $graphical_environment->set_location_id($location_id);

        $graphical_environment->set_page_title($this->lang['scm.games.management'], $event->get_event_name() . ($category->get_id() != Category::ROOT_CATEGORY ? ' - ' . $category->get_name() : '') . ' - ' . $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name());
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.games.management']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit_days_games($event->get_id(), $event->get_event_slug()));


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
		$breadcrumb->add($event->get_is_sub() ? ScmDivisionService::get_division($event->get_division_id())->get_division_name() : $event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));
		$breadcrumb->add($this->lang['scm.games.management'], ScmUrlBuilder::edit_days_games($event->get_id(), $event->get_event_slug(), AppContext::get_request()->get_value('cluster')));

		return $response;
	}
}
?>
