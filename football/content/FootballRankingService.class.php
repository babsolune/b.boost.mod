<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballRankingService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	public static function general_ranking($compet_id)
    {
        $days_teams = self::build_teams($compet_id);
        $ranks = self::build_ranks($compet_id, $days_teams);
        $final_ranks = self::sort_general_ranks($ranks);
        return $final_ranks;
    }

	public static function general_days_ranking($compet_id, $day)
    {
        $days_teams = self::build_days_teams($compet_id, $day);
        $ranks = self::build_ranks($compet_id, $days_teams);
        $final_ranks = self::sort_general_ranks($ranks);
        return $final_ranks;
    }

	public static function home_ranking($compet_id)
    {
        $days_teams = self::build_home_teams($compet_id);
        $ranks = self::build_ranks($compet_id, $days_teams);
        $final_ranks = self::sort_general_ranks($ranks);
        return $final_ranks;
    }

	public static function away_ranking($compet_id)
    {
        $days_teams = self::build_away_teams($compet_id);
        $ranks = self::build_ranks($compet_id, $days_teams);
        $final_ranks = self::sort_general_ranks($ranks);
        return $final_ranks;
    }

	public static function attack_ranking($compet_id)
    {
        $days_teams = self::build_teams($compet_id);
        $ranks = self::build_ranks($compet_id, $days_teams);
        $final_ranks = self::sort_attack_ranks($ranks);
        return $final_ranks;
    }

	public static function defense_ranking($compet_id)
    {
        $days_teams = self::build_teams($compet_id);
        $ranks = self::build_ranks($compet_id, $days_teams);
        $final_ranks = self::sort_defense_ranks($ranks);
        return $final_ranks;
    }

	public static function build_ranks($compet_id, $days_teams)
    {
        // Set rank details for each team in all matches
        $teams = [];
        for($i = 0; $i < count($days_teams); $i++)
        {
            $points = $played = $win = $draw = $loss = 0;
            if ($days_teams[$i]['goals_for'] > $days_teams[$i]['goals_against'])
            {
                $points = FootballParamsService::get_params($compet_id)->get_victory_points();
                $win = $played = 1;
            }
            elseif ($days_teams[$i]['goals_for'] != '' && ($days_teams[$i]['goals_for'] === $days_teams[$i]['goals_against']))
            {
                $points = FootballParamsService::get_params($compet_id)->get_draw_points();
                $draw = $played = 1;
            }
            elseif (($days_teams[$i]['goals_for'] < $days_teams[$i]['goals_against']))
            {
                $points = FootballParamsService::get_params($compet_id)->get_loss_points();
                $loss = $played = 1;
            }
            if ($days_teams[$i]['team_id'])
                $teams[] = [
                    'team_id' => $days_teams[$i]['team_id'],
                    'points' => $points,
                    'played' => $played,
                    'win' => $win,
                    'draw' => $draw,
                    'loss' => $loss,
                    'goals_for' => (int)$days_teams[$i]['goals_for'],
                    'goals_against' => (int)$days_teams[$i]['goals_against'],
                    'goal_average' => (int)$days_teams[$i]['goals_for'] - (int)$days_teams[$i]['goals_against'],
                ];
        }

        // Count points/goals for each team
        $ranks = [];
        foreach ($teams as $team) 
        {
            $team_id = $team['team_id'];
            $penalties = FootballTeamService::get_team($team['team_id'])->get_team_penalty();
            if (!isset($ranks[$team_id])) {
                $ranks[$team_id] = $team;
            } else {
                $ranks[$team_id]['points'] += $team['points'];
                $ranks[$team_id]['played'] += $team['played'];
                $ranks[$team_id]['win'] += $team['win'];
                $ranks[$team_id]['draw'] += $team['draw'];
                $ranks[$team_id]['loss'] += $team['loss'];
                $ranks[$team_id]['goals_for'] += $team['goals_for'];
                $ranks[$team_id]['goals_against'] += $team['goals_against'];
                $ranks[$team_id]['goal_average'] += $team['goals_for'] - $team['goals_against'];
            }
        }

        // Add team penalties to points
        $final_ranks = [];
        $ranks = array_values($ranks);
        foreach ($ranks as $rank)
        {
            $penalties = FootballTeamService::get_team($rank['team_id'])->get_team_penalty();
            $rank['points'] = $rank['points'] + $penalties;
            $final_ranks[] = $rank;
        }
        return $final_ranks;
    }

	public static function sort_general_ranks($ranks)
    {
        usort($ranks, function($a, $b)
        {
            if ($a['points'] == $b['points']) {
                if ($a['win'] == $b['win']) {
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
                return $b['win'] - $a['win'];
            }
            return $b['points'] - $a['points'];
        });
        return $ranks;
    }

	public static function sort_attack_ranks($ranks)
    {
        usort($ranks, function($a, $b)
        {
            if ($a['goals_for'] == $b['goals_for']) {
                if ($a['points'] == $b['points']) {
                    if ($a['win'] == $b['win']) {
                        if ($a['goal_average'] == $b['goal_average']) {
                            if ($a['goal_average'] == $b['goal_average']) {
                                return 0;
                            }
                        }
                        return $b['goal_average'] - $a['goal_average'];
                    }
                    return $b['win'] - $a['win'];
                }
                return $b['points'] - $a['points'];
            }
            return $b['goals_for'] - $a['goals_for'];
        });
        return $ranks;
    }

	public static function sort_defense_ranks($ranks)
    {
        usort($ranks, function($a, $b)
        {
            if ($a['goals_against'] == $b['goals_against']) {
                if ($a['points'] == $b['points']) {
                    if ($a['win'] == $b['win']) {
                        if ($a['goal_average'] == $b['goal_average']) {
                            if ($a['goal_average'] == $b['goal_average']) {
                                return 0;
                            }
                        }
                        return $b['goal_average'] - $a['goal_average'];
                    }
                    return $b['win'] - $a['win'];
                }
                return $b['points'] - $a['points'];
            }
            return $b['goals_against'] - $a['goals_against'];
        });
        return $ranks;
    }

	public static function build_teams($compet_id)
    {
        $matches = FootballMatchService::get_matches($compet_id);
        $days_teams = [];
        // Get results of all matches
        foreach ($matches as $match)
        {
            $days_teams[] = [
                'team_id' => $match['match_home_id'],
                'goals_for' => $match['match_home_score'],
                'goals_against' => $match['match_away_score'],
            ];
            $days_teams[] = [
                'team_id' => $match['match_away_id'],
                'goals_for' => $match['match_away_score'],
                'goals_against' => $match['match_home_score'],
            ];
        }
        return $days_teams;
    }

	public static function build_days_teams($compet_id, $day)
    {
        $matches = $days_matches = [];
        for ($i = 1; $i <= $day; $i++)
        {
            $matches[] = FootballMatchService::get_matches_in_day($compet_id, $i);
        }
        foreach ($matches as $day_matches)
        {
            $days_matches = array_merge($days_matches, $day_matches);
        }

        $days_teams = [];
        // Get results of all matches
        foreach ($days_matches as $match)
        {
            $days_teams[] = [
                'team_id' => $match['match_home_id'],
                'goals_for' => $match['match_home_score'],
                'goals_against' => $match['match_away_score'],
            ];
            $days_teams[] = [
                'team_id' => $match['match_away_id'],
                'goals_for' => $match['match_away_score'],
                'goals_against' => $match['match_home_score'],
            ];
        }
        return $days_teams;
    }

	public static function build_home_teams($compet_id)
    {
        $matches = FootballMatchService::get_matches($compet_id);
        $days_teams = [];
        // Get results of all matches
        foreach ($matches as $match)
        {
            $days_teams[] = [
                'team_id' => $match['match_home_id'],
                'goals_for' => $match['match_home_score'],
                'goals_against' => $match['match_away_score'],
            ];
        }
        return $days_teams;
    }

	public static function build_away_teams($compet_id)
    {
        $matches = FootballMatchService::get_matches($compet_id);
        $days_teams = [];
        // Get results of all matches
        foreach ($matches as $match)
        {
            $days_teams[] = [
                'team_id' => $match['match_away_id'],
                'goals_for' => $match['match_away_score'],
                'goals_against' => $match['match_home_score'],
            ];
        }
        return $days_teams;
    }
}