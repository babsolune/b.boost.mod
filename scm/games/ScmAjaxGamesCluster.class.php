<?php
/**
 * @copyright   &copy; 2005-2026 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.1 - last update: 2026 07 02
 * @since       PHPBoost 6.1 - 2026 07 02
*/

class ScmAjaxGamesCluster extends AbstractController
{
	public function execute(HTTPRequestCustom $request)
	{
        $request = AppContext::get_request();
        $event_id = $request->get_getint('event_id', 0);
        $type = $request->get_getstring('type', '');
        $cluster = $request->get_getint('cluster', 0);

        $result = PersistenceContext::get_querier()->select('
            SELECT *
            FROM  ' . ScmSetup::$scm_game_table . '
            WHERE game_event_id = ' . $event_id . '
            AND game_type = "' . $type . '"
            AND game_cluster = ' . $cluster
        );
        $response = [];

        while ($row = $result->fetch()) {
            $response[] = [
                'game_id'        => $row['game_type'] . $row['game_cluster'] . $row['game_round'] . $row['game_order'],
                'game_home_team' => $row['game_home_id'] ? ScmTeamService::get_team_name($row['game_home_id']) : '',
                'game_away_team' => $row['game_away_id'] ? ScmTeamService::get_team_name($row['game_away_id']) : '',

                'id_game'             => $row['id_game'],
                'game_event_id'       => $row['game_event_id'],
                'game_type'           => $row['game_type'],
                'game_cluster'        => $row['game_cluster'],
                'game_round'          => $row['game_round'],
                'game_order'          => $row['game_order'],
                'game_playground'     => $row['game_playground'],
                'game_home_id'        => $row['game_home_id'],
                'game_home_score'     => $row['game_home_score'],
                'game_home_pen'       => $row['game_home_pen'],
                'game_home_off_bonus' => $row['game_home_off_bonus'],
                'game_home_def_bonus' => $row['game_home_def_bonus'],
                'game_home_goals'     => TextHelper::serialize($row['game_home_goals']),
                'game_home_yellow'    => TextHelper::serialize($row['game_home_yellow']),
                'game_home_red'       => TextHelper::serialize($row['game_home_red']),
                'game_home_empty'     => $row['game_home_empty'],
                'game_home_forfeit'   => $row['game_home_forfeit'],
                'game_away_id'        => $row['game_away_id'],
                'game_away_score'     => $row['game_away_score'],
                'game_away_pen'       => $row['game_away_pen'],
                'game_away_off_bonus' => $row['game_away_off_bonus'],
                'game_away_def_bonus' => $row['game_away_def_bonus'],
                'game_away_goals'     => TextHelper::serialize($row['game_away_goals']),
                'game_away_yellow'    => TextHelper::serialize($row['game_away_yellow']),
                'game_away_red'       => TextHelper::serialize($row['game_away_red']),
                'game_away_empty'     => $row['game_away_empty'],
                'game_away_forfeit'   => $row['game_away_forfeit'],
                'game_date'           => $row['game_date'] !== null ? $row['game_date'] : 0,
                'game_video'          => $row['game_video'],
                'game_summary'        => $row['game_summary'],
                'game_status'         => $row['game_status'],
                'game_stadium'        => $row['game_stadium'],
                'game_stadium_name'   => $row['game_stadium_name'],
            ];
        }

		return new JSONResponse($response);
	}
}

?>