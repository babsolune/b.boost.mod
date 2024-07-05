<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmGame
{
    private $id_game;
    private $game_event_id;
    private $game_type;
    private $game_group;
    private $game_round;
    private $game_order;
    private $game_playground;
    private $game_home_id;
    private $game_home_score;
    private $game_home_pen;
    private $game_away_pen;
    private $game_away_score;
    private $game_away_id;
    private $game_date;

    function get_id_game()
    {
        return $this->id_game;
    }

    function set_id_game($id_game)
    {
        $this->id_game = $id_game;
    }

    function get_game_event_id()
    {
        return $this->game_event_id;
    }

    function set_game_event_id($game_event_id)
    {
        $this->game_event_id = $game_event_id;
    }

    function get_game_type()
    {
        return $this->game_type;
    }

    function set_game_type($game_type)
    {
        $this->game_type = $game_type;
    }

    function get_game_group()
    {
        return $this->game_group;
    }

    function set_game_group($game_group)
    {
        $this->game_group = $game_group;
    }

    function get_game_round()
    {
        return $this->game_round;
    }

    function set_game_round($game_round)
    {
        $this->game_round = $game_round;
    }

    function get_game_order()
    {
        return $this->game_order;
    }

    function set_game_order($game_order)
    {
        $this->game_order = $game_order;
    }

    function get_game_playground()
    {
        return $this->game_playground;
    }

    function set_game_playground($game_playground)
    {
        $this->game_playground = $game_playground;
    }

    function get_game_home_id()
    {
        return $this->game_home_id;
    }

    function set_game_home_id($game_home_id)
    {
        $this->game_home_id = $game_home_id;
    }

    function get_game_home_score()
    {
        return $this->game_home_score;
    }

    function set_game_home_score($game_home_score)
    {
        $this->game_home_score = $game_home_score;
    }

    function get_game_home_pen()
    {
        return $this->game_home_pen;
    }

    function set_game_home_pen($game_home_pen)
    {
        $this->game_home_pen = $game_home_pen;
    }

    function get_game_away_id()
    {
        return $this->game_away_id;
    }

    function set_game_away_id($game_away_id)
    {
        $this->game_away_id = $game_away_id;
    }

    function get_game_away_score()
    {
        return $this->game_away_score;
    }

    function set_game_away_score($game_away_score)
    {
        $this->game_away_score = $game_away_score;
    }

    function get_game_away_pen()
    {
        return $this->game_away_pen;
    }

    function set_game_away_pen($game_away_pen)
    {
        $this->game_away_pen = $game_away_pen;
    }

    function get_game_date()
    {
        return $this->game_date;
    }

    function set_game_date(Date $game_date)
    {
        $this->game_date = $game_date;
    }

    public function get_properties()
	{
		return [
			'id_game'         => $this->get_id_game(),
			'game_event_id'   => $this->get_game_event_id(),
			'game_type'       => $this->get_game_type(),
			'game_group'      => $this->get_game_group(),
			'game_round'      => $this->get_game_round(),
			'game_order'      => $this->get_game_order(),
			'game_playground' => $this->get_game_playground(),
			'game_home_id'    => $this->get_game_home_id(),
			'game_home_score' => $this->get_game_home_score(),
			'game_home_pen'   => $this->get_game_home_pen(),
			'game_away_id'    => $this->get_game_away_id(),
			'game_away_score' => $this->get_game_away_score(),
			'game_away_pen'   => $this->get_game_away_pen(),
			'game_date'       => $this->get_game_date() !== null ? $this->get_game_date()->get_timestamp() : 0,
        ];
	}

	public function set_properties(array $properties)
	{
		$this->id_game         = $properties['id_game'];
		$this->game_event_id   = $properties['game_event_id'];
		$this->game_type       = $properties['game_type'];
		$this->game_group      = $properties['game_group'];
		$this->game_round      = $properties['game_round'];
		$this->game_order      = $properties['game_order'];
		$this->game_playground = $properties['game_playground'];
		$this->game_home_id    = $properties['game_home_id'];
		$this->game_home_score = $properties['game_home_score'];
		$this->game_home_pen   = $properties['game_home_pen'];
		$this->game_away_id    = $properties['game_away_id'];
		$this->game_away_score = $properties['game_away_score'];
		$this->game_away_pen   = $properties['game_away_pen'];
		$this->game_date       = !empty($properties['game_date']) ? new Date($properties['game_date'], Timezone::SERVER_TIMEZONE) : null;
	}

	public function init_default_properties()
	{
        $this->game_date = new Date();
	}

	public function get_template_vars()
	{
        $c_home_score = $this->game_home_score != '';
        $c_home_pen   = $this->game_home_pen != '';
        $c_away_pen   = $this->game_away_pen != '';
        $c_away_score = $this->game_away_score != '';
        $event_slug   = ScmEventService::get_event_slug($this->game_event_id);

        return array_merge(
            Date::get_array_tpl_vars($this->game_date, 'game_date'),
            [
                'C_IS_LIVE'       => ScmGameService::is_live($this->game_event_id, $this->id_game),
                'C_HAS_SCORE'     => $c_home_score && $c_away_score,
                'WIN_COLOR'       => ScmConfig::load()->get_promotion_color(),
                'C_HAS_PEN'       => $c_home_pen && $c_away_pen,
                'C_HOME_FAV'      => ScmParamsService::check_fav($this->game_event_id, $this->game_home_id) && $this->game_home_id,
                'C_HOME_WIN'      => $this->game_home_score > $this->game_away_score || $this->game_home_pen > $this->game_away_pen,
                'C_AWAY_FAV'      => ScmParamsService::check_fav($this->game_event_id, $this->game_away_id) && $this->game_away_id,
                'C_AWAY_WIN'      => $this->game_home_score < $this->game_away_score || $this->game_home_pen < $this->game_away_pen,
                'GAME_ID'         => $this->game_type.$this->game_group.$this->game_order,
                'MATCHDAY'        => $this->game_round,
                'PLAYGROUND'      => $this->game_playground,
                'HOME_ID'         => $this->game_home_id,
                'HOME_LOGO'       => $this->game_home_id ? ScmTeamService::get_team_logo($this->game_home_id) : '',
                'HOME_TEAM'       => $this->game_home_id ? ScmTeamService::get_team_name($this->game_home_id) : '',
                'U_HOME_CALENDAR' => $this->game_home_id ? ScmUrlBuilder::display_team_calendar($this->game_event_id, $event_slug, $this->game_home_id)->rel() : '#',
                'HOME_SCORE'      => $this->game_home_score,
                'HOME_PEN'        => $this->game_home_pen,
                'AWAY_PEN'        => $this->game_away_pen,
                'AWAY_SCORE'      => $this->game_away_score,
                'U_AWAY_CALENDAR' => $this->game_away_id ? ScmUrlBuilder::display_team_calendar($this->game_event_id, $event_slug, $this->game_away_id)->rel() : '#',
                'AWAY_TEAM'       => $this->game_away_id ? ScmTeamService::get_team_name($this->game_away_id) : '',
                'AWAY_LOGO'       => $this->game_away_id ? ScmTeamService::get_team_logo($this->game_away_id) : '',
                'AWAY_ID'         => $this->game_away_id
            ]
        );
    }

}