<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 07 23
 * @since       PHPBoost 6.0 - 2024 07 23
*/

class ScmGameFormService
{
	public static function get_details_field_list($form, $php_class, $event_id, $item, $type, $cluster, $round, $order, $lang)
	{
        $field = $cluster.$round.$order;
        ${'modal_menu_'.$field} = new FormFieldMenuFieldset('modal_menu_' . $field, '');
        $form->add_fieldset(${'modal_menu_'.$field});
        ${'modal_menu_'.$field}->set_css_class('modal-nav');
        ${'modal_menu_'.$field}->add_field(new FormFieldMultitabsLinkList('modal_button_' . $field,
            [new FormFieldMultitabsLinkElement('', 'modal', $php_class . '_modal_' . $field, 'far fa-square-plus')]
        ));
        ${'modal_fieldset_'.$field} = new FormFieldsetMultitabsHTML('modal_' . $field, $lang['scm.game.event.details'] . ' ' . $field,
            ['css_class' => 'modal modal-animation game-details portable', 'modal' => true]
        );
        $form->add_fieldset(${'modal_fieldset_'.$field});
        ${'modal_fieldset_'.$field}->add_field(new FormFieldSpacer('sub_title', '', ['class' => 'w100']));
        ${'modal_fieldset_'.$field}->add_field(new FormFieldSpacer('empty', '', ['class' => 'portable-full']));
        ${'modal_fieldset_'.$field}->add_field(new FormFieldSpacer('team_1_' . $field, $item->get_game_home_id() == 0 ? $lang['scm.th.team'] . ' 1' : ScmTeamService::get_team_name($item->get_game_home_id()),
            ['class' => 'portable-half']
        ));
        ${'modal_fieldset_'.$field}->add_field(new FormFieldSpacer('team_2_' . $field, $item->get_game_away_id() == 0 ? $lang['scm.th.team'] . ' 2' : ScmTeamService::get_team_name($item->get_game_away_id()),
            ['class' => 'portable-half']
        ));
        if ($type == 'B' && ScmEventService::get_event_game_type($event_id) == ScmDivision::RETURN_GAMES) {
            if (($item->get_game_order() > count(ScmGameService::get_games($event_id)) / 2)) {
                ${'modal_fieldset_'.$field}->add_field(new FormFieldSpacer('penalties_' . $field, $lang['scm.game.event.penalties'],
                    ['class' => 'portable-full']
                ));
                ${'modal_fieldset_'.$field}->add_field(new FormFieldNumberEditor('home_pen_' . $field, '', $item->get_game_home_pen(),
                    ['class' => 'home-details portable-half', 'pattern' => '[0-9]*']
                ));
                ${'modal_fieldset_'.$field}->add_field(new FormFieldNumberEditor('away_pen_' . $field, '', $item->get_game_away_pen(),
                    ['class' => 'away-details portable-half', 'pattern' => '[0-9]*']
                ));
            }
        }
        elseif ($type == 'B') {
            ${'modal_fieldset_'.$field}->add_field(new FormFieldSpacer('penalties_' . $field, $lang['scm.game.event.penalties'],
                ['class' => 'portable-full']
            ));
            ${'modal_fieldset_'.$field}->add_field(new FormFieldNumberEditor('home_pen_' . $field, '', $item->get_game_home_pen(),
                ['class' => 'home-details portable-half', 'pattern' => '[0-9]*']
            ));
            ${'modal_fieldset_'.$field}->add_field(new FormFieldNumberEditor('away_pen_' . $field, '', $item->get_game_away_pen(),
                ['class' => 'away-details portable-half', 'pattern' => '[0-9]*']
            ));
        }
        if ($type == 'B') {
            ${'modal_fieldset_'.$field}->add_field(new FormFieldSpacer('empty_field_' . $field, $lang['scm.game.event.empty.field'],
                ['class' => 'portable-full']
            ));
            ${'modal_fieldset_'.$field}->add_field(new FormFieldTextEditor('home_empty_' . $field, '', $item->get_game_home_empty(),
                ['class' => 'portable-half']
            ));
            ${'modal_fieldset_'.$field}->add_field(new FormFieldTextEditor('away_empty_' . $field, '', $item->get_game_away_empty(),
                ['class' => 'portable-half']
            ));
        }
        if(self::get_params($event_id)->get_bonus())
        {
            ${'modal_fieldset_'.$field}->add_field(new FormFieldSpacer('offensive_bonus_' . $field, self::get_params($event_id)->get_bonus() == ScmParams::BONUS_DOUBLE ? $lang['scm.game.event.bonus.off'] : $lang['scm.game.event.bonus'],
                ['class' => 'portable-full']
            ));
            ${'modal_fieldset_'.$field}->add_field(new FormFieldNumberEditor('home_off_bonus_' . $field, '', $item->get_game_home_off_bonus(),
                ['class' => 'portable-half home-details', 'pattern' => '[0-9]*']
            ));
            ${'modal_fieldset_'.$field}->add_field(new FormFieldNumberEditor('away_off_bonus_' . $field, '', $item->get_game_away_off_bonus(),
                ['class' => 'portable-half away-details', 'pattern' => '[0-9]*']
            ));
            if(self::get_params($event_id)->get_bonus() == ScmParams::BONUS_DOUBLE)
            {
                ${'modal_fieldset_'.$field}->add_field(new FormFieldSpacer('defensive_bonus_' . $field, $lang['scm.game.event.bonus.def'],
                    ['class' => 'portable-full']
                ));
                ${'modal_fieldset_'.$field}->add_field(new FormFieldNumberEditor('home_def_bonus_' . $field, '', $item->get_game_home_def_bonus(),
                    ['class' => 'portable-half home-details', 'pattern' => '[0-9]*']
                ));
                ${'modal_fieldset_'.$field}->add_field(new FormFieldNumberEditor('away_def_bonus_' . $field, '', $item->get_game_away_def_bonus(),
                    ['class' => 'portable-half away-details', 'pattern' => '[0-9]*']
                ));
            }
        }
        ${'modal_fieldset_'.$field}->add_field(new FormFieldSpacer('game_forfeit_' . $field, $lang['scm.game.event.forfeit'],
            ['class' => 'portable-full']
        ));
        ${'modal_fieldset_'.$field}->add_field(new FormFieldCheckbox('home_forfeit_' . $field, '', $item->get_game_home_forfeit(),
            ['class' => 'portable-half']
        ));
        ${'modal_fieldset_'.$field}->add_field(new FormFieldCheckbox('away_forfeit_' . $field, '', $item->get_game_away_forfeit(),
            ['class' => 'portable-half']
        ));
        ${'modal_fieldset_'.$field}->add_field(new FormFieldSpacer('game_goals_' . $field, $lang['scm.game.event.goals'],
            ['class' => 'portable-full']
        ));
        ${'modal_fieldset_'.$field}->add_field(new ScmFormFieldGameEvents('home_goals_' . $field, '', $item->get_game_home_goals(),
            ['class' => 'portable-half']
        ));
        ${'modal_fieldset_'.$field}->add_field(new ScmFormFieldGameEvents('away_goals_' . $field, '', $item->get_game_away_goals(),
            ['class' => 'portable-half']
        ));
        ${'modal_fieldset_'.$field}->add_field(new FormFieldSpacer('yellow_card_' . $field, $lang['scm.game.event.cards.yellow'],
            ['class' => 'portable-full']
        ));
        ${'modal_fieldset_'.$field}->add_field(new ScmFormFieldGameEvents('home_yellow_' . $field, '', $item->get_game_home_yellow(),
            ['class' => 'portable-half']
        ));
        ${'modal_fieldset_'.$field}->add_field(new ScmFormFieldGameEvents('away_yellow_' . $field, '', $item->get_game_away_yellow(),
            ['class' => 'portable-half']
        ));
        ${'modal_fieldset_'.$field}->add_field(new FormFieldSpacer('red_card_' . $field, $lang['scm.game.event.cards.red'],
            ['class' => 'portable-full']
        ));
        ${'modal_fieldset_'.$field}->add_field(new ScmFormFieldGameEvents('home_red_' . $field, '', $item->get_game_home_red(),
            ['class' => 'portable-half']
        ));
        ${'modal_fieldset_'.$field}->add_field(new ScmFormFieldGameEvents('away_red_' . $field, '', $item->get_game_away_red(),
            ['class' => 'portable-half']
        ));
        if (!$item->get_game_playground())
        {
            ${'modal_fieldset_'.$field}->add_field(new FormFieldSimpleSelectChoice('stadium', $lang['scm.game.event.stadium'], $item->get_game_stadium(),
                self::get_stadium($item->get_game_home_id(), $lang),
                ['class' => 'details-full']
            ));
        }
        ${'modal_fieldset_'.$field}->add_field(new FormFieldUrlEditor('video', $lang['scm.game.event.video'], $item->get_game_video()->relative(),
            ['class' => 'details-full']
        ));
        ${'modal_fieldset_'.$field}->add_field(new FormFieldRichTextEditor('summary', $lang['scm.game.event.summary'], $item->get_game_summary(),
            ['class' => 'details-full']
        ));
    }

