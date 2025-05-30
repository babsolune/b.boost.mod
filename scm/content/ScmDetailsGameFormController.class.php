<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 07 23
 * @since       PHPBoost 6.0 - 2024 07 23
*/

class ScmDetailsGameFormController extends DefaultModuleController
{
    private $event;
    private $params;
    private $games_number;
    private $bracket_games;
    private $practice_games;
    private $return_games;

	public function execute(HTTPRequestCustom $request)
	{
        $this->init();
		$this->check_authorizations();

        $this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
            $this->redirect();
        }

		$this->view->put_all([
            'MENU'    => ScmMenuService::build_event_menu($this->event_id()),
            'CONTENT' => $this->form->display(),
        ]);

		return $this->generate_response($this->view);
	}

    private function init()
    {
        $this->games_number = ScmGameService::get_games($this->event_id());
        $this->return_games = $this->get_event()->get_event_game_type() == ScmEvent::RETURN_GAMES;
        $this->bracket_games = $this->get_game()->get_game_type() === 'B';
        $this->practice_games = $this->get_game()->get_game_type() === 'P';
    }

	private function build_form()
	{
        if($this->get_game()->get_game_type() == 'D')
            $url = ScmUrlBuilder::edit_days_games($this->event_id(), $this->get_event()->get_event_slug(), $this->get_game()->get_game_cluster())->rel();
        if($this->get_game()->get_game_type() == 'G')
            $url = ScmUrlBuilder::edit_groups_games($this->event_id(), $this->get_event()->get_event_slug(), $this->get_game()->get_game_cluster())->rel();
        if($this->get_game()->get_game_type() == 'B')
            $url = ScmUrlBuilder::edit_brackets_games($this->event_id(), $this->get_event()->get_event_slug(), $this->get_game()->get_game_cluster())->rel();
        if($this->get_game()->get_game_type() == 'P')
            $url = ScmUrlBuilder::edit_practice_games($this->event_id(), $this->get_event()->get_event_slug(), $this->get_game()->get_game_cluster())->rel();

        $form = new HTMLForm(__CLASS__);
        $form->set_css_class('floating-submit');
		$form->set_layout_title(
            '<div class="align-center small">' . $this->lang['scm.game.event.details'] . '</div>'
            . '<div class="align-center smaller">' . $this->get_game()->get_game_type() . $this->get_game()->get_game_cluster() . $this->get_game()->get_game_round() . $this->get_game()->get_game_order() . ' - <a href="' . $url . '" class="small offload"><i class="fa fa-share-from-square fa-flip-horizontal"></i> ' . $this->lang['common.back'] . '</a></div>'
        );

        $fieldset = new FormFieldsetHTML('game', '');
        $fieldset->set_css_class('game-details portable');
        $form->add_fieldset($fieldset);

        $fieldset->add_field(new FormFieldSpacer('empty', '', ['class' => 'portable-full']));
        $fieldset->add_field(new FormFieldSpacer('team_1', $this->get_game()->get_game_home_id() == 0 ? $this->lang['scm.th.team'] . ' 1' : ScmTeamService::get_team_name($this->get_game()->get_game_home_id()),
            ['class' => 'portable-half']
        ));
        $fieldset->add_field(new FormFieldSpacer('team_2', $this->get_game()->get_game_away_id() == 0 ? $this->lang['scm.th.team'] . ' 2' : ScmTeamService::get_team_name($this->get_game()->get_game_away_id()),
            ['class' => 'portable-half']
        ));

        if (($this->bracket_games || $this->practice_games) && $this->return_games) {
            if (($this->get_game()->get_game_order() > count($this->games_number) / 2)) {
                $fieldset->add_field(new FormFieldSpacer('penalties', $this->lang['scm.game.event.penalties'],
                    ['class' => 'portable-full']
                ));
                $fieldset->add_field(new FormFieldNumberEditor('home_pen', '', $this->get_game()->get_game_home_pen(),
                    ['class' => 'home-details portable-half', 'pattern' => '[0-9]*']
                ));
                $fieldset->add_field(new FormFieldNumberEditor('away_pen', '', $this->get_game()->get_game_away_pen(),
                    ['class' => 'away-details portable-half', 'pattern' => '[0-9]*']
                ));
            }
        }
        elseif (($this->bracket_games || $this->practice_games) && !$this->return_games) {
            $fieldset->add_field(new FormFieldSpacer('penalties', $this->lang['scm.game.event.penalties'],
                ['class' => 'portable-full']
            ));
            $fieldset->add_field(new FormFieldNumberEditor('home_pen', '', $this->get_game()->get_game_home_pen(),
                ['class' => 'home-details portable-half', 'pattern' => '[0-9]*']
            ));
            $fieldset->add_field(new FormFieldNumberEditor('away_pen', '', $this->get_game()->get_game_away_pen(),
                ['class' => 'away-details portable-half', 'pattern' => '[0-9]*']
            ));
        }

        if ($this->bracket_games) {
            $fieldset->add_field(new FormFieldSpacer('empty_field', $this->lang['scm.game.event.empty.field'],
                ['class' => 'portable-full']
            ));
            $fieldset->add_field(new FormFieldTextEditor('home_empty', '', $this->get_game()->get_game_home_empty(),
                ['class' => 'portable-half']
            ));
            $fieldset->add_field(new FormFieldTextEditor('away_empty', '', $this->get_game()->get_game_away_empty(),
                ['class' => 'portable-half']
            ));
        }

        if($this->get_params()->get_bonus())
        {
            $fieldset->add_field(new FormFieldSpacer('offensive_bonus', $this->get_params()->get_bonus() == ScmParams::BONUS_DOUBLE ? $this->lang['scm.game.event.bonus.off'] : $this->lang['scm.game.event.bonus'],
                ['class' => 'portable-full']
            ));
            $fieldset->add_field(new FormFieldNumberEditor('home_off_bonus', '', $this->get_game()->get_game_home_off_bonus(),
                ['class' => 'portable-half home-details', 'pattern' => '[0-9]*']
            ));
            $fieldset->add_field(new FormFieldNumberEditor('away_off_bonus', '', $this->get_game()->get_game_away_off_bonus(),
                ['class' => 'portable-half away-details', 'pattern' => '[0-9]*']
            ));
            if($this->get_params()->get_bonus() == ScmParams::BONUS_DOUBLE)
            {
                $fieldset->add_field(new FormFieldSpacer('defensive_bonus', $this->lang['scm.game.event.bonus.def'],
                    ['class' => 'portable-full']
                ));
                $fieldset->add_field(new FormFieldNumberEditor('home_def_bonus', '', $this->get_game()->get_game_home_def_bonus(),
                    ['class' => 'portable-half home-details', 'pattern' => '[0-9]*']
                ));
                $fieldset->add_field(new FormFieldNumberEditor('away_def_bonus', '', $this->get_game()->get_game_away_def_bonus(),
                    ['class' => 'portable-half away-details', 'pattern' => '[0-9]*']
                ));
            }
        }

        $fieldset->add_field(new FormFieldSpacer('game_forfeit', $this->lang['scm.game.event.forfeit'],
            ['class' => 'portable-full']
        ));
        $fieldset->add_field(new FormFieldCheckbox('home_forfeit', '', $this->get_game()->get_game_home_forfeit(),
            ['class' => 'portable-half']
        ));
        $fieldset->add_field(new FormFieldCheckbox('away_forfeit', '', $this->get_game()->get_game_away_forfeit(),
            ['class' => 'portable-half']
        ));

        $fieldset->add_field(new FormFieldSpacer('game_goals', $this->lang['scm.game.event.goals'],
            ['class' => 'portable-full']
        ));
        $fieldset->add_field(new ScmFormFieldGameEvents('home_goals', '', $this->get_game()->get_game_home_goals(),
            ['class' => 'portable-half']
        ));
        $fieldset->add_field(new ScmFormFieldGameEvents('away_goals', '', $this->get_game()->get_game_away_goals(),
            ['class' => 'portable-half']
        ));

        $fieldset->add_field(new FormFieldSpacer('yellow_card', $this->lang['scm.game.event.cards.yellow'],
            ['class' => 'portable-full']
        ));
        $fieldset->add_field(new ScmFormFieldGameEvents('home_yellow', '', $this->get_game()->get_game_home_yellow(),
            ['class' => 'portable-half']
        ));
        $fieldset->add_field(new ScmFormFieldGameEvents('away_yellow', '', $this->get_game()->get_game_away_yellow(),
            ['class' => 'portable-half']
        ));

        $fieldset->add_field(new FormFieldSpacer('red_card', $this->lang['scm.game.event.cards.red'],
            ['class' => 'portable-full']
        ));
        $fieldset->add_field(new ScmFormFieldGameEvents('home_red', '', $this->get_game()->get_game_home_red(),
            ['class' => 'portable-half']
        ));
        $fieldset->add_field(new ScmFormFieldGameEvents('away_red', '', $this->get_game()->get_game_away_red(),
            ['class' => 'portable-half']
        ));

        $fieldset_unique = new FormFieldsetHTML('unique_details', '');
        $form->add_fieldset($fieldset_unique);

        if (!$this->get_params()->get_display_playgrounds())
        {
            $fieldset_unique->add_field(new FormFieldSimpleSelectChoice('stadium', $this->lang['scm.game.event.stadium'], $this->get_game()->get_game_stadium(),
                $this->get_stadium($this->get_game()->get_game_home_id()),
                ['class' => 'portable-full']
            ));
        }

        $fieldset_unique->add_field(new FormFieldUrlEditor('video', $this->lang['scm.game.event.video'], $this->get_game()->get_game_video()->relative(),
            ['class' => 'portable-full']
        ));

        $fieldset_unique->add_field(new FormFieldRichTextEditor('summary', $this->lang['scm.game.event.summary'], $this->get_game()->get_game_summary(),
            ['class' => 'portable-full']
        ));

        $this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);

		$this->form = $form;
	}

	private function save()
	{
        $game = $this->get_game();
        if ($this->bracket_games || $this->practice_games)
        {
            $game->set_game_home_pen($this->form->get_value('home_pen'));
            $game->set_game_away_pen($this->form->get_value('away_pen'));
        }
        $game->set_game_home_goals($this->form->get_value('home_goals'));
        $game->set_game_home_yellow($this->form->get_value('home_yellow'));
        $game->set_game_home_red($this->form->get_value('home_red'));
        $game->set_game_home_empty($this->form->get_value('home_empty'));
        $game->set_game_home_forfeit($this->form->get_value('home_forfeit'));
        $game->set_game_away_goals($this->form->get_value('away_goals'));
        $game->set_game_away_yellow($this->form->get_value('away_yellow'));
        $game->set_game_away_red($this->form->get_value('away_red'));
        $game->set_game_away_empty($this->form->get_value('away_empty'));
        $game->set_game_away_forfeit($this->form->get_value('away_forfeit'));

        $game->set_game_video(new Url($this->form->get_value('video')));
        $game->set_game_summary($this->form->get_value('summary'));

        if (!$this->get_params()->get_display_playgrounds())
        {
            $game->set_game_stadium($this->form->get_value('stadium')->get_raw_value());
            $game->set_game_stadium_name($this->form->get_value('stadium')->get_label());
        }

        if($this->get_params()->get_bonus())
        {
            $game->set_game_home_off_bonus($this->form->get_value('home_off_bonus'));
            $game->set_game_away_off_bonus($this->form->get_value('away_off_bonus'));
            if($this->get_params()->get_bonus() == ScmParams::BONUS_DOUBLE)
            {
                $game->set_game_home_def_bonus($this->form->get_value('home_def_bonus'));
                $game->set_game_away_def_bonus($this->form->get_value('away_def_bonus'));
            }
        }

        ScmGameService::update_game($game, $game->get_id_game());

		ScmEventService::clear_cache();
	}

    private function get_stadium(int $club_id)
    {
        if ($club_id)
        {
            $team = ScmTeamService::get_team($club_id);
            $club = ScmClubCache::load()->get_club($team->get_team_club_id());
            $real_id = $club['club_affiliate'] ? $club['club_affiliation'] : $club['id_club'];
            $real_club = new ScmClub();
            $real_club->set_properties(ScmClubCache::load()->get_club($real_id));
// Debug::stop($real_club);
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
        return [new FormFieldSelectChoiceOption($this->lang['scm.club.no.home.club'], 0)];
    }

    private function get_game()
    {
        $request = AppContext::get_request();
        $type = $request->get_getstring('type', '');
        $cluster = $request->get_getint('cluster', 0);
        $round = $request->get_getint('round', 0);
        $order = $request->get_getint('order', 0);
        $game = ScmGameService::get_game($this->event_id(), $type, $cluster, $round, $order);

        return $game;
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

    private function redirect()
    {
        $type    = $this->get_game()->get_game_type();
        $cluster = $this->get_game()->get_game_cluster();
        $round   = $this->get_game()->get_game_round();
        $order   = $this->get_game()->get_game_order();
        return AppContext::get_response()->redirect(ScmUrlBuilder::edit_details_game($this->event_id(), $this->get_event()->get_event_slug(), $type, $cluster, $round, $order), $this->lang['scm.warning.details.update']);
    }

	private function generate_response(View $view)
	{
		$event = $this->get_event();
        $category = $event->get_category();
        $request = AppContext::get_request();

		$location_id = $event->get_id() ? 'scm-details-game'. $event->get_id() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		if (!AppContext::get_session()->location_id_already_exists($location_id))
            $graphical_environment->set_location_id($location_id);

        $graphical_environment->set_page_title($this->lang['scm.game.event.details'], $event->get_event_name() . ($category->get_id() != Category::ROOT_CATEGORY ? ' - ' . $category->get_name() : '') . ' - ' . $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name());
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.games.management']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit_groups_games($event->get_id(), $event->get_event_slug()));

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
		if ($request->get_value('type') == 'G')
            $link = ScmUrlBuilder::edit_groups_games($event->get_id(), $event->get_event_slug(), $request->get_value('cluster'));
        elseif ($request->get_value('type') == 'B')
            $link = ScmUrlBuilder::edit_brackets_games($event->get_id(), $event->get_event_slug(), $request->get_value('cluster'));
        elseif ($request->get_value('type') == 'D')
            $link = ScmUrlBuilder::edit_days_games($event->get_id(), $event->get_event_slug(), $request->get_value('cluster'));
        elseif ($request->get_value('type') == 'P')
            $link = ScmUrlBuilder::edit_practice_games($event->get_id(), $event->get_event_slug());
        $breadcrumb->add($this->lang['scm.games.management'], $link);
        $breadcrumb->add($this->lang['scm.game.event.details'], ScmUrlBuilder::edit_details_game($this->event_id(), $this->get_event()->get_event_slug(), $request->get_value('type'), $request->get_value('cluster'), $request->get_value('round'), $request->get_value('order')));

		return $response;
	}
}
?>
