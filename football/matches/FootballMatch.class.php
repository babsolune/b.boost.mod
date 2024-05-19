<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 27
 * @since       PHPBoost 6.0 - 2022 12 27
*/

class FootballMatch
{
    private $id_match;
    private $match_number;
    private $match_compet_id;
    private $match_field;
    private $match_day_id;
    private $match_home_team_id;
    private $match_home_team_score;
    private $match_visit_team_id;
    private $match_visit_team_score;
    private $match_date;

    function get_id_match()
    {
        return $this->id_match;
    }

    function set_id_match($id_match)
    {
        $this->id_match = $id_match;
    }

    function get_match_compet_id()
    {
        return $this->match_compet_id;
    }

    function set_match_compet_id($match_compet_id)
    {
        $this->match_compet_id = $match_compet_id;
    }

    function get_match_field()
    {
        return $this->match_field;
    }

    function set_match_field($match_field)
    {
        $this->match_field = $match_field;
    }

    function get_match_number()
    {
        return $this->match_number;
    }

    function set_match_number($match_number)
    {
        $this->match_number = $match_number;
    }

    function get_match_day_id()
    {
        return $this->match_day_id;
    }

    function set_match_day_id($match_day_id)
    {
        $this->match_day_id = $match_day_id;
    }

    function get_match_home_team_id()
    {
        return $this->match_home_team_id;
    }

    function set_match_home_team_id($match_home_team_id)
    {
        $this->match_home_team_id = $match_home_team_id;
    }

    function get_match_home_team_score()
    {
        return $this->match_home_team_score;
    }

    function set_match_home_team_score($match_home_team_score)
    {
        $this->match_home_team_score = $match_home_team_score;
    }

    function get_match_visit_team_id()
    {
        return $this->match_visit_team_id;
    }

    function set_match_visit_team_id($match_visit_team_id)
    {
        $this->match_visit_team_id = $match_visit_team_id;
    }

    function get_match_visit_team_score()
    {
        return $this->match_visit_team_score;
    }

    function set_match_visit_team_score($match_visit_team_score)
    {
        $this->match_visit_team_score = $match_visit_team_score;
    }

    function get_match_date()
    {
        return $this->match_date;
    }

    function set_match_date(Date $match_date)
    {
        $this->match_date = $match_date;
    }

    public function get_properties()
	{
		return array(
			'id_match' => $this->get_id_match(),
			'match_compet_id' => $this->get_match_compet_id(),
			'match_field' => $this->get_match_field(),
			'match_number' => $this->get_match_number(),
			'match_day_id' => $this->get_match_day_id(),
			'match_home_team_id' => $this->get_match_home_team_id(),
			'match_home_team_score' => $this->get_match_home_team_score(),
			'match_visit_team_id' => $this->get_match_visit_team_id(),
			'match_visit_team_score' => $this->get_match_visit_team_score(),
			'match_date' => $this->get_match_date() !== null ? $this->get_match_date()->get_timestamp() : 0,
		);
	}

	public function set_properties(array $properties)
	{
		$this->id_match = $properties['id_match'];
		$this->match_compet_id = $properties['match_compet_id'];
		$this->match_field = $properties['match_field'];
		$this->match_number = $properties['match_number'];
		$this->match_day_id = $properties['match_day_id'];
		$this->match_home_team_id = $properties['match_home_team_id'];
		$this->match_home_team_score = $properties['match_home_team_score'];
		$this->match_visit_team_id = $properties['match_visit_team_id'];
		$this->match_visit_team_score = $properties['match_visit_team_score'];
		$this->match_date = !empty($properties['match_date']) ? new Date($properties['match_date'], Timezone::SERVER_TIMEZONE) : null;
	}

	public function init_default_properties()
	{
        $this->match_date = new Date();
	}

}