    public static function get_field_list($form, $php_class, $event_id, $item, $type, $cluster, $round, $order, $lang)
	{
        $field = $cluster.$round.$order;
        ${'fieldset_'.$field} = new FormFieldsetHTML('game_' . $field, '');
        ${'fieldset_'.$field}->set_css_class('grouped-fields matchdays-game');
        $form->add_fieldset(${'fieldset_'.$field});

        ${'fieldset_'.$field}->add_field(new FormFieldDateTime('game_date_' . $field, $lang['scm.game.form.date'], $item->get_game_date(),
            ['class' => 'game-date label-top']
        ));
        ${'fieldset_'.$field}->add_field(new FormFieldSimpleSelectChoice('home_team_' . $field, $lang['scm.game.form.home.team'], $item->get_game_home_id(),
            self::get_teams_list($event_id),
            ['class' => 'home-team game-team label-top']
        ));
        ${'fieldset_'.$field}->add_field(new FormFieldNumberEditor('home_score_' . $field, $lang['scm.game.form.home.score'], $item->get_game_home_score(),
            ['class' => 'home-team game-score label-top', 'pattern' => '[0-9]*']
        ));
        ${'fieldset_'.$field}->add_field(new FormFieldNumberEditor('away_score_' . $field, $lang['scm.game.form.away.score'], $item->get_game_away_score(),
            ['class' => 'away-team game-score label-top', 'pattern' => '[0-9]*']
        ));
        ${'fieldset_'.$field}->add_field(new FormFieldSimpleSelectChoice('away_team_' . $field, $lang['scm.game.form.away.team'], $item->get_game_away_id(),
            self::get_teams_list($event_id),
            ['class' => 'away-team game-team label-top']
        ));
        ${'fieldset_'.$field}->add_field(new FormFieldSimpleSelectChoice('status_' . $field, $lang['scm.game.form.status'], $item->get_game_status(),
            [
                new FormFieldSelectChoiceOption('', ''),
                new FormFieldSelectChoiceOption($lang['scm.game.form.status.completed'], ScmGame::COMPLETED),
                new FormFieldSelectChoiceOption($lang['scm.game.form.status.delayed'], ScmGame::DELAYED),
                new FormFieldSelectChoiceOption($lang['scm.game.form.status.stopped'], ScmGame::STOPPED)
            ],
            ['class' => 'game-status portable-full label-top']
        ));
        if (self::get_params($event_id)->get_display_playgrounds())
            ${'fieldset_'.$field}->add_field(new FormFieldTextEditor('game_playground_' . $field, '', $item->get_game_playground(),
                ['class' => 'game-playground', 'placeholder' => $lang['scm.field']]
            ));
	}

    private static function get_stadium(int $club_id, $lang)
    {
        if ($club_id)
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
            return $stadiums ? $options : [new FormFieldSelectChoiceOption(StringVars::replace_vars($lang['scm.club.no.stadium'], ['club' => $real_club->get_club_name()]), 0)];
        }
        return [new FormFieldSelectChoiceOption($lang['scm.club.no.club'], 0)];
    }

    private static function get_teams_list($event_id)
    {
        $options = [];
        $options[] = new FormFieldSelectChoiceOption('', 0);
        foreach (ScmTeamService::get_teams($event_id) as $team)
        {
			$options[] = new FormFieldSelectChoiceOption($team['club_name'], $team['id_team']);
        }

		return $options;
    }

    private static function get_params($event_id)
	{
        if (!empty($event_id))
        {
            try {
                $params = ScmParamsService::get_params($event_id);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        }
		return $params;
	}
}
?>
