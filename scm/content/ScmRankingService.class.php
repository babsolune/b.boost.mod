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

	public static function general_ranking($event_id)
    {
        $game_teams = self::build_teams($event_id);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_general_ranks($results);
        return $final_ranks;
    }

	public static function general_days_ranking($event_id, $day)
    {
        $game_teams = self::build_game_teams($event_id, $day);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_general_ranks($results);
        return $final_ranks;
    }

	public static function general_groups_ranking($event_id, $group)
    {
        $game_teams = self::build_groups_teams($event_id, $group);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_general_ranks($results);
        return $final_ranks;
    }

	public static function home_ranking($event_id)
    {
        $game_teams = self::build_home_teams($event_id);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_general_ranks($results);
        return $final_ranks;
    }

	public static function away_ranking($event_id)
    {
        $game_teams = self::build_away_teams($event_id);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_general_ranks($results);
        return $final_ranks;
    }

	public static function attack_ranking($event_id)
    {
        $game_teams = self::build_teams($event_id);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_attack_ranks($results);
        return $final_ranks;
    }

	public static function defense_ranking($event_id)
    {
        $game_teams = self::build_teams($event_id);
        $results = self::build_results($event_id, $game_teams);
        $final_ranks = self::sort_defense_ranks($results);
        return $final_ranks;
    }

	public static function build_results($event_id, $game_teams)
    {
        // Set rank details for each team in all games
        $teams = [];
        for($i = 0; $i < count($game_teams); $i++)
        {
            $points = $played = $win = $draw = $loss = 0;
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
                    'goal_average'  => (int)$game_teams[$i]['goals_for'] - (int)$game_teams[$i]['goals_against']
                ];
        }

        // Count points/goals for each team
        $results = [];
        foreach ($teams as $team)
        {
            $team_id = $team['team_id'];
            $penalties = ScmTeamService::get_team($team_id)->get_team_penalty();
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
                $results[$team_id]['goal_average']  += $team['goals_for'] - $team['goals_against'];
            }
        }

        // Add team penalties to points
        $final_results = [];
        $results = array_values($results);
        foreach ($results as $result)
        {
            $penalties = ScmTeamService::get_team($result['team_id'])->get_team_penalty();
            $result['points'] = $result['points'] + $penalties;
            $final_results[] = $result;
        }
        return $final_results;
    }

	public static function sort_general_ranks($results)
    {
        usort($results, function($a, $b)
        {
            if ($a['points'] == $b['points']) {
                if ($a['goal_average'] == $b['goal_average']) {
                    if ($a['goals_for'] == $b['goals_for']) {
                        if ($a['goals_for'] == $b['goals_for']) {
                            return 0;
                        }
                    }
                    return $b['goals_for'] - $a['goals_for'];
                }
                return $b['goal_average'] - $a['goal_average'];
            }
            return $b['points'] - $a['points'];
        });
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
                'team_id' => $game['game_home_id'],
                'goals_for' => $game['game_home_score'],
                'goals_against' => $game['game_away_score'],
                'yellow_card' => $game['game_home_yellow'],
                'red_card' => $game['game_home_red'],
            ];
            $game_teams[] = [
                'team_id' => $game['game_away_id'],
                'goals_for' => $game['game_away_score'],
                'goals_against' => $game['game_home_score'],
                'yellow_card' => $game['game_away_yellow'],
                'red_card' => $game['game_away_red'],
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
                'team_id' => $game['game_home_id'],
                'goals_for' => $game['game_home_score'],
                'goals_against' => $game['game_away_score'],
            ];
            $game_teams[] = [
                'team_id' => $game['game_away_id'],
                'goals_for' => $game['game_away_score'],
                'goals_against' => $game['game_home_score'],
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
                'team_id' => $game['game_home_id'],
                'goals_for' => $game['game_home_score'],
                'goals_against' => $game['game_away_score'],
            ];
            $game_teams[] = [
                'team_id' => $game['game_away_id'],
                'goals_for' => $game['game_away_score'],
                'goals_against' => $game['game_home_score'],
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
                'team_id' => $game['game_home_id'],
                'goals_for' => $game['game_home_score'],
                'goals_against' => $game['game_away_score'],
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
                'team_id' => $game['game_away_id'],
                'goals_for' => $game['game_away_score'],
                'goals_against' => $game['game_home_score'],
            ];
        }
        return $game_teams;
    }
}