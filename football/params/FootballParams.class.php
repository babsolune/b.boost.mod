<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 27
 * @since       PHPBoost 6.0 - 2022 12 27
*/

class FootballParams
{
	private $id_params;
	private $params_compet_id;
	private $teams_per_group;
	private $hat_ranking;
	private $hat_days;
	private $fill_matches;
	private $looser_bracket;
	private $display_playgrounds;
	private $victory_points;
	private $draw_points;
	private $loss_points;
	private $promotion;
	private $promotion_color;
	private $play_off;
	private $play_off_color;
	private $relegation;
	private $relegation_color;
	private $ranking_type;
	private $match_duration;

	private $rounds_number;
	private $has_overtime;
	private $overtime_duration;
	private $third_place;
	private $golden_goal;
	private $silver_goal;

	private $favorite_team_id;

	public function get_id_params()
	{
		return $this->id_params;
	}

	public function set_id_params($id_params)
	{
		$this->id_params = $id_params;
	}

	public function get_params_compet_id()
	{
		return $this->params_compet_id;
	}

	public function set_params_compet_id($params_compet_id)
	{
		$this->params_compet_id = $params_compet_id;
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

	public function get_fill_matches()
	{
		return $this->fill_matches;
	}

	public function set_fill_matches($fill_matches)
	{
		$this->fill_matches = $fill_matches;
	}

	public function get_looser_bracket()
	{
		return $this->looser_bracket;
	}

	public function set_looser_bracket($looser_bracket)
	{
		$this->looser_bracket = $looser_bracket;
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

	public function get_promotion_color()
	{
		return $this->promotion_color;
	}

	public function set_promotion_color($promotion_color)
	{
		$this->promotion_color = $promotion_color;
	}

	public function get_play_off()
	{
		return $this->play_off;
	}

	public function set_play_off($play_off)
	{
		$this->play_off = $play_off;
	}

	public function get_play_off_color()
	{
		return $this->play_off_color;
	}

	public function set_play_off_color($play_off_color)
	{
		$this->play_off_color = $play_off_color;
	}

	public function get_relegation()
	{
		return $this->relegation;
	}

	public function set_relegation($relegation)
	{
		$this->relegation = $relegation;
	}

	public function get_relegation_color()
	{
		return $this->relegation_color;
	}

	public function set_relegation_color($relegation_color)
	{
		$this->relegation_color = $relegation_color;
	}

	public function get_ranking_type()
	{
		return $this->ranking_type;
	}

	public function set_ranking_type($ranking_type)
	{
		$this->ranking_type = $ranking_type;
	}

	public function get_match_duration()
	{
		return $this->match_duration;
	}

	public function set_match_duration($match_duration)
	{
		$this->match_duration = $match_duration;
	}

	public function get_rounds_number()
	{
		return $this->rounds_number;
	}

	public function set_rounds_number($rounds_number)
	{
		$this->rounds_number = $rounds_number;
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
		return FootballAuthorizationsService::check_authorizations()->manage_compets();
	}

	public function get_properties()
	{
		return array(
			'id_params' => $this->get_id_params(),
			'params_compet_id' => $this->get_params_compet_id(),
			'teams_per_group' => $this->get_teams_per_group(),
			'hat_ranking' => $this->get_hat_ranking(),
			'hat_days' => $this->get_hat_days(),
			'fill_matches' => $this->get_fill_matches(),
			'looser_bracket' => $this->get_looser_bracket(),
			'display_playgrounds' => $this->get_display_playgrounds(),
			'victory_points' => $this->get_victory_points(),
			'draw_points' => $this->get_draw_points(),
			'loss_points' => $this->get_loss_points(),
			'promotion' => $this->get_promotion(),
			'promotion_color' => $this->get_promotion_color(),
			'play_off' => $this->get_play_off(),
			'play_off_color' => $this->get_play_off_color(),
			'relegation' => $this->get_relegation(),
			'relegation_color' => $this->get_relegation_color(),
			'ranking_type' => $this->get_ranking_type(),
			'match_duration' => $this->get_match_duration(),
			'rounds_number' => $this->get_rounds_number(),
			'has_overtime' => $this->get_has_overtime(),
			'overtime_duration' => $this->get_overtime_duration(),
			'third_place' => $this->get_third_place(),
			'golden_goal' => $this->get_golden_goal(),
			'silver_goal' => $this->get_silver_goal(),
			'favorite_team_id' => $this->get_favorite_team_id(),
		);
	}

	public function set_properties(array $properties)
	{
		$this->id_params = $properties['id_params'];
		$this->params_compet_id = $properties['params_compet_id'];
		$this->teams_per_group = $properties['teams_per_group'];
		$this->hat_ranking = $properties['hat_ranking'];
		$this->hat_days = $properties['hat_days'];
		$this->fill_matches = $properties['fill_matches'];
		$this->looser_bracket = $properties['looser_bracket'];
		$this->display_playgrounds = $properties['display_playgrounds'];
		$this->victory_points = $properties['victory_points'];
		$this->draw_points = $properties['draw_points'];
		$this->loss_points = $properties['loss_points'];
		$this->promotion = $properties['promotion'];
		$this->promotion_color = $properties['promotion_color'];
		$this->play_off = $properties['play_off'];
		$this->play_off_color = $properties['play_off_color'];
		$this->relegation = $properties['relegation'];
		$this->relegation_color = $properties['relegation_color'];
		$this->ranking_type = $properties['ranking_type'];
		$this->match_duration = $properties['match_duration'];
		$this->rounds_number = $properties['rounds_number'];
		$this->has_overtime = $properties['has_overtime'];
		$this->overtime_duration = $properties['overtime_duration'];
		$this->third_place = $properties['third_place'];
		$this->golden_goal = $properties['golden_goal'];
		$this->silver_goal = $properties['silver_goal'];
		$this->favorite_team_id = $properties['favorite_team_id'];
	}

	public function init_default_properties()
	{
        $this->promotion = 0;
        $this->promotion_color = '#baffb0';
        $this->play_off = 0;
        $this->play_off_color = '#b0e1ff';
        $this->relegation = 0;
        $this->relegation_color = '#ffb0b0';
	}
}
?>
