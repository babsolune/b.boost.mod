<?php
/**
 * @copyright   &copy; 2005-2026 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.1 - last update: 2026 07 02
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmAjaxGameForm extends AbstractController
{
    private $form;
    private $submit_button;

    private $params;
    private $game;
    private $event_id;
    private $game_type;
    private $game_cluster;
    private $game_round;
    private $game_order;

    public function execute(HTTPRequestCustom $request)
	{
        AppContext::get_session()->csrf_get_protect();
        $this->init($request);

        if($request->get_poststring('action', '') == 'call')
        {
            $result = PersistenceContext::get_querier()->select('
                SELECT *
                FROM  ' . ScmSetup::$scm_game_table . '
                WHERE game_event_id = ' . $this->event_id . '
                AND game_type = "' . $this->game_type . '"
                AND game_cluster = "' . $this->game_cluster . '"
                AND game_round = "' . $this->game_round . '"
                AND game_order = "' . $this->game_order . '"'
            );
            $response = [];

            while ($row = $result->fetch()) {
                $date = new DateTime('@' . $row['game_date']);
                $date->setTimezone(new DateTimeZone(GeneralConfig::load()->get_site_timezone()));
                $response[] = [
                    'game_id' => $row['game_type'] . $row['game_cluster'] . $row['game_round'] . $row['game_order'],
                    'game_cluster' => $row['game_cluster'],
                    'playground' => $row['game_playground'],
                    'home_name' => ScmTeamService::get_team_name($row['game_home_id']),
                    'home_score' => $row['game_home_score'],
                    'home_pen' => $row['game_home_pen'],
                    'away_pen' => $row['game_away_pen'],
                    'away_score' => $row['game_away_score'],
                    'away_name' => ScmTeamService::get_team_name($row['game_away_id']),
                    'date' => $date->format('Y-m-d\TH:i'),
                    'timestamp' => gmdate('Y-m-d\TH:i', $row['game_date']),
                    'team_list' => $this->get_teams_list($this->event_id, $this->game_type, $this->game_cluster)
                ];
            }

            return new JSONResponse($response);
        }

        if($request->get_poststring('action', '') == 'validate')
        {
            $response[] = $request;
            $item = $this->get_game($this->game_type , $this->game_cluster, $this->game_round, $this->game_order);
            $date = new Date($request->get_value('date'));
            $item->set_game_date($date);
            if ($this->get_params($this->event_id)->get_display_playgrounds())
                $item->set_game_playground($request->get_poststring('playground', ''));
            $item->set_game_home_id((int)$request->get_postint('home_id', ''));
            $item->set_game_home_score($request->get_postint('home_score'));
            if ($this->game_type == 'B' && $request->get_postint('home_pen', 0) && $request->get_postint('away_pen', 0))
            {
                $item->set_game_home_pen($request->get_postint('home_pen', 0));
                $item->set_game_away_pen($request->get_postint('away_pen', 0));
            }
            $item->set_game_away_score($request->get_postint('away_score'));
            $item->set_game_away_id((int)$request->get_postint('away_id'));

            ScmGameService::update_game($item, $item->get_id_game());
            return new JSONResponse($response);
        }
	}

	private function get_game($type, $cluster, $round, $order)
	{
        try {
            $this->game = ScmGameService::get_game($this->event_id, $type, $cluster, $round, $order);
        } catch (RowNotFoundException $e) {
            $error_controller = PHPBoostErrors::unexisting_page();
            DispatchManager::redirect($error_controller);
        }
		return $this->game;
	}

    private function get_params($event_id)
	{
        if (!empty($event_id))
        {
            try {
                $this->params = ScmParamsService::get_params($event_id);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        }
		return $this->params;
	}

    private function init(HTTPRequestCustom $request)
    {
        $this->event_id = $request->get_postint('event_id', 0);
        $this->game_type    = $request->get_poststring('game_type');
        $this->game_cluster = $request->get_postint('game_cluster');
        $this->game_round   = $request->get_postint('game_round');
        $this->game_order   = $request->get_postint('game_order');
    }

    private function get_teams_list($event_id, $type, $cluster)
    {
        if ($type == 'G')
        {
            $teams_list = [];
            foreach (ScmTeamService::get_teams($event_id) as $team)
            {
                $team_group = $team['team_group'];
                if ($team_group == $cluster)
                    $teams_list[] = $team;
            }
            $options = [];

            $clubs = ScmClubCache::load();
            $options[] = [
                'name' => '',
                'id' => 0
            ];
            foreach($teams_list as $team)
            {
                $options[] = [
                    'name' => $clubs->get_club_name($team['team_club_id']),
                    'id' => $team['id_team']
                ];
            }

            return $options;
        }
        else
        {
            $options = [];
            $clubs = ScmClubCache::load();
            $options[] = [
                'name' => '',
                'id' => 0
            ];
            foreach (ScmTeamService::get_teams($event_id) as $team)
            {
                $options[] = [
                    'name' => $clubs->get_club_name($team['team_club_id']),
                    'id' => $team['id_team']
                ];
            }

            return $options;
        }
    }
}

?>