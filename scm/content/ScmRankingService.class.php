<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmRankingService
{
	private static $db_querier;

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

    public static function reorder_forfeit($ranks)
    {
        $running = array_filter($ranks, function($item) { return $item['status'] !== 'forfeit'; }); 
        $forfeit = array_filter($ranks, function($item) { return $item['status'] === 'forfeit'; });
        $running = array_values($running);
        $forfeit = array_values($forfeit);
        return $ranks = array_merge($running, $forfeit);
    }

	public static function general_ranking($event_id)
    {
        $game_teams = self::build_teams($event_id);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_general_ranks($results, $event_id);

        return self::reorder_forfeit($final_ranks);
    }

	public static function general_days_ranking($event_id, $day)
    {
        $game_teams = self::build_game_teams($event_id, $day);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_general_ranks($results, $event_id);

        return self::reorder_forfeit($final_ranks);
    }

	public static function general_groups_ranking($event_id, $group)
    {
        $game_teams = self::build_groups_teams($event_id, $group);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_general_ranks($results, $event_id);

        return self::reorder_forfeit($final_ranks);
    }

	public static function general_groups_finals_ranking($event_id, $group)
    {
        $game_teams = self::build_groups_finals_teams($event_id, $group);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_general_ranks($results, $event_id);

        return self::reorder_forfeit($final_ranks);
    }

	public static function general_groups_full_ranking($event_id)
    {
        $game_teams = self::build_teams($event_id);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_general_ranks($results, $event_id);

        return self::reorder_forfeit($final_ranks);
    }

	public static function home_ranking($event_id)
    {
        $game_teams = self::build_home_teams($event_id);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_general_ranks($results, $event_id);

        return self::reorder_forfeit($final_ranks);
    }

	public static function away_ranking($event_id)
    {
        $game_teams = self::build_away_teams($event_id);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_general_ranks($results, $event_id);

        return self::reorder_forfeit($final_ranks);
    }

	public static function attack_ranking($event_id)
    {
        $game_teams = self::build_teams($event_id);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_attack_ranks($results);

        return self::reorder_forfeit($final_ranks);
    }

	public static function defense_ranking($event_id)
    {
        $game_teams = self::build_teams($event_id);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_defense_ranks($results);

        return self::reorder_forfeit($final_ranks);
    }

	public static function build_results($event_id, $game_teams)
    {

        // Set result details for each team in all games
        $teams = [];
        for($i = 0; $i < count($game_teams); $i++)
        {
            $points = $played = $win = $draw = $loss = $fairplay = 0;
            if ($game_teams[$i]['goals_for'] > $game_teams[$i]['goals_against'])
            {
                $points = ScmParamsService::get_params($event_id)->get_victory_points();
                $win = $played = 1;
            }
            elseif ($game_teams[$i]['goals_for'] != '' && ($game_teams[$i]['goals_for'] === $game_teams[$i]['goals_against']))
            {
                $points = ScmParamsService::get_params($event_id)->get_draw_points();
                $draw = $played = 1;
            }
            elseif (($game_teams[$i]['goals_for'] < $game_teams[$i]['goals_against']))
            {
                $points = ScmParamsService::get_params($event_id)->get_loss_points();
                $loss = $played = 1;
            }

            $fairplay_cards = [];
            if (!empty($game_teams[$i]['yellow_card']) || !empty($game_teams[$i]['red_card']))
            {
                foreach(TextHelper::deserialize($game_teams[$i]['yellow_card']) as $yellow)
                {
                    $fairplay_cards[] = ScmParamsService::get_params($event_id)->get_fairplay_yellow();
                }
                foreach(TextHelper::deserialize($game_teams[$i]['red_card']) as $red)
                {
                    $fairplay_cards[] = ScmParamsService::get_params($event_id)->get_fairplay_red();
                }
            }

            if ($game_teams[$i]['team_id'])
                $teams[] = [
                    'team_id'       => $game_teams[$i]['team_id'],
                    'points'        => $points,
                    'played'        => $played,
                    'win'           => $win,
                    'draw'          => $draw,
                    'loss'          => $loss,
                    'goals_for'     => (int)$game_teams[$i]['goals_for'],
                    'goals_against' => (int)$game_teams[$i]['goals_against'],
                    'goal_average'  => (int)$game_teams[$i]['goals_for'] - (int)$game_teams[$i]['goals_against'],
                    'off_bonus'     => (int)$game_teams[$i]['off_bonus'],
                    'def_bonus'     => (int)$game_teams[$i]['def_bonus'],
                    'fairplay'      => array_sum($fairplay_cards),

                    'event_id'          => $event_id,
                    'points_prtl'       => 0,
                    'goal_average_prtl' => 0,
                    'goals_for_prlt'    => 0,
                    'goals_for_away'    => 0,
                    'win_away'          => 0,
                    'fairplay_prtl'     => 0,
                    'status'            => ScmTeamService::get_event_team_status($game_teams[$i]['team_id'])
                ];
        }

        // Count points/goals for each team
        $results = [];
        foreach ($teams as $team)
        {
            $team_id = $team['team_id'];
            if (!isset($results[$team_id])) {
                $results[$team_id] = $team;
            } else {
                $results[$team_id]['points']        += $team['points'];
                $results[$team_id]['played']        += $team['played'];
                $results[$team_id]['win']           += $team['win'];
                $results[$team_id]['draw']          += $team['draw'];
                $results[$team_id]['loss']          += $team['loss'];
                $results[$team_id]['goals_for']     += $team['goals_for'];
                $results[$team_id]['goals_against'] += $team['goals_against'];
                $results[$team_id]['goal_average']  += $team['goal_average'];
                $results[$team_id]['off_bonus']     += $team['off_bonus'];
                $results[$team_id]['def_bonus']     += $team['def_bonus'];
                $results[$team_id]['fairplay']      += $team['fairplay'];

                $yellow_card = '';
            }
        }

        $final_results = [];
        // Add team penalties to points
        $results = array_values($results);
        foreach ($results as $result)
        {
            $penalties = ScmTeamService::get_team($result['team_id'])->get_team_penalty();
            $result['points'] = $result['points'] + $penalties;
            if (ScmParamsService::get_params($event_id)->get_bonus())
            {
                $result['points'] = $result['points'] + ($result['off_bonus'] + $result['def_bonus']);
            }
            $final_results[] = $result;
        }
        $running_results = array_filter($final_results, function($item) {
            return $item['status'] !== 'exempt';
        });

        $final_results = array_values($running_results);
        return $final_results;
    }

    public static function get_params_crit($event_id)
    {
        return [
            ScmParamsService::get_params($event_id)->get_ranking_crit_1(),
            ScmParamsService::get_params($event_id)->get_ranking_crit_2(),
            ScmParamsService::get_params($event_id)->get_ranking_crit_3(),
            ScmParamsService::get_params($event_id)->get_ranking_crit_4(),
            ScmParamsService::get_params($event_id)->get_ranking_crit_5(),
            ScmParamsService::get_params($event_id)->get_ranking_crit_6(),
            ScmParamsService::get_params($event_id)->get_ranking_crit_7(),
            ScmParamsService::get_params($event_id)->get_ranking_crit_8(),
            ScmParamsService::get_params($event_id)->get_ranking_crit_9(),
            ScmParamsService::get_params($event_id)->get_ranking_crit_10()
        ];
    }

	public static function points($a, $b)
    {
		if ($a['points'] == $b['points'])
			return 0;
		return ($a['points'] < $b['points']) ? 1 : -1;
	}

	public static function points_prtl($a, $b)
    {
        foreach (ScmGameService::get_games($a['event_id']) as $game)
        {
            if (($a['team_id'] == $game['game_home_id'] && $b['team_id'] == $game['game_away_id']))
            {
                if ($game['game_home_score'] > $game['game_away_score'])
                {
                    $a['points_prtl'] += ScmParamsService::get_params($a['event_id'])->get_victory_points();
                    $b['points_prtl'] += ScmParamsService::get_params($a['event_id'])->get_loss_points();
                }
                elseif ($game['game_home_score'] == $game['game_away_score'])
                {
                    $a['points_prtl'] += ScmParamsService::get_params($a['event_id'])->get_draw_points();
                    $b['points_prtl'] += ScmParamsService::get_params($a['event_id'])->get_draw_points();
                }
                elseif ($game['game_home_score'] < $game['game_away_score'])
                {
                    $a['points_prtl'] += ScmParamsService::get_params($a['event_id'])->get_loss_points();
                    $b['points_prtl'] += ScmParamsService::get_params($a['event_id'])->get_victory_points();
                }
            }
            if ($b['team_id'] == $game['game_home_id'] && $a['team_id'] == $game['game_away_id'])
            {
                if ($game['game_home_score'] > $game['game_away_score'])
                {
                    $b['points_prtl'] += ScmParamsService::get_params($a['event_id'])->get_victory_points();
                    $a['points_prtl'] += ScmParamsService::get_params($a['event_id'])->get_loss_points();
                }
                elseif ($game['game_home_score'] = $game['game_away_score'])
                {
                    $b['points_prtl'] += ScmParamsService::get_params($a['event_id'])->get_draw_points();
                    $a['points_prtl'] += ScmParamsService::get_params($a['event_id'])->get_draw_points();
                }
                elseif ($game['game_home_score'] < $game['game_away_score'])
                {
                    $b['points_prtl'] += ScmParamsService::get_params($a['event_id'])->get_loss_points();
                    $a['points_prtl'] += ScmParamsService::get_params($a['event_id'])->get_victory_points();
                }
            }

        }
        if ($a['points_prtl'] == $b['points_prtl'])
            return 0;
		return ($a['points_prtl'] < $b['points_prtl']) ? 1 : -1;
	}

	public static function goal_average($a, $b)
    {
		if ($a['goal_average'] == $b['goal_average'])
			return 0;
		return $a['goal_average'] < $b['goal_average'] ? 1 : -1;
	}

	public static function goal_average_prtl($a, $b)
    {
		if ($a['goal_average_prtl'] == $b['goal_average_prtl'])
			return 0;
		return $a['goal_average_prtl'] < $b['goal_average_prtl'] ? 1 : -1;
	}

	public static function goals_for($a, $b)
    {
		if ($a['goals_for'] == $b['goals_for'])
			return 0;
		return ($a['goals_for'] < $b['goals_for']) ? 1 : -1;
	}

	public static function goals_for_prtl($a, $b)
    {
        foreach (ScmGameService::get_games($a['event_id']) as $game)
        {
            if (($a['team_id'] == $game['game_home_id'] && $b['team_id'] == $game['game_away_id']))
            {
                $a['goals_for_prtl'] += $game['game_home_score'];
                $b['goals_for_prtl'] += $game['game_away_score'];
            }
            if ($b['team_id'] == $game['game_home_id'] && $a['team_id'] == $game['game_away_id'])
            {
                $b['goals_for_prtl'] += $game['game_home_score'];
                $a['goals_for_prtl'] += $game['game_away_score'];
            }
        }
		if ($a['goals_for_prtl'] == $b['goals_for_prtl'])
			return 0;
		return ($a['goals_for_prtl'] < $b['goals_for_prtl']) ? 1 : -1;
	}

	public static function goals_for_away($a, $b)
    {
        foreach (ScmGameService::get_games($a['event_id']) as $game)
        {
            if (($a['team_id'] == $game['game_away_id']))
            {
                $a['goals_for'] += $game['game_home_score'];
            }
            if ($b['team_id'] == $game['game_away_id'])
            {
                $b['goals_for'] += $game['game_home_score'];
            }
        }
		if ($a['goals_for_away'] == $b['goals_for_away'])
			return 0;
		return ($a['goals_for_away'] < $b['goals_for_away']) ? 1 : -1;
	}

	public static function win($a, $b)
    {
		if ($a['win'] == $b['win'])
			return 0;
		return ($a['win'] < $b['win']) ? 1 : -1;
	}

	public static function win_away($a, $b)
    {
        foreach (ScmGameService::get_games($a['event_id']) as $game)
        {
            if ($a['team_id'] == $game['game_away_id'])
            {
                if ($game['game_home_score'] < $game['game_away_score'])
                {
                    $a['win_away'] += 1;
                }
            }
            if ($b['team_id'] == $game['game_away_id'])
            {
                if ($game['game_home_score'] < $game['game_away_score'])
                {
                    $b['win_away'] += 1;
                }
            }
        }
		if ($a['win_away'] == $b['win_away'])
			return 0;
		return ($a['win_away'] < $b['win_away']) ? 1 : -1;
	}

	public static function goals_against($a, $b)
    {
		if ($a['goals_against'] == $b['goals_against'])
			return 0;
		return ($b['goals_against'] < $a['goals_against']) ? -1 : 1;
	}

	public static function fairplay($a, $b)
    {
		if ($a['fairplay'] == $b['fairplay'])
			return 0;
		return ($b['fairplay'] < $a['fairplay']) ? 1 : -1;
	}

	public static function fairplay_prtl($a, $b)
    {
        foreach (ScmGameService::get_games($a['event_id']) as $game)
        {
            $a_fairplay = $b_fairplay = [];
            if (($a['team_id'] == $game['game_home_id'] && $b['team_id'] == $game['game_away_id']))
            {
                foreach(TextHelper::deserialize($game['game_home_yellow']) as $yellow)
                {
                    $a_fairplay[] = ScmParamsService::get_params($game['game_event_id'])->get_fairplay_yellow();
                }
                foreach(TextHelper::deserialize($game['game_home_red']) as $red)
                {
                    $a_fairplay[] = ScmParamsService::get_params($game['game_event_id'])->get_fairplay_red();
                }
                foreach(TextHelper::deserialize($game['game_away_yellow']) as $yellow)
                {
                    $b_fairplay[] = ScmParamsService::get_params($game['game_event_id'])->get_fairplay_yellow();
                }
                foreach(TextHelper::deserialize($game['game_away_red']) as $red)
                {
                    $b_fairplay[] = ScmParamsService::get_params($game['game_event_id'])->get_fairplay_red();
                }
                $a['fairplay_prtl'] += array_sum($a_fairplay);
                $b['fairplay_prtl'] += array_sum($b_fairplay);
            }
            if ($b['team_id'] == $game['game_home_id'] && $a['team_id'] == $game['game_away_id'])
            {
                foreach(TextHelper::deserialize($game['game_home_yellow']) as $yellow)
                {
                    $b_fairplay[] = ScmParamsService::get_params($game['game_event_id'])->get_fairplay_yellow();
                }
                foreach(TextHelper::deserialize($game['game_home_red']) as $red)
                {
                    $b_fairplay[] = ScmParamsService::get_params($game['game_event_id'])->get_fairplay_red();
                }
                foreach(TextHelper::deserialize($game['game_away_yellow']) as $yellow)
                {
                    $a_fairplay[] = ScmParamsService::get_params($game['game_event_id'])->get_fairplay_yellow();
                }
                foreach(TextHelper::deserialize($game['game_away_red']) as $red)
                {
                    $a_fairplay[] = ScmParamsService::get_params($game['game_event_id'])->get_fairplay_red();
                }
                $a['fairplay_prtl'] += array_sum($a_fairplay);
                $b['fairplay_prtl'] += array_sum($b_fairplay);
            }
        }
		if ($a['fairplay_prtl'] == $b['fairplay_prtl'])
			return 0;
		return ($b['fairplay_prtl'] < $a['fairplay_prtl']) ? 1 : -1;
	}

    public static function sort_general_ranks($results, $event_id)
    {
        foreach (array_reverse(self::get_params_crit($event_id)) as $crit) {
            if ($crit != '')
            {
                usort($results, self::class . '::' . $crit);
            }
		}
        return $results;
    }

	public static function sort_attack_ranks($results)
    {
        usort($results, function($a, $b)
        {
            if ($a['goals_for'] == $b['goals_for']) {
                if ($a['points'] == $b['points']) {
                    if ($a['goal_average'] == $b['goal_average']) {
                        if ($a['goal_average'] == $b['goal_average']) {
                            return 0;
                        }
                    }
                    return $b['goal_average'] - $a['goal_average'];
                }
                return $b['points'] - $a['points'];
            }
            return $b['goals_for'] - $a['goals_for'];
        });
        return $results;
    }

	public static function sort_defense_ranks($results)
    {
        usort($results, function($a, $b)
        {
            if ($a['goals_against'] == $b['goals_against']) {
                if ($a['points'] == $b['points']) {
                    if ($a['goal_average'] == $b['goal_average']) {
                        if ($a['goal_average'] == $b['goal_average']) {
                            return 0;
                        }
                    }
                    return $b['goal_average'] - $a['goal_average'];
                }
                return $b['points'] - $a['points'];
            }
            return $a['goals_against'] - $b['goals_against'];
        });
        return $results;
    }

	public static function build_teams($event_id)
    {
        $games = ScmGameService::get_games($event_id);
        $game_teams = [];
        // Get results of all games
        foreach ($games as $game)
        {
            $game_teams[] = [
                'team_id'       => $game['game_home_id'],
                'goals_for'     => $game['game_home_score'],
                'goals_against' => $game['game_away_score'],
                'yellow_card'   => $game['game_home_yellow'],
                'red_card'      => $game['game_home_red'],
                'off_bonus'     => $game['game_home_off_bonus'],
                'def_bonus'     => $game['game_home_def_bonus'],
            ];
            $game_teams[] = [
                'team_id'       => $game['game_away_id'],
                'goals_for'     => $game['game_away_score'],
                'goals_against' => $game['game_home_score'],
                'yellow_card'   => $game['game_away_yellow'],
                'red_card'      => $game['game_away_red'],
                'off_bonus'     => $game['game_away_off_bonus'],
                'def_bonus'     => $game['game_away_def_bonus'],
            ];
        }
        return $game_teams;
    }

	public static function build_groups_teams($event_id, $group)
    {
        $games = $days_games = [];
        $games[] = ScmGameService::get_games_in_cluster($event_id, $group);
        foreach ($games as $day_games)
        {
            $days_games = array_merge($days_games, $day_games);
        }

        $game_teams = [];
        // Get results of all games
        foreach ($days_games as $game)
        {
            $game_teams[] = [
                'team_id'       => $game['game_home_id'],
                'goals_for'     => $game['game_home_score'],
                'goals_against' => $game['game_away_score'],
                'yellow_card'   => $game['game_home_yellow'],
                'red_card'      => $game['game_home_red'],
                'off_bonus'     => $game['game_home_off_bonus'],
                'def_bonus'     => $game['game_home_def_bonus'],
            ];
            $game_teams[] = [
                'team_id'       => $game['game_away_id'],
                'goals_for'     => $game['game_away_score'],
                'goals_against' => $game['game_home_score'],
                'yellow_card'   => $game['game_away_yellow'],
                'red_card'      => $game['game_away_red'],
                'off_bonus'     => $game['game_away_off_bonus'],
                'def_bonus'     => $game['game_away_def_bonus'],
            ];
        }
        return $game_teams;
    }

	public static function build_groups_finals_teams($event_id, $group)
    {
        $games = $days_games = [];
        $games[] = ScmGroupService::games_list_from_group($event_id, 'B', $group);
        foreach ($games as $day_games)
        {
            $days_games = array_merge($days_games, $day_games);
        }

        $game_teams = [];
        // Get results of all games
        foreach ($days_games as $game)
        {
            $game_teams[] = [
                'team_id'       => $game['game_home_id'],
                'goals_for'     => $game['game_home_score'],
                'goals_against' => $game['game_away_score'],
                'yellow_card'   => $game['game_home_yellow'],
                'red_card'      => $game['game_home_red'],
                'off_bonus'     => $game['game_home_off_bonus'],
                'def_bonus'     => $game['game_home_def_bonus'],
            ];
            $game_teams[] = [
                'team_id'       => $game['game_away_id'],
                'goals_for'     => $game['game_away_score'],
                'goals_against' => $game['game_home_score'],
                'yellow_card'   => $game['game_away_yellow'],
                'red_card'      => $game['game_away_red'],
                'off_bonus'     => $game['game_away_off_bonus'],
                'def_bonus'     => $game['game_away_def_bonus'],
            ];
        }
        return $game_teams;
    }

	public static function build_game_teams($event_id, $day)
    {
        $games = $days_games = [];
        for ($i = 1; $i <= $day; $i++)
        {
            $games[] = ScmGameService::get_games_in_cluster($event_id, $i);
        }
        foreach ($games as $day_games)
        {
            $days_games = array_merge($days_games, $day_games);
        }

        $game_teams = [];
        // Get results of all games
        foreach ($days_games as $game)
        {
            $game_teams[] = [
                'team_id'       => $game['game_home_id'],
                'goals_for'     => $game['game_home_score'],
                'goals_against' => $game['game_away_score'],
                'yellow_card'   => $game['game_home_yellow'],
                'red_card'      => $game['game_home_red'],
                'off_bonus'     => $game['game_home_off_bonus'],
                'def_bonus'     => $game['game_home_def_bonus'],
            ];
            $game_teams[] = [
                'team_id'       => $game['game_away_id'],
                'goals_for'     => $game['game_away_score'],
                'goals_against' => $game['game_home_score'],
                'yellow_card'   => $game['game_away_yellow'],
                'red_card'      => $game['game_away_red'],
                'off_bonus'     => $game['game_home_off_bonus'],
                'def_bonus'     => $game['game_home_def_bonus'],
            ];
        }
        return $game_teams;
    }

	public static function build_home_teams($event_id)
    {
        $games = ScmGameService::get_games($event_id);
        $game_teams = [];
        // Get results of all games
        foreach ($games as $game)
        {
            $game_teams[] = [
                'team_id'       => $game['game_home_id'],
                'goals_for'     => $game['game_home_score'],
                'goals_against' => $game['game_away_score'],
                'yellow_card'   => $game['game_home_yellow'],
                'red_card'      => $game['game_home_red'],
                'off_bonus'     => $game['game_home_off_bonus'],
                'def_bonus'     => $game['game_home_def_bonus'],
            ];
        }
        return $game_teams;
    }

	public static function build_away_teams($event_id)
    {
        $games = ScmGameService::get_games($event_id);
        $game_teams = [];
        // Get results of all games
        foreach ($games as $game)
        {
            $game_teams[] = [
                'team_id'       => $game['game_away_id'],
                'goals_for'     => $game['game_away_score'],
                'goals_against' => $game['game_home_score'],
                'yellow_card'   => $game['game_away_yellow'],
                'red_card'      => $game['game_away_red'],
                'off_bonus'     => $game['game_away_off_bonus'],
                'def_bonus'     => $game['game_away_def_bonus'],
            ];
        }
        return $game_teams;
    }
}