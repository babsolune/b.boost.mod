<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmAroundGamesController extends DefaultModuleController
{
	private $category;

	protected function get_template_to_use()
	{
		return new FileTemplate('scm/main/ScmAroundGamesController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_view($request);

		return $this->generate_response();
	}

	private function build_view(HTTPRequestCustom $request)
	{
		$now = new Date();

        $this->view->put_all([
            'C_CURRENT_GAMES_CONFIG' => ScmConfig::load()->get_current_games() && ScmConfig::load()->get_homepage() == ScmConfig::GAME_LIST,
            'CURRENT_GAMES' => ScmCurrentGamesService::display_current_games()
        ]);

		$now = new Date();
        $running_events = ScmEventService::get_running_events_id();
        $events_id = $running_events ? implode(', ', $running_events) : 0;

        // Next games
        $next_games = $next_events = $next_events_games = $next_categories = [];

        $next_results = PersistenceContext::get_querier()->select('SELECT games.*, params.*
            FROM ' . ScmSetup::$scm_game_table . ' games
            LEFT JOIN ' . ScmSetup::$scm_params_table . ' params ON params.params_event_id = games.game_event_id
            WHERE games.game_date > :now
            AND (games.game_home_id = params.favorite_team_id OR games.game_away_id = params.favorite_team_id)
            AND games.game_event_id IN (' . $events_id . ')
            ORDER BY games.game_date', [
                'now' => $now->get_timestamp()
            ]
        );

        while ($row = $next_results->fetch())
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

        $this->view->put_all([
			'C_NEXT_ITEMS' => count($next_events_games) > 0,
            'NEXT_ITEMS' => ScmGameFormat::format_categories($next_events_games)
		]);

        // Previous games
        $prev_games = $prev_events = $prev_events_games = $prev_categories = [];

        $prev_results = PersistenceContext::get_querier()->select('SELECT games.*, params.*
            FROM ' . ScmSetup::$scm_game_table . ' games
            LEFT JOIN ' . ScmSetup::$scm_params_table . ' params ON params.params_event_id = games.game_event_id
            WHERE games.game_date < :now
            AND (games.game_home_id = params.favorite_team_id OR games.game_away_id = params.favorite_team_id)
            AND games.game_event_id IN (' . $events_id . ')
            ORDER BY games.game_date', [
                'now' => $now->get_timestamp()
            ]
        );

        while ($row = $prev_results->fetch())
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

        $this->view->put_all([
			'C_PREV_ITEMS' => count($prev_events_games) > 0,
            'PREV_ITEMS' => ScmGameFormat::format_categories($prev_events_games)
		]);
	}

	private function get_category()
	{
		if ($this->category === null)
		{
			$id = AppContext::get_request()->get_getint('id_category', 0);
			if (!empty($id))
			{
				try {
					$this->category = CategoriesService::get_categories_manager('scm')->get_categories_cache()->get_category($id);
				} catch (CategoryNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->category = CategoriesService::get_categories_manager('scm')->get_categories_cache()->get_category(Category::ROOT_CATEGORY);
			}
		}
		return $this->category;
	}

	private function check_authorizations()
	{
        if (!ScmAuthorizationsService::check_authorizations($this->get_category()->get_id())->read())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
        }
	}

	private function generate_response()
	{
		$response = new SiteDisplayResponse($this->view);
        $config = ScmConfig::load();

		$graphical_environment = $response->get_graphical_environment();
        $graphical_environment->set_page_title($this->lang['scm.around.games'], $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name());
		$description = StringVars::replace_vars($this->lang['scm.seo.description.game.list'], ['site' => GeneralConfig::load()->get_site_name()]);
		$graphical_environment->get_seo_meta_data()->set_description($description);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name()));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());
        if ($config->get_homepage() != ScmConfig::GAME_LIST)
		$breadcrumb->add($this->lang['scm.around.games'], ScmUrlBuilder::display_game_list());

		return $response;
	}

	public static function get_view()
	{
		$object = new self('scm');
		$object->check_authorizations();
		$object->build_view(AppContext::get_request());
		return $object->view;
	}
}
?>
