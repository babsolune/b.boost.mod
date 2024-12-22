<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmGroupService
{
	private static $db_querier;
	protected static $module_id = 'scm';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	/** number to letter */
	public static function ntl(int $number) : string
    {
        return chr(64 + $number);
    }

    /** Add all games of all days of the $event_id event when it's a hat ranking event into database */
    public static function set_hat_days_games(int $event_id, int $days_number, int $teams_number)
    {
        for ($day = 1; $day <= $days_number; $day++)
        {
            for ($game = 1; $game <= ($teams_number / 2); $game++)
            {
                self::$db_querier->insert(ScmSetup::$scm_game_table, [
                    'game_event_id' => $event_id,
                    'game_type'     => 'G',
                    'game_cluster'  => $day,
                    'game_round'    => 0,
                    'game_order'    => $game,
                    'game_home_id'  => 0,
                    'game_away_id'  => 0,
                    'game_date'     => ScmEventService::get_event($event_id)->get_start_date()->get_timestamp()
                ]);
            }
        }
    }

    /** Set all games of all groups */
    public static function set_groups_games(int $event_id) : void
    {
        $c_return_games = ScmEventService::get_event_game_type($event_id) == ScmDivision::RETURN_GAMES;
        // build groups from event teams list
        $groups = self::get_group_teams_list($event_id);
        // Build schedule
        $full_schedule = [];
        foreach ($groups as $index => $teams) {
            if (count($teams) % 2 != 0) {
                $teams[] = ['id_team' => 0];
            }
            $schedule = self::build_group_games($teams);
            $full_schedule[$index] = $schedule;
        }
        // Build game list
        foreach ($full_schedule as $group => $schedule) {
            $game_order = $game_round = 1;
            foreach ($schedule as $round => $games) {
                foreach ($games as $i => $game) {
                    self::$db_querier->insert(ScmSetup::$scm_game_table, [
                        'game_event_id' => $event_id,
                        'game_type'     => 'G',
                        'game_cluster'    => $group,
                        'game_round'    => $game_round,
                        'game_order'    => $game_order,
                        'game_home_id'  => ScmParamsService::get_params($event_id)->get_fill_games() ? $game[0]['id_team'] : 0,
                        'game_away_id'  => ScmParamsService::get_params($event_id)->get_fill_games() ? $game[1]['id_team'] : 0,
                        'game_date'     => ScmEventService::get_event($event_id)->get_start_date()->get_timestamp()
                    ]);
                    $game_order++;
                }
                $game_round++;
            }
            if ($c_return_games)
            {
                $game_order_r = $game_order;
                $game_round_r = $game_round;
                foreach ($schedule as $round => $games) {
                    foreach ($games as $i => $game) {
                        self::$db_querier->insert(ScmSetup::$scm_game_table, [
                            'game_event_id' => $event_id,
                            'game_type' => 'G',
                            'game_cluster' => $group,
                            'game_round' => $game_round_r,
                            'game_order' => $game_order_r,
                            'game_home_id' => ScmParamsService::get_params($event_id)->get_fill_games() ? $game[1]['id_team'] : 0,
                            'game_away_id' => ScmParamsService::get_params($event_id)->get_fill_games() ? $game[0]['id_team'] : 0,
                            'game_date' => ScmEventService::get_event($event_id)->get_start_date()->get_timestamp()
                        ]);
                        $game_order_r++;
                    }
                    $game_round_r++;
                }
            }
        }
    }

    public static function set_groups_finals_games(int $event_id) : void
    {
        $c_return_games = ScmEventService::get_event_game_type($event_id) == ScmDivision::RETURN_GAMES;
        // build groups from event teams list
        $groups = self::get_group_teams_list($event_id);

        // Build schedule
        $full_schedule = [];
        foreach ($groups as $index => $teams) {
            if (count($teams) % 2 != 0) {
                $teams[] = ['id_team' => 0];
            }
            $schedule = self::build_group_games($teams);
            $full_schedule[$index] = $schedule;
        }
        // Build game list
        foreach ($full_schedule as $group => $schedule) {
            $game_order = $game_round = 1;
            foreach ($schedule as $round => $games) 
            {
                foreach ($games as $i => $game) {
                    self::$db_querier->insert(ScmSetup::$scm_game_table, [
                        'game_event_id' => $event_id,
                        'game_type'     => 'B',
                        'game_cluster'    => $group,
                        'game_round'    => $game_round,
                        'game_order'    => $game_order,
                        'game_home_id'  => 0,
                        'game_away_id'  => 0,
                        'game_date'     => ScmEventService::get_event($event_id)->get_start_date()->get_timestamp()
                    ]);
                    $game_order++;
                }
                $game_round++;
            }
            if ($c_return_games)
            {
                $game_order_r = $game_order;
                $game_round_r = $game_round;
                foreach ($schedule as $round => $games) {
                    foreach ($games as $i => $game) {
                        self::$db_querier->insert(ScmSetup::$scm_game_table, [
                            'game_event_id' => $event_id,
                            'game_type' => 'B',
                            'game_cluster' => $group,
                            'game_round' => $game_round_r,
                            'game_order' => $game_order_r,
                            'game_home_id' => 0,
                            'game_away_id' => 0,
                            'game_date' => ScmEventService::get_event($event_id)->get_start_date()->get_timestamp()
                        ]);
                        $game_order_r++;
                    }
                    $game_round_r++;
                }
            }
        }
    }

    /** set all games of one group */
    private static function build_group_games(array $group_teams) : array
    {
        $teams_number = count($group_teams);
        $schedule = [];
        for ($round = 0; $round < $teams_number - 1; $round++) {
            for ($game = 0; $game < $teams_number / 2; $game++) {
                $home = ($round + $game) % ($teams_number - 1);
                $away = ($teams_number - 1 - $game + $round) % ($teams_number - 1);

                if ($game == 0) {
                    $away = $teams_number - 1;
                }
                $schedule[$round][] = [$group_teams[$home], $group_teams[$away]];
            }
        }
        return $schedule;
    }

    /** Get list of teams in a group */
    public static function get_group_teams_list(int $event_id) : array
    {
        $event_teams = ScmTeamService::get_teams($event_id);
        $groups = [];
        foreach($event_teams as $team)
        {
            $group_nb = $team['team_group'];
            if(!isset($groups[$group_nb]))
                $groups[$group_nb] = [];
            $groups[$group_nb][] = $team;
        }
        return $groups;
    }

    /** Get list of games in a day or group */
    public static function games_list_from_group(int $event_id, string $stage = '', int $group = null) : array
    {
        $event_games = ScmGameService::get_games($event_id);
        $games = [];
        if (!is_null($group))
        {
            foreach($event_games as $game)
            {
                if($game['game_type'] == $stage && $game['game_cluster'] == $group)
                {
                    $games[] = $game;
                }
            }
        }
        else
        {
            foreach($event_games as $game)
            {
                if($game['game_type'] == $stage)
                {
                    $group_nb = $game['game_cluster'];
                    if(!isset($games[$group_nb]))
                        $games[$group_nb] = [];
                    $games[$group_nb][] = $game;
                }
            }
        }

        return $games;
    }

    public static function get_last_matchday_group(int $event_id):int
    {
        $now = new Date();
        $results = self::$db_querier->select('SELECT game.*, params.*
            FROM ' . ScmSetup::$scm_game_table . ' game
            LEFT JOIN ' . ScmSetup::$scm_params_table . ' params ON params.params_event_id = game.game_event_id
            WHERE game.game_event_id = :id
            AND game.game_date < ' . $now->get_timestamp() . '
            AND params.hat_ranking = 0
            AND (game_type = "G" OR game_type = "B")
            ORDER BY game.game_date ASC', [
                'id' => $event_id
            ]
        );
        $last_rounds = [];
        foreach ($results as $game)
        {
            if ($game['game_home_score'] != '')
            {
                $last_rounds[] = $game['game_round'];
            }
        }

        return end($last_rounds);
    }

    public static function get_last_matchday_hat(int $event_id):int
    {
        $now = new Date();
        $results = self::$db_querier->select('SELECT game.*, params.*
            FROM ' . ScmSetup::$scm_game_table . ' game
            LEFT JOIN ' . ScmSetup::$scm_params_table . ' params ON params.params_event_id = game.game_event_id
            WHERE game.game_event_id = :id
            AND game.game_date < ' . $now->get_timestamp() . '
            AND params.hat_ranking = 1
            AND game_type = "G"
            ORDER BY game.game_date ASC', [
                'id' => $event_id
            ]
        );
        $last_rounds = [];
        foreach ($results as $game)
        {
            if ($game['game_home_score'] != '')
            {
                $last_rounds[] = $game['game_cluster'];
            }
        }

        return end($last_rounds);
    }

    public static function get_next_matchday_group(int $event_id):string
    {
        $now = new Date();
        $results = self::$db_querier->select('SELECT game.*, params.*
            FROM ' . ScmSetup::$scm_game_table . ' game
            LEFT JOIN ' . ScmSetup::$scm_params_table . ' params ON params.params_event_id = game.game_event_id
            WHERE game.game_event_id = :id
            AND game.game_date > ' . $now->get_timestamp() . '
            AND params.hat_ranking = 0
            AND (game_type = "G" OR game_type = "B")
            ORDER BY game_date DESC', [
                'id' => $event_id
            ]
        );
        $next_rounds = [];
        foreach ($results as $game)
        {
            if ($game['game_home_score'] == '' && $game['game_home_score'] != 0)
                $next_rounds[] = $game['game_round'];
        }

        return $next_rounds ? end($next_rounds) : '';
    }

    public static function get_next_matchday_hat(int $event_id):string
    {
        $now = new Date();
        $results = self::$db_querier->select('SELECT game.*, params.*
            FROM ' . ScmSetup::$scm_game_table . ' game
            LEFT JOIN ' . ScmSetup::$scm_params_table . ' params ON params.params_event_id = game.game_event_id
            WHERE game.game_event_id = :id
            AND game.game_date > ' . $now->get_timestamp() . '
            AND params.hat_ranking = 1
            AND (game_type = "G" OR game_type = "B")
            ORDER BY game_date DESC', [
                'id' => $event_id
            ]
        );
        $next_rounds = [];
        foreach ($results as $game)
        {
            if ($game['game_home_score'] == '')
            {
                $next_rounds[] = $game['game_cluster'];
            }
        }

        return $next_rounds ? end($next_rounds) : '';
    }
}