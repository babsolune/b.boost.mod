<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballCompetCalendarService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

    public static function build_tourney_calendar(int $compet_id)
    {
        $view = new FileTemplate('football/FootballTourneyCalendarController.tpl');
        $view->add_lang(LangLoader::get_all_langs('football'));

        $results = self::$db_querier->select('SELECT matches.*, compet.*
            FROM ' . FootballSetup::$football_match_table . ' matches
            LEFT JOIN ' . FootballSetup::$football_compet_table . ' compet ON compet.id_compet = matches.match_compet_id
            WHERE matches.match_compet_id = :id
            ORDER BY matches.match_date ASC, matches.match_playground ASC', array(
                'id' => $compet_id
            )
        );

        $view->put_all(array(
            'C_PLAYGROUNDS' => FootballParamsService::get_params($compet_id)->get_display_playgrounds(),
            'C_MATCHES' => $results->get_rows_count() > 0,
            'C_ONE_DAY' => FootballMatchService::one_day_compet($compet_id),
            'TEAMS_NUMBER' => FootballTeamService::get_compet_teams_number($compet_id),
            'TEAMS_PER_GROUP' => FootballParamsService::get_params($compet_id)->get_teams_per_group()
        ));

        while($row = $results->fetch())
        {
            $c_home_score = $row['match_home_score'] != '';
            $c_home_pen   = $row['match_home_pen'] != '';
            $c_away_pen   = $row['match_away_pen'] != '';
            $c_away_score = $row['match_away_score'] != '';
            $items = TextHelper::substr($row['match_number'], 0, 1) == 'G' ? 'groups' : 'finals';

            $view->assign_block_vars($items,array_merge(
				Date::get_array_tpl_vars(new Date($row['match_date'], Timezone::SERVER_TIMEZONE), 'match_date'),
                array(
                    'C_HAS_SCORE' => $c_home_score && $c_away_score,
                    'C_HAS_PEN' => $c_home_pen && $c_away_pen,
                    'ID' => $row['match_number'],
                    'PLAYGROUND' => $row['match_playground'],
                    'HOME_TEAM' => $row['match_home_id'] ? FootballTeamService::get_team($row['match_home_id'])->get_team_club_name() : '',
                    'HOME_SCORE' => $row['match_home_score'],
                    'HOME_PEN' => $row['match_home_pen'],
                    'AWAY_PEN' => $row['match_away_pen'],
                    'AWAY_SCORE' => $row['match_away_score'],
                    'AWAY_TEAM' => $row['match_home_id'] ? FootballTeamService::get_team($row['match_away_id'])->get_team_club_name() : ''
                )
            ));
        }
        return $view;
    }

    public static function build_group_finals_list(int $compet_id)
    {
        $view = new FileTemplate('football/FootballGroupMatchesController.tpl');
        $view->add_lang(LangLoader::get_all_langs('football'));

        $results = self::$db_querier->select('SELECT matches.*, compet.*
            FROM ' . FootballSetup::$football_match_table . ' matches
            LEFT JOIN ' . FootballSetup::$football_compet_table . ' compet ON compet.id_compet = matches.match_compet_id
            WHERE matches.match_compet_id = :id
            ORDER BY matches.match_date ASC, matches.match_playground ASC', array(
                'id' => $compet_id
            )
        );

        $view->put_all(array(
            'C_PLAYGROUNDS' => FootballParamsService::get_params($compet_id)->get_display_playgrounds(),
            'C_MATCHES' => $results->get_rows_count() > 0,
            'C_ONE_DAY' => FootballMatchService::one_day_compet($compet_id),
            'TEAMS_NUMBER' => FootballTeamService::get_compet_teams_number($compet_id),
            'TEAMS_PER_GROUP' => FootballParamsService::get_params($compet_id)->get_teams_per_group()
        ));

        while($row = $results->fetch())
        {
            $view->assign_block_vars('matches',array_merge(
				Date::get_array_tpl_vars(new Date($row['match_date'], Timezone::SERVER_TIMEZONE), 'match_date'),
                array(
                    'ID' => $row['match_number'],
                    'PLAYGROUND' => $row['match_playground'],
                    'HOME_TEAM' => $row['match_home_id'] ? FootballTeamService::get_team($row['match_home_id'])->get_team_club_name(): '',
                    'HOME_SCORE' => $row['match_home_score'],
                    'AWAY_SCORE' => $row['match_away_score'],
                    'AWAY_TEAM' => $row['match_home_id'] ? FootballTeamService::get_team($row['match_away_id'])->get_team_club_name(): ''
                )
            ));
        }
        return $view;
    }
}
?>
