<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballTourneyService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

    public static function build_finals_match($compet_id, $bracket, $round, $match)
    {
        $view = new FileTemplate('football/FootballTourneyFinalsMatchController.tpl');
        $view->add_lang(LangLoader::get_all_langs('football'));

        $results = self::$db_querier->select('SELECT matches.*, compet.*
            FROM ' . FootballSetup::$football_match_table . ' matches
            LEFT JOIN ' . FootballSetup::$football_compet_table . ' compet ON compet.id_compet = matches.match_compet_id
            WHERE matches.match_compet_id = :id
            AND matches.match_number = :match_number', array(
                'id' => $compet_id,
                'match_number' => $bracket.$round.$match
            )
        );

        while($row = $results->fetch())
        {
            $c_home_score = $row['match_home_score'] != '';
            $c_home_pen   = $row['match_home_pen'] != '';
            $c_away_pen   = $row['match_away_pen'] != '';
            $c_away_score = $row['match_away_score'] != '';

            $view->put_all(array_merge(
				Date::get_array_tpl_vars(new Date($row['match_date'], Timezone::SERVER_TIMEZONE), 'match_date'),
                array(
                    'C_ONE_DAY' => FootballMatchService::one_day_compet($compet_id),
                    'C_MATCH' => $row['match_home_id'] || $row['match_away_id'],
                    'C_HAS_SCORE' => $c_home_score && $c_away_score,
                    'C_HAS_PEN' => $c_home_pen && $c_away_pen,
                    'C_HOME_WIN' => $row['match_home_score'] > $row['match_away_score'] || $row['match_home_pen'] > $row['match_away_pen'],
                    'C_AWAY_WIN' => $row['match_home_score'] < $row['match_away_score'] || $row['match_home_pen'] < $row['match_away_pen'],
                    'WIN_COLOR' => FootballParamsService::get_params($compet_id)->get_promotion_color(),
                    'MATCH_ID' => $row['match_number'],
                    'PLAYGROUND' => $row['match_playground'],
                    'HOME_ID' => $row['match_home_id'],
                    'HOME_TEAM' => $row['match_home_id'] ? FootballTeamService::get_team($row['match_home_id'])->get_team_club_name() : '',
                    'HOME_SCORE' => $row['match_home_score'],
                    'HOME_PEN' => $row['match_home_pen'],
                    'AWAY_PEN' => $row['match_away_pen'],
                    'AWAY_SCORE' => $row['match_away_score'],
                    'AWAY_TEAM' => $row['match_away_id'] ? FootballTeamService::get_team($row['match_away_id'])->get_team_club_name() : '',
                    'AWAY_ID' => $row['match_away_id']
                )
            ));
        }
        return $view;
    }

    public static function build_finals_js_match($compet_id, $bracket, $round, $match)
    {
        $view = new FileTemplate('football/FootballTourneyFinalsMatchJsController.tpl');
        $view->add_lang(LangLoader::get_all_langs('football'));

        $results = self::$db_querier->select('SELECT matches.*, compet.*
            FROM ' . FootballSetup::$football_match_table . ' matches
            LEFT JOIN ' . FootballSetup::$football_compet_table . ' compet ON compet.id_compet = matches.match_compet_id
            WHERE matches.match_compet_id = :id
            AND matches.match_number = :match_number', array(
                'id' => $compet_id,
                'match_number' => $bracket.$round.$match
            )
        );

        while($row = $results->fetch())
        {
            $view->put_all(array_merge(
				Date::get_array_tpl_vars(new Date($row['match_date'], Timezone::SERVER_TIMEZONE), 'match_date'),
                array(
                    'C_MATCH' => $row['match_home_id'] || $row['match_away_id'],
                    'C_HOME_SCORE' => $row['match_home_score'] != '',
                    'C_HOME_PEN' => $row['match_home_pen'] != '',
                    'C_AWAY_SCORE' => $row['match_away_score'] != '',
                    'C_AWAY_PEN' => $row['match_away_pen'] != '',

                    'ID' => $row['match_number'],
                    'PLAYGROUND' => $row['match_playground'],
                    'HOME_ID' => $row['match_home_id'],
                    'HOME_TEAM' => $row['match_home_id'] ? FootballTeamService::get_team($row['match_home_id'])->get_team_club_name() : '',
                    'HOME_SCORE' => $row['match_home_score'],
                    'HOME_PEN' => $row['match_home_pen'],
                    'AWAY_PEN' => $row['match_away_pen'],
                    'AWAY_SCORE' => $row['match_away_score'],
                    'AWAY_TEAM' => $row['match_home_id'] ? FootballTeamService::get_team($row['match_away_id'])->get_team_club_name() : '',
                    'AWAY_ID' => $row['match_home_id']
                )
            ));
        }
        return $view;
    }
}
?>
