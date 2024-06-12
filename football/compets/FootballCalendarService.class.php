<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballCalendarService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

    public static function build_rounds_calendar(int $compet_id)
    {
        $view = new FileTemplate('football/FootballRoundsCalendarController.tpl');
        $view->add_lang(LangLoader::get_all_langs('football'));

        $results = self::$db_querier->select('SELECT matches.*, compet.*
            FROM ' . FootballSetup::$football_match_table . ' matches
            LEFT JOIN ' . FootballSetup::$football_compet_table . ' compet ON compet.id_compet = matches.match_compet_id
            WHERE matches.match_compet_id = :id
            ORDER BY matches.match_date ASC, matches.match_playground ASC, matches.match_group DESC, matches.match_order ASC', array(
                'id' => $compet_id
            )
        );

        $view->put_all(array(
            'C_PLAYGROUNDS' => FootballParamsService::get_params($compet_id)->get_display_playgrounds(),
            'C_HAS_MATCHES' => FootballMatchService::has_matches($compet_id),
            'C_ONE_DAY' => FootballMatchService::one_day_compet($compet_id),
            'TEAMS_NUMBER' => FootballTeamService::get_teams_number($compet_id),
            'TEAMS_PER_GROUP' => FootballParamsService::get_params($compet_id)->get_teams_per_group()
        ));

        while($row = $results->fetch())
        {
            $match = new FootballMatch();
            $match->set_properties($row);
            $items = $row['match_type'] == 'G' ? 'groups' : 'bracket';

            $view->assign_block_vars($items, $match->get_array_tpl_vars());
        }
        return $view;
    }
}
?>
