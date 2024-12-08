<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmMiniPrevGame extends ModuleMiniMenu
{
	public function get_default_block()
	{
		return self::BLOCK_POSITION__RIGHT;
	}

	public function get_menu_id()
	{
		return 'module-mini-prev-scm';
	}

	public function get_menu_title()
	{
		return LangLoader::get_message('scm.mini.prev', 'common', 'scm');
	}

	public function get_formated_title()
	{
		return LangLoader::get_message('scm.mini.prev', 'common', 'scm');
	}

	public function is_displayed()
	{
		return ScmAuthorizationsService::check_authorizations()->read();
	}

	public function get_menu_content()
	{
		// Create file template
		$view = new FileTemplate('scm/ScmMiniPrevGame.tpl');

		// Assign the lang file to the tpl
		$view->add_lang(LangLoader::get_all_langs('scm'));

		// Assign common menu variables to the tpl
		MenuService::assign_positions_conditions($view, $this->get_block());
        $now = new Date();

        $cache = ScmGameCache::load();
        $prev_events = $prev_matchdays = $event_games = [];

        $prev_matchdays = array_filter($cache->get_games(), function($game) use ($now) {
            $is_sub = ScmEventService::get_event($game['game_event_id'])->get_is_sub();
            $real_event_id = $is_sub ? ScmEventService::get_event($game['game_event_id'])->get_master_id() : $game['game_event_id'];
            $is_last_event_id = $is_sub ? ScmEventService::is_last_sub($real_event_id, $game['game_event_id']) : 0;
            if ($is_sub && $is_last_event_id)
                return ( $game['game_date'] < $now->get_timestamp() && $now->get_timestamp() < ScmEventService::get_event($real_event_id)->get_end_date()->get_timestamp());
            else
                return ($now->get_timestamp() < ScmEventService::get_event($game['game_event_id'])->get_end_date()->get_timestamp()) && $game['game_date'] < $now->get_timestamp();
        });

        foreach ($prev_matchdays as $game)
        {
            $favorite_team = ScmParamsService::get_params($game['game_event_id'])->get_favorite_team_id();
            $event_id = $game['game_event_id'];
            if (!isset($event_games[$event_id])) {
                $event_games[$event_id] = [];
            }
            if ($favorite_team && ($game['game_home_id'] == $favorite_team || $game['game_away_id'] == $favorite_team))
                $event_games[$event_id][] = $game;
        }

        foreach ($event_games as $games) {
            usort($games, function($a, $b) {
                return $a['game_date'] - $b['game_date'];
            });
            $prev_events[] = end($games);
        }

        $view->put_all([
			'C_ITEMS' => count($prev_events) > 0
		]);

        usort($prev_events, function($a,$b) {
            if(isset($a['game_date']) && isset($b['game_date']))
                return $a['game_date'] - $b['game_date'];
        });

		foreach ($prev_events as $game)
		{
            if(isset($game['game_home_id']) && isset($game['game_away_id']))
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
		}

		return $view->render();
	}
}
?>
