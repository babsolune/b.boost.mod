<?php
/**
 * @copyright   &copy; 2005-2026 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.1 - last update: 2026 03 21
 * @since       PHPBoost 6.1 - 2026 03 21
*/

class ScmLobbyProvider extends DefaultModuleLobbyProvider
{
	public function get_module_id(): string
	{
		return 'scm';
	}

	public function has_categories(): bool
	{
		return false;
	}

    public function get_config_fields(LobbyModule $module): array
	{
		return [];
	}

    public function save(HTMLForm $form, LobbyModule $module): void {}

	public function get_view(): FileTemplate
	{
		$module_id     = $this->get_module_id();
		$module        = LobbyModulesList::load()[$module_id];
		$module_config = ScmConfig::load();

		$view = $this->get_lobby_template('main/ScmLobbyProvider.tpl');
		$view->add_lang(array_merge(LangLoader::get_all_langs('lobby'), LangLoader::get_all_langs($module_id)));

		$view->put_all([
			'MODULE_NAME'     => $module_id,
			'MODULE_POSITION' => LobbyConfig::load()->get_module_position_by_id($module_id),
			'L_MODULE_TITLE'  => ModulesManager::get_module($module_id)->get_configuration()->get_name(),
		]);

        $now = new Date();

        $next_games = $next_events = $next_events_games = [];

        $running_events = ScmEventService::get_running_events_id();
        $events_id = $running_events ? implode(', ', $running_events) : 0;

        $results = PersistenceContext::get_querier()->select('
            SELECT games.*, params.*
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
			'C_NEXT_ITEMS' => count($next_events_games) > 0
		]);

        usort($next_events_games, function($a,$b) {
            return $a['game_date'] - $b['game_date'];
        });

		foreach ($next_events_games as $game)
		{
			$item = new ScmGame();
			$item->set_properties($game);

			$view->assign_block_vars('next_items', array_merge(
                $item->get_template_vars(),
                [
                    'C_DELAYED'      => $item->get_game_cluster() < ScmDayService::get_last_day($item->get_game_event_id()),
                    'YEAR'           => date('y', $item->get_game_date()->get_timestamp()),
                    'C_IS_SUB'       => ScmEventService::is_sub_event($item->get_game_event_id()),
                    'MASTER_EVENT'   => ScmEventService::get_master_division($item->get_game_event_id()),
                    'U_MASTER_EVENT' => ScmEventService::get_master_url($item->get_game_event_id()),
                    'U_EVENT'        => ScmUrlBuilder::event_home($item->get_game_event_id(), ScmEventService::get_event_slug($item->get_game_event_id()))->rel()
                ]
            ));
		}

        // prev games
        $prev_events_games = $prev_events = $prev_games = [];

        $running_events = ScmEventService::get_running_events_id();
        $events_id = $running_events ? implode(', ', $running_events) : 0;

        $results = PersistenceContext::get_querier()->select('
            SELECT games.*, params.*
            FROM ' . ScmSetup::$scm_game_table . ' games
            LEFT JOIN ' . ScmSetup::$scm_params_table . ' params ON params.params_event_id = games.game_event_id
            WHERE games.game_date < :now
            AND (games.game_home_id = params.favorite_team_id OR games.game_away_id = params.favorite_team_id)
            AND games.game_event_id IN (' . $events_id . ')
            ORDER BY games.game_date', [
                'now' => $now->get_timestamp()
            ]
        );

        while ($row = $results->fetch())
        {
            if (!empty($row['game_home_id']) && !empty($row['game_away_id']))
                $prev_games[] = $row;
        }
        foreach ($prev_games as $game)
        {
            $prev_events[$game['game_event_id']][] = $game;
        }

        foreach ($prev_events as $games) {
            usort($games, function($a, $b) {
                return $b['game_date'] - $a['game_date'];
            });
            $prev_events_games[] = $games[0];
        }

        $view->put_all([
			'C_PREV_ITEMS' => count($prev_events_games) > 0
		]);

        usort($prev_events_games, function($a,$b) {
            return $a['game_date'] - $b['game_date'];
        });

		foreach ($prev_events_games as $game)
		{
			$item = new ScmGame();
			$item->set_properties($game);

			$view->assign_block_vars('prev_items', array_merge(
                $item->get_template_vars(),
                [
                    'C_LATE'         => $item->get_game_cluster() < ScmDayService::get_last_day($item->get_game_event_id()),
                    'YEAR'           => date('y', $item->get_game_date()->get_timestamp()),
                    'C_IS_SUB'       => ScmEventService::is_sub_event($item->get_game_event_id()),
                    'MASTER_EVENT'   => ScmEventService::get_master_division($item->get_game_event_id()),
                    'U_MASTER_EVENT' => ScmEventService::get_master_url($item->get_game_event_id()),
                    'U_EVENT'        => ScmUrlBuilder::event_home($item->get_game_event_id(), ScmEventService::get_event_slug($item->get_game_event_id()))->rel()
                ]
            ));
		}

		return $view;
	}
}
?>
