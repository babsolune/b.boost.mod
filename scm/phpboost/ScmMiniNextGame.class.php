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

		// Load module config
		$config = ScmConfig::load();

		// Load module cache
		$scm_cache = ScmGameCache::load();

		// Load categories cache
		$categories_cache = CategoriesService::get_categories_manager('scm')->get_categories_cache();

		$items = $scm_cache->get_games();

		$view->put_all([
			'C_ITEMS' => !empty($items)
		]);

        $now = new Date();
        $full_games = ScmGameCache::load()->get_games();
        usort($full_games, function($a, $b) {
            return $a['game_date'] - $b['game_date'];
        });
        $games = [];
        foreach ($full_games as $game)
        {
            $params = ScmParamsService::get_params($game['game_event_id']);
            $favorite_team = $params->get_favorite_team_id();
            if (
                $game['game_group'] == ScmDayService::get_next_day($game['game_event_id'])
                && (ScmEventService::get_event($game['game_event_id'])->get_end_date()->get_timestamp() > $now->get_timestamp())
                && ($game['game_home_id'] == $favorite_team || $game['game_away_id'] == $favorite_team)
            )
                $games[] = $game;
        }

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
