<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmMiniNextGame extends ModuleMiniMenu
{
	public function get_default_block()
	{
		return self::BLOCK_POSITION__RIGHT;
	}

	public function get_menu_id()
	{
		return 'module-mini-next-scm';
	}

	public function get_menu_title()
	{
		return LangLoader::get_message('scm.mini.next', 'common', 'scm');
	}

	public function get_formated_title()
	{
		return LangLoader::get_message('scm.mini.next', 'common', 'scm');
	}

	public function is_displayed()
	{
        $config = ScmConfig::load();
        if ($config->get_homepage() == ScmConfig::GAME_LIST)
            return !Url::is_current_url('/scm/', true) && ScmAuthorizationsService::check_authorizations()->read();
        else
            return !Url::is_current_url('/scm/game_list/') && ScmAuthorizationsService::check_authorizations()->read();
	}

	public function get_menu_content()
	{
		// Create file template
		$view = new FileTemplate('scm/ScmMiniNextGame.tpl');

		// Assign the lang file to the tpl
		$view->add_lang(LangLoader::get_all_langs('scm'));

		// Assign common menu variables to the tpl
		MenuService::assign_positions_conditions($view, $this->get_block());

		$now = new Date();

        $next_games = $next_events = $next_events_games = [];

        $running_events = ScmEventService::get_running_events_id();
        $events_id = $running_events ? implode(', ', $running_events) : 0;

        $results = PersistenceContext::get_querier()->select('SELECT games.*, params.*
            FROM ' . ScmSetup::$scm_game_table . ' games
            LEFT JOIN ' . ScmSetup::$scm_params_table . ' params ON params.params_event_id = games.game_event_id
            WHERE games.game_date > :now
            AND (games.game_home_id = params.favorite_team_id OR games.game_away_id = params.favorite_team_id)
            AND games.game_event_id IN (' . $events_id . ')
            ORDER BY games.game_date', [
                'now' => $now->get_timestamp()
            ]
        );

        while ($row = $results->fetch())
        {
            $next_games[] = $row;
        }
        foreach ($next_games as $game)
        {
            $next_events[$game['game_event_id']][] = $game;
        }

        foreach ($next_events as $games) {
            usort($games, function($a, $b) {
                return $a['game_date'] - $b['game_date'];
            });
            $next_events_games[] = $games[0];
        }

        $view->put_all([
			'C_ITEMS' => count($next_events_games) > 0
		]);

        usort($next_events_games, function($a,$b) {
            return $a['game_date'] - $b['game_date'];
        });

		foreach ($next_events_games as $game)
		{
			$item = new ScmGame();
			$item->set_properties($game);

			$view->assign_block_vars('items', array_merge($item->get_template_vars(), [
                'C_LATE'         => $item->get_game_cluster() < ScmDayService::get_last_day($item->get_game_event_id()),
                'YEAR'           => date('y', $item->get_game_date()->get_timestamp()),
                'C_IS_SUB'       => ScmEventService::is_sub_event($item->get_game_event_id()),
                'MASTER_EVENT'   => ScmEventService::get_master_division($item->get_game_event_id()),
                'U_MASTER_EVENT' => ScmEventService::get_master_url($item->get_game_event_id()),
                'U_EVENT'        => ScmUrlBuilder::event_home($item->get_game_event_id(), ScmEventService::get_event_slug($item->get_game_event_id()))->rel()
			]));
		}

		return $view->render();
	}
}
?>
