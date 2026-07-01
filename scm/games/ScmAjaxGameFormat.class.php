<?php
/**
 * @copyright   &copy; 2005-2026 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.1 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmAjaxGameFormat extends AbstractController
{
	public function execute(HTTPRequestCustom $request)
	{
        $request = AppContext::get_request();
        $event_id = $request->get_postint('event_id', 0);
        $result = PersistenceContext::get_querier()->select('
            SELECT *
            FROM  ' . ScmSetup::$scm_game_table . '
            WHERE game_event_id = ' . $event_id
        );
        $response = [];

        while ($row = $result->fetch()) {
            $response[] = [
                'game_id' => $row['game_type'] . $row['game_cluster'] . $row['game_round'] . $row['game_order'],
                'home_score' => $row['game_home_score'],
                'away_score' => $row['game_away_score'],
            ];
        }

		return new JSONResponse($response);
	}
}

?>