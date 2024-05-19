<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballRankingService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

    public static function add_ranking($compet_id, $team_id)
    {
        $result = self::$db_querier->insert(FootballSetup::$football_ranking_table, array(
            'id_ranking' => null,
            'ranking_compet_id' => $compet_id,
            'ranking_team_id' => $team_id,
        ));

		return $result->get_last_inserted_id();
    }

	public static function delete_ranking($team_id)
	{
		if (AppContext::get_current_user()->is_readonly())
        {
            $controller = PHPBoostErrors::user_in_read_only();
            DispatchManager::redirect($controller);
        }
		self::$db_querier->delete(FootballSetup::$football_ranking_table, 'WHERE ranking_team_id = :id', array('id' => $team_id));
	}

    public static function get_group_ranking($compet_id, $group_id, $team_id)
    {
        $result = self::$db_querier->select('SELECT results.*
		FROM ' . FootballSetup::$football_result_table . ' results
		WHERE results.result_compet_id = :compet
        AND results.result_team_id = :team_id', array(
            'compet' => $compet_id,
            'team_id' => $team_id
        ));

        $points = $played = $win = $draw = $loss = $goals_for = $goals_against = [];
        foreach ($result as $row)
        {
            if ($row['result_goals_for'] > $row['result_goals_against'])
            {
                $points[] = FootballParamsService::get_params($compet_id)->get_victory_points();
                $win[] = 1;
            }
            elseif ($row['result_goals_for'] == $row['result_goals_against'])
            {
                $points[] = FootballParamsService::get_params($compet_id)->get_draw_points();
                $draw[] = 1;
            }
            elseif ($row['result_goals_for'] < $row['result_goals_against'])
            {
                $points[] = FootballParamsService::get_params($compet_id)->get_loss_points();
                $loss[] = 1;
            }
            $played[] = 1;
            $goals_for[] = $row['result_goals_for'];
            $goals_against[] = $row['result_goals_against'];
        }
    }

    public static function build_ranking_group($compet_id)
    {
        // Debug::dump(FootballGroupService::group_teams_lists($compet_id));
    }
}
?>
