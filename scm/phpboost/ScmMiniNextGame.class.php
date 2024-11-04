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
		return 'module-mini-scm';
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
		return ScmAuthorizationsService::check_authorizations()->read();
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
        $games = [];
        $result_rounds = PersistenceContext::get_querier()->select('SELECT game.*, params.*
            FROM ' . ScmSetup::$scm_game_table . ' game
            LEFT JOIN ' . ScmSetup::$scm_params_table . ' params ON params.params_event_id = game.game_event_id
            WHERE game.game_date > ' . $now->get_timestamp() . '
            AND (game.game_home_id = params.favorite_team_id OR game.game_away_id = params.favorite_team_id)
            AND (game_type = "G" OR game_type = "B")
            ORDER BY game.game_date ASC'
        );
        foreach ($result_rounds as $game)
        {
            if (
                ($game['hat_ranking'] && $game['game_group'] == ScmGroupService::get_next_matchday_hat($game['game_event_id']))
                || (!$game['hat_ranking'] && $game['game_round'] == ScmGroupService::get_next_matchday_group($game['game_event_id']))
                && ScmEventService::get_event($game['game_event_id'])->get_end_date()->get_timestamp() > $now->get_timestamp()
            )
            {
                $games[] = $game;
            }
        }

        $result_days = PersistenceContext::get_querier()->select('SELECT game.*, params.*
            FROM ' . ScmSetup::$scm_game_table . ' game
            LEFT JOIN ' . ScmSetup::$scm_params_table . ' params ON params.params_event_id = game.game_event_id
            WHERE game.game_date > ' . $now->get_timestamp() . '
            AND (game.game_home_id = params.favorite_team_id OR game.game_away_id = params.favorite_team_id)
            AND (game_type = "D")
            ORDER BY game.game_date ASC'
        );
        foreach ($result_days as $game)
        {
            if (
                $game['game_group'] == ScmDayService::get_next_day($game['game_event_id']) // Select last match day
                && ScmEventService::get_event($game['game_event_id'])->get_end_date()->get_timestamp() > $now->get_timestamp() // Remove ended events
            )
            {
                $games[] = $game;
            }
        }

        $view->put_all([
			'C_ITEMS' => count($games) > 0
		]);

        usort($games, function($a,$b) {
            return $a['game_date'] - $b['game_date'];
        });

		foreach ($games as $game)
		{
			$item = new ScmGame();
			$item->set_properties($game);

			$view->assign_block_vars('items', array_merge($item->get_template_vars(), [
                'U_EVENT' => ScmUrlBuilder::event_home($item->get_game_event_id(), ScmEventService::get_event_slug($item->get_game_event_id()))->rel()
			]));

		}

		return $view->render();
	}
}
?>
