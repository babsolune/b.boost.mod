<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmParams
{
	private $id_params;
	private $params_event_id;
	private $games_number;
	private $groups_number;
	private $teams_per_group;
	private $hat_ranking;
	private $hat_days;
	private $fill_games;
	private $looser_bracket;
	private $brackets_number;
	private $display_playgrounds;
	private $victory_points;
	private $draw_points;
	private $loss_points;
	private $promotion;
	private $playoff_prom;
	private $playoff_releg;
	private $relegation;
	private $fairplay_yellow;
	private $fairplay_red;
	private $bonus;

	private $ranking_crit_1;
	private $ranking_crit_2;
	private $ranking_crit_3;
	private $ranking_crit_4;
	private $ranking_crit_5;
	private $ranking_crit_6;
	private $ranking_crit_7;
	private $ranking_crit_8;
	private $ranking_crit_9;
	private $ranking_crit_10;

	private $finals_type;
	private $rounds_number;
	private $draw_games;
	private $has_overtime;
	private $overtime_duration;
	private $third_place;
	private $golden_goal;
	private $silver_goal;

	private $game_duration;
	private $favorite_team_id;

    const FINALS_DIRECT   = "finals_direct";
    const FINALS_ROUND   = "finals_round";
    const FINALS_RANKING = "finals_ranking";

    const FORFEIT = 'forfeit';
    const EXEMPT = 'exempt';

    const BONUS_SINGLE   = "single";
    const BONUS_DOUBLE   = "double";

	public function get_id_params()
	{
		return $this->id_params;
	}

	public function set_id_params($id_params)
	{
		$this->id_params = $id_params;
	}

	public function get_params_event_id()
	{
		return $this->params_event_id;
	}

	public function set_params_event_id($params_event_id)
	{
		$this->params_event_id = $params_event_id;
	}

	public function get_games_number()
	{
		return $this->games_number;
	}

	public function set_games_number($games_number)
	{
		$this->games_number = $games_number;
	}

	public function get_groups_number()
	{
		return $this->groups_number;
	}

	public function set_groups_number($groups_number)
	{
		$this->groups_number = $groups_number;
	}

	public function get_teams_per_group()
	{
		return $this->teams_per_group;
	}

	public function set_teams_per_group($teams_per_group)
	{
		$this->teams_per_group = $teams_per_group;
	}

	public function get_hat_ranking()
	{
		return $this->hat_ranking;
	}

	public function set_hat_ranking($hat_ranking)
	{
		$this->hat_ranking = $hat_ranking;
	}

	public function get_hat_days()
	{
		return $this->hat_days;
	}

	public function set_hat_days($hat_days)
	{
		$this->hat_days = $hat_days;
	}

	public function get_fill_games()
	{
		return $this->fill_games;
	}

	public function set_fill_games($fill_games)
	{
		$this->fill_games = $fill_games;
	}

	public function get_looser_bracket()
	{
		return $this->looser_bracket;
	}

	public function set_looser_bracket($looser_bracket)
	{
		$this->looser_bracket = $looser_bracket;
	}

	public function get_brackets_number()
	{
		return $this->brackets_number;
	}

	public function set_brackets_number($brackets_number)
	{
		$this->brackets_number = $brackets_number;
	}

	public function get_display_playgrounds()
	{
		return $this->display_playgrounds;
	}

	public function set_display_playgrounds($display_playgrounds)
	{
		$this->display_playgrounds = $display_playgrounds;
	}

	public function get_victory_points()
	{
		return $this->victory_points;
	}

	public function set_victory_points($victory_points)
	{
		$this->victory_points = $victory_points;
	}

	public function get_draw_points()
	{
		return $this->draw_points;
	}

	public function set_draw_points($draw_points)
	{
		$this->draw_points = $draw_points;
	}

	public function get_loss_points()
	{
		return $this->loss_points;
	}

	public function set_loss_points($loss_points)
	{
		$this->loss_points = $loss_points;
	}

	public function get_promotion()
	{
		return $this->promotion;
	}

	public function set_promotion($promotion)
	{
		$this->promotion = $promotion;
	}

	public function get_playoff_prom()
	{
		return $this->playoff_prom;
	}

	public function set_playoff_prom($playoff_prom)
	{
		$this->playoff_prom = $playoff_prom;
	}

	public function get_playoff_releg()
	{
		return $this->playoff_releg;
	}

	public function set_playoff_releg($playoff_releg)
	{
		$this->playoff_releg = $playoff_releg;
	}

	public function get_relegation()
	{
		return $this->relegation;
	}

	public function set_relegation($relegation)
	{
		$this->relegation = $relegation;
	}

	public function get_fairplay_yellow()
	{
		return $this->fairplay_yellow;
	}

	public function set_fairplay_yellow($fairplay_yellow)
	{
		$this->fairplay_yellow = $fairplay_yellow;
	}

	public function get_fairplay_red()
	{
		return $this->fairplay_red;
	}

	public function set_fairplay_red($fairplay_red)
	{
		$this->fairplay_red = $fairplay_red;
	}

	public function get_bonus()
	{
		return $this->bonus;
	}

	public function set_bonus($bonus)
	{
		$this->bonus = $bonus;
	}

	public function get_ranking_crit_1()
	{
		return $this->ranking_crit_1;
	}

	public function set_ranking_crit_1($ranking_crit_1)
	{
		$this->ranking_crit_1 = $ranking_crit_1;
	}

	public function get_ranking_crit_2()
	{
		return $this->ranking_crit_2;
	}

	public function set_ranking_crit_2($ranking_crit_2)
	{
		$this->ranking_crit_2 = $ranking_crit_2;
	}

	public function get_ranking_crit_3()
	{
		return $this->ranking_crit_3;
	}

	public function set_ranking_crit_3($ranking_crit_3)
	{
		$this->ranking_crit_3 = $ranking_crit_3;
	}

	public function get_ranking_crit_4()
	{
		return $this->ranking_crit_4;
	}

	public function set_ranking_crit_4($ranking_crit_4)
	{
		$this->ranking_crit_4 = $ranking_crit_4;
	}

	public function get_ranking_crit_5()
	{
		return $this->ranking_crit_5;
	}

	public function set_ranking_crit_5($ranking_crit_5)
	{
		$this->ranking_crit_5 = $ranking_crit_5;
	}

	public function get_ranking_crit_6()
	{
		return $this->ranking_crit_6;
	}

	public function set_ranking_crit_6($ranking_crit_6)
	{
		$this->ranking_crit_6 = $ranking_crit_6;
	}

	public function get_ranking_crit_7()
	{
		return $this->ranking_crit_7;
	}

	public function set_ranking_crit_7($ranking_crit_7)
	{
		$this->ranking_crit_7 = $ranking_crit_7;
	}

	public function get_ranking_crit_8()
	{
		return $this->ranking_crit_8;
	}

	public function set_ranking_crit_8($ranking_crit_8)
	{
		$this->ranking_crit_8 = $ranking_crit_8;
	}

	public function get_ranking_crit_9()
	{
		return $this->ranking_crit_9;
	}

	public function set_ranking_crit_9($ranking_crit_9)
	{
		$this->ranking_crit_9 = $ranking_crit_9;
	}

	public function get_ranking_crit_10()
	{
		return $this->ranking_crit_10;
	}

	public function set_ranking_crit_10($ranking_crit_10)
	{
		$this->ranking_crit_10 = $ranking_crit_10;
	}

	public function get_game_duration()
	{
		return $this->game_duration;
	}

	public function set_game_duration($game_duration)
	{
		$this->game_duration = $game_duration;
	}

	public function get_finals_type()
	{
		return $this->finals_type;
	}

	public function set_finals_type($finals_type)
	{
		$this->finals_type = $finals_type;
	}

	public function get_rounds_number()
	{
		return $this->rounds_number;
	}

	public function set_rounds_number($rounds_number)
	{
		$this->rounds_number = $rounds_number;
	}

	public function get_draw_games()
	{
		return $this->draw_games;
	}

	public function set_draw_games($draw_games)
	{
		$this->draw_games = $draw_games;
	}

	public function get_has_overtime()
	{
		return $this->has_overtime;
	}

	public function set_has_overtime($has_overtime)
	{
		$this->has_overtime = $has_overtime;
	}

	public function get_overtime_duration()
	{
		return $this->overtime_duration;
	}

	public function set_overtime_duration($overtime_duration)
	{
		$this->overtime_duration = $overtime_duration;
	}

	public function get_third_place()
	{
		return $this->third_place;
	}

	public function set_third_place($third_place)
	{
		$this->third_place = $third_place;
	}

	public function get_golden_goal()
	{
		return $this->golden_goal;
	}

	public function set_golden_goal($golden_goal)
	{
		$this->golden_goal = $golden_goal;
	}

	public function get_silver_goal()
	{
		return $this->silver_goal;
	}

	public function set_silver_goal($silver_goal)
	{
		$this->silver_goal = $silver_goal;
	}

	public function get_favorite_team_id()
	{
		return $this->favorite_team_id;
	}

	public function set_favorite_team_id($favorite_team_id)
	{
		$this->favorite_team_id = $favorite_team_id;
	}

	public function is_authorized_to_manage_params()
	{
		return ScmAuthorizationsService::check_authorizations()->manage_events();
	}

	public function get_properties()
	{
		return [
			'id_params'           => $this->get_id_params(),
			'params_event_id'     => $this->get_params_event_id(),
			'games_number'        => $this->get_games_number(),
			'groups_number'       => $this->get_groups_number(),
			'teams_per_group'     => $this->get_teams_per_group(),
			'hat_ranking'         => $this->get_hat_ranking(),
			'hat_days'            => $this->get_hat_days(),
			'fill_games'          => $this->get_fill_games(),
			'looser_bracket'      => $this->get_looser_bracket(),
			'brackets_number'     => $this->get_brackets_number(),
			'display_playgrounds' => $this->get_display_playgrounds(),
			'victory_points'      => $this->get_victory_points(),
			'draw_points'         => $this->get_draw_points(),
			'loss_points'         => $this->get_loss_points(),
			'promotion'           => $this->get_promotion(),
			'playoff_prom'        => $this->get_playoff_prom(),
			'playoff_releg'       => $this->get_playoff_releg(),
			'relegation'          => $this->get_relegation(),
			'fairplay_yellow'     => $this->get_fairplay_yellow(),
			'fairplay_red'        => $this->get_fairplay_red(),
			'bonus'               => $this->get_bonus(),
			'ranking_crit_1'      => $this->get_ranking_crit_1(),
			'ranking_crit_2'      => $this->get_ranking_crit_2(),
			'ranking_crit_3'      => $this->get_ranking_crit_3(),
			'ranking_crit_4'      => $this->get_ranking_crit_4(),
			'ranking_crit_5'      => $this->get_ranking_crit_5(),
			'ranking_crit_6'      => $this->get_ranking_crit_6(),
			'ranking_crit_7'      => $this->get_ranking_crit_7(),
			'ranking_crit_8'      => $this->get_ranking_crit_8(),
			'ranking_crit_9'      => $this->get_ranking_crit_9(),
			'ranking_crit_10'     => $this->get_ranking_crit_10(),
			'game_duration'       => $this->get_game_duration(),
			'finals_type'         => $this->get_finals_type(),
			'rounds_number'       => $this->get_rounds_number(),
			'draw_games'          => $this->get_draw_games(),
			'has_overtime'        => $this->get_has_overtime(),
			'overtime_duration'   => $this->get_overtime_duration(),
			'third_place'         => $this->get_third_place(),
			'golden_goal'         => $this->get_golden_goal(),
			'silver_goal'         => $this->get_silver_goal(),
			'favorite_team_id'    => $this->get_favorite_team_id(),
        ];
	}

	public function set_properties(array $properties)
	{
		$this->id_params           = $properties['id_params'];
		$this->params_event_id     = $properties['params_event_id'];
		$this->games_number        = $properties['games_number'];
		$this->groups_number       = $properties['groups_number'];
		$this->teams_per_group     = $properties['teams_per_group'];
		$this->hat_ranking         = $properties['hat_ranking'];
		$this->hat_days            = $properties['hat_days'];
		$this->fill_games          = $properties['fill_games'];
		$this->looser_bracket      = $properties['looser_bracket'];
		$this->brackets_number     = $properties['brackets_number'];
		$this->display_playgrounds = $properties['display_playgrounds'];
		$this->victory_points      = $properties['victory_points'];
		$this->draw_points         = $properties['draw_points'];
		$this->loss_points         = $properties['loss_points'];
		$this->promotion           = $properties['promotion'];
		$this->playoff_prom        = $properties['playoff_prom'];
		$this->playoff_releg       = $properties['playoff_releg'];
		$this->relegation          = $properties['relegation'];
		$this->fairplay_yellow     = $properties['fairplay_yellow'];
		$this->fairplay_red        = $properties['fairplay_red'];
		$this->bonus               = $properties['bonus'];
		$this->ranking_crit_1      = $properties['ranking_crit_1'];
		$this->ranking_crit_2      = $properties['ranking_crit_2'];
		$this->ranking_crit_3      = $properties['ranking_crit_3'];
		$this->ranking_crit_4      = $properties['ranking_crit_4'];
		$this->ranking_crit_5      = $properties['ranking_crit_5'];
		$this->ranking_crit_6      = $properties['ranking_crit_6'];
		$this->ranking_crit_7      = $properties['ranking_crit_7'];
		$this->ranking_crit_8      = $properties['ranking_crit_8'];
		$this->ranking_crit_9      = $properties['ranking_crit_9'];
		$this->ranking_crit_10     = $properties['ranking_crit_10'];
		$this->game_duration       = $properties['game_duration'];
		$this->finals_type         = $properties['finals_type'];
		$this->rounds_number       = $properties['rounds_number'];
		$this->draw_games          = $properties['draw_games'];
		$this->has_overtime        = $properties['has_overtime'];
		$this->overtime_duration   = $properties['overtime_duration'];
		$this->third_place         = $properties['third_place'];
		$this->golden_goal         = $properties['golden_goal'];
		$this->silver_goal         = $properties['silver_goal'];
		$this->favorite_team_id    = $properties['favorite_team_id'];
	}

	public function init_default_properties()
	{
        $this->ranking_crit_1  = 'points_gen';
        $this->finals_type  = 'finals_round';
        $this->promotion  = 0;
        $this->playoff_prom    = 0;
        $this->playoff_releg    = 0;
        $this->relegation = 0;
	}
}
?>
