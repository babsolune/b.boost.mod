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

    public static function build_group_calendar(int $compet_id)
    {
        $view = new FileTemplate('football/FootballGroupCalendarController.tpl');
        $view->add_lang(LangLoader::get_all_langs('football'));

        $results = self::$db_querier->select('SELECT matches.*, compet.*
            FROM ' . FootballSetup::$football_match_table . ' matches
            LEFT JOIN ' . FootballSetup::$football_compet_table . ' compet ON compet.id_compet = matches.match_compet_id
            WHERE matches.match_compet_id = :id
            ORDER BY matches.match_date ASC, matches.match_field ASC', array(
                'id' => $compet_id
            )
        );

        $view->put_all(array(
            'C_MATCHES' => $results->get_rows_count() > 0
        ));

        while($row = $results->fetch())
        {
            $home_has_score = !empty($row['match_home_team_score']) && TextHelper::strlen($row['match_home_team_score']) > 0 ? TextHelper::strlen($row['match_home_team_score']) > 0 : '';
            $visit_has_score = !empty($row['match_home_team_score']) && TextHelper::strlen($row['match_visit_team_score']) > 0 ? TextHelper::strlen($row['match_visit_team_score']) > 0 : '';
            $view->assign_block_vars('matches',array_merge(
				Date::get_array_tpl_vars(new Date($row['match_date'], Timezone::SERVER_TIMEZONE), 'match_date'),
                array(
                    'C_HAS_SCORE' => $home_has_score && $visit_has_score,
                    'PLAYGROUND' => $row['match_field'],
                    'HOME_TEAM' => $row['match_home_team_id'] ? FootballTeamService::get_team($row['match_home_team_id'])->get_team_club_name() : '',
                    'HOME_SCORE' => $row['match_home_team_score'],
                    'VISIT_SCORE' => $row['match_visit_team_score'],
                    'VISIT_TEAM' => $row['match_home_team_id'] ? FootballTeamService::get_team($row['match_visit_team_id'])->get_team_club_name() : ''
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
            ORDER BY matches.match_date ASC, matches.match_field ASC', array(
                'id' => $compet_id
            )
        );

        $view->put_all(array(
            'C_MATCHES' => $results->get_rows_count() > 0
        ));

        while($row = $results->fetch())
        {
            $view->assign_block_vars('matches',array_merge(
				Date::get_array_tpl_vars(new Date($row['match_date'], Timezone::SERVER_TIMEZONE), 'match_date'),
                array(
                    'PLAYGROUND' => $row['match_field'],
                    'HOME_TEAM' => $row['match_home_team_id'] ? FootballTeamService::get_team($row['match_home_team_id'])->get_team_club_name(): '',
                    'HOME_SCORE' => $row['match_home_team_score'],
                    'VISIT_SCORE' => $row['match_visit_team_score'],
                    'VISIT_TEAM' => $row['match_home_team_id'] ? FootballTeamService::get_team($row['match_visit_team_id'])->get_team_club_name(): ''
                )
            ));
        }
        return $view;
    }
}
?>
