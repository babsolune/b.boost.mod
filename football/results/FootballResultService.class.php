<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballResultService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

    public static function add_result($compet_id, $match_id, $group_id, $team_id, $team_score, $goals_against)
    {
        $result = self::$db_querier->insert(FootballSetup::$football_result_table, array(
            'id_result' => null,
            'result_compet_id' => $compet_id,
            'result_match_id' => $match_id,
            'result_group_id' => $group_id,
            'result_team_id' => $team_id,
            'result_goals_for' => $team_score,
            'result_goals_against' => $goals_against,
        ));

		return $result->get_last_inserted_id();
    }

    public static function update_result($team_id, $team_score, $goals_against)
    {
        self::$db_querier->update(FootballSetup::$football_result_table, array('result_goals_for' => $team_score, 'result_goals_against' => $goals_against), 'WHERE result_team_id = :id', array('id' => $team_id));
    }

    public static function has_score($match_id, $team_id)
    {
        $result = self::$db_querier->select('SELECT results.*
		FROM ' . FootballSetup::$football_result_table . ' results
		WHERE results.result_match_id = :match
        AND results.result_team_id = :team', array(
            'match' => $match_id,
            'team' => $team_id
        ));

        while ($row = $result->fetch())
        {
            return $row['result_goals_for'];
        }
    }
}
?>
