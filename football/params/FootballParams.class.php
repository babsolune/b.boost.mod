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
	private $overtime;
	private $third_place;
	private $golden_goal;
	private $silver_goal;

	private $set_mode;
	private $sets_number;
	private $bonus;
	private $favorite_team_id;

	private $is_sub_compet;
	private $compet_master_id;
	private $sub_compet_rank;

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

	public function get_overtime()
	{
		return $this->overtime;
	}

	public function set_overtime($overtime)
	{
		$this->overtime = $overtime;
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

	public function get_set_mode()
	{
		return $this->set_mode;
	}

	public function set_set_mode($set_mode)
	{
		$this->set_mode = $set_mode;
	}

	public function get_sets_number()
	{
		return $this->sets_number;
	}

	public function set_sets_number($sets_number)
	{
		$this->sets_number = $sets_number;
	}

	public function get_bonus()
	{
		return $this->bonus;
	}

	public function set_bonus($bonus)
	{
		$this->bonus = $bonus;
	}

	public function get_favorite_team_id()
	{
		return $this->favorite_team_id;
	}

	public function set_favorite_team_id($favorite_team_id)
	{
		$this->favorite_team_id = $favorite_team_id;
	}

	public function get_is_sub_compet()
	{
		return $this->is_sub_compet;
	}

	public function set_is_sub_compet($is_sub_compet)
	{
		$this->is_sub_compet = $is_sub_compet;
	}

	public function get_compet_master_id()
	{
		return $this->compet_master_id;
	}

	public function set_compet_master_id($compet_master_id)
	{
		$this->compet_master_id = $compet_master_id;
	}

	public function get_sub_compet_rank()
	{
		return $this->sub_compet_rank;
	}

	public function set_sub_compet_rank($sub_compet_rank)
	{
		$this->sub_compet_rank = $sub_compet_rank;
	}

	public function is_authorized_to_manage_params()
	{
		return FootballAuthorizationsService::check_authorizations()->manage_seasons();
	}

	public function get_properties()
	{
		return array(
			'id_params' => $this->get_id_params(),
			'params_compet_id' => $this->get_params_compet_id(),
			'teams_per_group' => $this->get_teams_per_group(),
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
			'overtime' => $this->get_overtime(),
			'third_place' => $this->get_third_place(),
			'golden_goal' => $this->get_golden_goal(),
			'silver_goal' => $this->get_silver_goal(),
			'set_mode' => $this->get_set_mode(),
			'sets_number' => $this->get_sets_number(),
			'bonus' => $this->get_bonus(),
			'favorite_team_id' => $this->get_favorite_team_id(),
			'is_sub_compet' => $this->get_is_sub_compet(),
			'compet_master_id' => $this->get_compet_master_id(),
			'sub_compet_rank' => $this->get_sub_compet_rank(),
		);
	}

	public function set_properties(array $properties)
	{
		$this->id_params = $properties['id_params'];
		$this->params_compet_id = $properties['params_compet_id'];
		$this->teams_per_group = $properties['teams_per_group'];
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
		$this->overtime = $properties['overtime'];
		$this->third_place = $properties['third_place'];
		$this->golden_goal = $properties['golden_goal'];
		$this->silver_goal = $properties['silver_goal'];
		$this->set_mode = $properties['set_mode'];
		$this->sets_number = $properties['sets_number'];
		$this->bonus = $properties['bonus'];
		$this->favorite_team_id = $properties['favorite_team_id'];
		$this->is_sub_compet = $properties['is_sub_compet'];
		$this->compet_master_id = $properties['compet_master_id'];
		$this->sub_compet_rank = $properties['sub_compet_rank'];
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

	public function get_template_vars()
	{
		return array(
            // Conditions

            // Item
		);
	}
}
?>
