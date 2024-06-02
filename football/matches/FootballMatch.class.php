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
    private $match_compet_id;
    private $match_type;
    private $match_group;
    private $match_order;
    private $match_day;
    private $match_playground;
    private $match_home_id;
    private $match_home_score;
    private $match_home_pen;
    private $match_away_pen;
    private $match_away_score;
    private $match_away_id;
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

    function get_match_type()
    {
        return $this->match_type;
    }

    function set_match_type($match_type)
    {
        $this->match_type = $match_type;
    }

    function get_match_group()
    {
        return $this->match_group;
    }

    function set_match_group($match_group)
    {
        $this->match_group = $match_group;
    }

    function get_match_order()
    {
        return $this->match_order;
    }

    function set_match_order($match_order)
    {
        $this->match_order = $match_order;
    }

    function get_match_day()
    {
        return $this->match_day;
    }

    function set_match_day($match_day)
    {
        $this->match_day = $match_day;
    }

    function get_match_playground()
    {
        return $this->match_playground;
    }

    function set_match_playground($match_playground)
    {
        $this->match_playground = $match_playground;
    }

    function get_match_home_id()
    {
        return $this->match_home_id;
    }

    function set_match_home_id($match_home_id)
    {
        $this->match_home_id = $match_home_id;
    }

    function get_match_home_score()
    {
        return $this->match_home_score;
    }

    function set_match_home_score($match_home_score)
    {
        $this->match_home_score = $match_home_score;
    }

    function get_match_home_pen()
    {
        return $this->match_home_pen;
    }

    function set_match_home_pen($match_home_pen)
    {
        $this->match_home_pen = $match_home_pen;
    }

    function get_match_away_id()
    {
        return $this->match_away_id;
    }

    function set_match_away_id($match_away_id)
    {
        $this->match_away_id = $match_away_id;
    }

    function get_match_away_score()
    {
        return $this->match_away_score;
    }

    function set_match_away_score($match_away_score)
    {
        $this->match_away_score = $match_away_score;
    }

    function get_match_away_pen()
    {
        return $this->match_away_pen;
    }

    function set_match_away_pen($match_away_pen)
    {
        $this->match_away_pen = $match_away_pen;
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
			'match_type' => $this->get_match_type(),
			'match_group' => $this->get_match_group(),
			'match_order' => $this->get_match_order(),
			'match_day' => $this->get_match_day(),
			'match_playground' => $this->get_match_playground(),
			'match_home_id' => $this->get_match_home_id(),
			'match_home_score' => $this->get_match_home_score(),
			'match_home_pen' => $this->get_match_home_pen(),
			'match_away_id' => $this->get_match_away_id(),
			'match_away_score' => $this->get_match_away_score(),
			'match_away_pen' => $this->get_match_away_pen(),
			'match_date' => $this->get_match_date() !== null ? $this->get_match_date()->get_timestamp() : 0,
		);
	}

	public function set_properties(array $properties)
	{
		$this->id_match = $properties['id_match'];
		$this->match_compet_id = $properties['match_compet_id'];
		$this->match_type = $properties['match_type'];
		$this->match_group = $properties['match_group'];
		$this->match_order = $properties['match_order'];
		$this->match_day = $properties['match_day'];
		$this->match_playground = $properties['match_playground'];
		$this->match_home_id = $properties['match_home_id'];
		$this->match_home_score = $properties['match_home_score'];
		$this->match_home_pen = $properties['match_home_pen'];
		$this->match_away_id = $properties['match_away_id'];
		$this->match_away_score = $properties['match_away_score'];
		$this->match_away_pen = $properties['match_away_pen'];
		$this->match_date = !empty($properties['match_date']) ? new Date($properties['match_date'], Timezone::SERVER_TIMEZONE) : null;
	}

	public function init_default_properties()
	{
        $this->match_date = new Date();
	}

	public function get_array_tpl_vars()
	{
        $c_home_score = $this->match_home_score != '';
        $c_home_pen   = $this->match_home_pen != '';
        $c_away_pen   = $this->match_away_pen != '';
        $c_away_score = $this->match_away_score != '';

        return array_merge(
            Date::get_array_tpl_vars($this->match_date, 'match_date'),
            array(
                'C_ONE_DAY' => FootballMatchService::one_day_compet($this->match_compet_id),
                'C_HAS_SCORE' => $c_home_score && $c_away_score,
                'C_HAS_PEN' => $c_home_pen && $c_away_pen,
                'C_HOME_FAV' => FootballParamsService::check_fav($this->match_compet_id, $this->match_home_id),
                'C_HOME_WIN' => $this->match_home_score > $this->match_away_score || $this->match_home_pen > $this->match_away_pen,
                'C_AWAY_FAV' => FootballParamsService::check_fav($this->match_compet_id, $this->match_away_id),
                'C_AWAY_WIN' => $this->match_home_score < $this->match_away_score || $this->match_home_pen < $this->match_away_pen,
                'WIN_COLOR' => FootballParamsService::get_params($this->match_compet_id)->get_promotion_color(),
                'MATCH_ID' => $this->match_type.$this->match_group.$this->match_order,
                'PLAYGROUND' => $this->match_playground,
                'HOME_ID' => $this->match_home_id,
                'HOME_TEAM' => $this->match_home_id ? FootballTeamService::get_team($this->match_home_id)->get_team_club_name() : '',
                'HOME_SCORE' => $this->match_home_score,
                'HOME_PEN' => $this->match_home_pen,
                'AWAY_PEN' => $this->match_away_pen,
                'AWAY_SCORE' => $this->match_away_score,
                'AWAY_TEAM' => $this->match_away_id ? FootballTeamService::get_team($this->match_away_id)->get_team_club_name() : '',
                'AWAY_ID' => $this->match_away_id
            )
        );
    }

}