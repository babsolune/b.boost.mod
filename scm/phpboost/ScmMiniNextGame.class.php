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

        $cache = ScmGameCache::load();
        $next_events = $next_matchdays = $event_games = [];

        $next_matchdays = array_filter($cache->get_games(), function($game) use ($now) {
            return $game['game_date'] > $now->get_timestamp();
        });

        foreach ($next_matchdays as $game)
        {
            $event_id = $game['game_event_id'];
            $favorite_team = ScmParamsService::get_params($event_id)->get_favorite_team_id();
            if ($game['game_home_id'] == $favorite_team || $game['game_away_id'] == $favorite_team)
                $event_games[$event_id][] = $game;
        }

        foreach ($event_games as $games) {
            usort($games, function($a, $b) {
                return $a['game_date'] - $b['game_date'];
            });
            $next_events[] = $games[0];
        }

        $view->put_all([
			'C_ITEMS' => count($next_events) > 0
		]);

        usort($next_events, function($a,$b) {
            return $a['game_date'] - $b['game_date'];
        });

		foreach ($next_events as $game)
		{
			$item = new ScmGame();
			$item->set_properties($game);

			$view->assign_block_vars('items', array_merge($item->get_template_vars(), [
                'YEAR' => date('y', $item->get_game_date()->get_timestamp()),
                'U_EVENT' => ScmUrlBuilder::event_home($item->get_game_event_id(), ScmEventService::get_event_slug($item->get_game_event_id()))->rel()
			]));
		}

		return $view->render();
	}
}
?>
