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
    private $game_home_off_bonus;
    private $game_home_def_bonus;
    private $game_home_goals;
    private $game_home_yellow;
    private $game_home_red;
    private $game_home_empty;
    private $game_away_id;
    private $game_away_score;
    private $game_away_def_bonus;
    private $game_away_off_bonus;
    private $game_away_pen;
    private $game_away_goals;
    private $game_away_yellow;
    private $game_away_red;
    private $game_away_empty;
    private $game_date;
    private $game_video;
    private $game_summary;
    private $game_status;
    private $game_stadium;
    private $game_stadium_name;

    const DELAYED = 'delayed';
    const STOPPED = 'stopped';

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

    function get_game_home_off_bonus()
    {
        return $this->game_home_off_bonus;
    }

    function set_game_home_off_bonus($game_home_off_bonus)
    {
        $this->game_home_off_bonus = $game_home_off_bonus;
    }

    function get_game_home_def_bonus()
    {
        return $this->game_home_def_bonus;
    }

    function set_game_home_def_bonus($game_home_def_bonus)
    {
        $this->game_home_def_bonus = $game_home_def_bonus;
    }

	public function add_game_home_goals($game_home_goals)
	{
		$this->game_home_goals[] = $game_home_goals;
	}

    function get_game_home_goals()
    {
        return $this->game_home_goals;
    }

    function set_game_home_goals($game_home_goals)
    {
        $this->game_home_goals = $game_home_goals;
    }

	public function add_game_home_yellow($game_home_yellow)
	{
		$this->game_home_yellow[] = $game_home_yellow;
	}

    function get_game_home_yellow()
    {
        return $this->game_home_yellow;
    }

    function set_game_home_yellow($game_home_yellow)
    {
        $this->game_home_yellow = $game_home_yellow;
    }

	public function add_game_home_red($game_home_red)
	{
		$this->game_home_red[] = $game_home_red;
	}

    function get_game_home_red()
    {
        return $this->game_home_red;
    }

    function set_game_home_red($game_home_red)
    {
        $this->game_home_red = $game_home_red;
    }

    function get_game_home_empty()
    {
        return $this->game_home_empty;
    }

    function set_game_home_empty($game_home_empty)
    {
        $this->game_home_empty = $game_home_empty;
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

    function get_game_away_off_bonus()
    {
        return $this->game_away_off_bonus;
    }

    function set_game_away_off_bonus($game_away_off_bonus)
    {
        $this->game_away_off_bonus = $game_away_off_bonus;
    }

    function get_game_away_def_bonus()
    {
        return $this->game_away_def_bonus;
    }

    function set_game_away_def_bonus($game_away_def_bonus)
    {
        $this->game_away_def_bonus = $game_away_def_bonus;
    }

	public function add_game_away_goals($game_away_goals)
	{
		$this->game_away_goals[] = $game_away_goals;
	}

    function get_game_away_goals()
    {
        return $this->game_away_goals;
    }

    function set_game_away_goals($game_away_goals)
    {
        $this->game_away_goals = $game_away_goals;
    }

	public function add_game_away_yellow($game_away_yellow)
	{
		$this->game_away_yellow[] = $game_away_yellow;
	}

    function get_game_away_yellow()
    {
        return $this->game_away_yellow;
    }

    function set_game_away_yellow($game_away_yellow)
    {
        $this->game_away_yellow = $game_away_yellow;
    }

	public function add_game_away_red($game_away_red)
	{
		$this->game_away_red[] = $game_away_red;
	}

    function get_game_away_red()
    {
        return $this->game_away_red;
    }

    function set_game_away_red($game_away_red)
    {
        $this->game_away_red = $game_away_red;
    }

    function get_game_away_empty()
    {
        return $this->game_away_empty;
    }

    function set_game_away_empty($game_away_empty)
    {
        $this->game_away_empty = $game_away_empty;
    }

    function get_game_date()
    {
        return $this->game_date;
    }

    function set_game_date(Date $game_date)
    {
        $this->game_date = $game_date;
    }

    function get_game_video()
    {
		if (!$this->game_video instanceof Url)
			return new Url('');

		return $this->game_video;
    }

    function set_game_video(Url $game_video)
    {
        $this->game_video = $game_video;
    }

    function get_game_summary()
    {
        return $this->game_summary;
    }

    function set_game_summary($game_summary)
    {
        $this->game_summary = $game_summary;
    }

    function get_game_status()
    {
        return $this->game_status;
    }

    function set_game_status($game_status)
    {
        $this->game_status = $game_status;
    }

    function get_game_stadium()
    {
        return $this->game_stadium;
    }

    function set_game_stadium($game_stadium)
    {
        $this->game_stadium = $game_stadium;
    }

    function get_game_stadium_name()
    {
        return $this->game_stadium_name;
    }

    function set_game_stadium_name($game_stadium_name)
    {
        $this->game_stadium_name = $game_stadium_name;
    }

    public function get_properties()
	{
		return [
			'id_game'             => $this->get_id_game(),
			'game_event_id'       => $this->get_game_event_id(),
			'game_type'           => $this->get_game_type(),
			'game_group'          => $this->get_game_group(),
			'game_round'          => $this->get_game_round(),
			'game_order'          => $this->get_game_order(),
			'game_playground'     => $this->get_game_playground(),
			'game_home_id'        => $this->get_game_home_id(),
			'game_home_score'     => $this->get_game_home_score(),
			'game_home_pen'       => $this->get_game_home_pen(),
			'game_home_off_bonus' => $this->get_game_home_off_bonus(),
			'game_home_def_bonus' => $this->get_game_home_def_bonus(),
			'game_home_goals'     => TextHelper::serialize($this->get_game_home_goals()),
			'game_home_yellow'    => TextHelper::serialize($this->get_game_home_yellow()),
			'game_home_red'       => TextHelper::serialize($this->get_game_home_red()),
			'game_home_empty'     => $this->get_game_home_empty(),
			'game_away_id'        => $this->get_game_away_id(),
			'game_away_score'     => $this->get_game_away_score(),
			'game_away_pen'       => $this->get_game_away_pen(),
			'game_away_off_bonus' => $this->get_game_away_off_bonus(),
			'game_away_def_bonus' => $this->get_game_away_def_bonus(),
			'game_away_goals'     => TextHelper::serialize($this->get_game_away_goals()),
			'game_away_yellow'    => TextHelper::serialize($this->get_game_away_yellow()),
			'game_away_red'       => TextHelper::serialize($this->get_game_away_red()),
			'game_away_empty'     => $this->get_game_away_empty(),
			'game_date'           => $this->get_game_date() !== null ? $this->get_game_date()->get_timestamp() : 0,
			'game_video'          => $this->get_game_video()->relative(),
			'game_summary'        => $this->get_game_summary(),
			'game_status'         => $this->get_game_status(),
			'game_stadium'        => $this->get_game_stadium(),
			'game_stadium_name'   => $this->get_game_stadium_name(),
        ];
	}

	public function set_properties(array $properties)
	{
		$this->id_game             = $properties['id_game'];
		$this->game_event_id       = $properties['game_event_id'];
		$this->game_type           = $properties['game_type'];
		$this->game_group          = $properties['game_group'];
		$this->game_round          = $properties['game_round'];
		$this->game_order          = $properties['game_order'];
		$this->game_playground     = $properties['game_playground'];
		$this->game_home_id        = $properties['game_home_id'];
		$this->game_home_score     = $properties['game_home_score'];
		$this->game_home_pen       = $properties['game_home_pen'];
		$this->game_home_off_bonus = $properties['game_home_off_bonus'];
		$this->game_home_def_bonus = $properties['game_home_def_bonus'];
		$this->game_home_goals     = !empty($properties['game_home_goals']) ? TextHelper::unserialize($properties['game_home_goals']) : [];
		$this->game_home_yellow    = !empty($properties['game_home_yellow']) ? TextHelper::unserialize($properties['game_home_yellow']) : [];
		$this->game_home_red       = !empty($properties['game_home_red']) ? TextHelper::unserialize($properties['game_home_red']) : [];
		$this->game_home_empty     = $properties['game_home_empty'];
		$this->game_away_id        = $properties['game_away_id'];
		$this->game_away_score     = $properties['game_away_score'];
		$this->game_away_pen       = $properties['game_away_pen'];
		$this->game_away_off_bonus = $properties['game_away_off_bonus'];
		$this->game_away_def_bonus = $properties['game_away_def_bonus'];
		$this->game_away_goals     = !empty($properties['game_away_goals']) ? TextHelper::unserialize($properties['game_away_goals']) : [];
		$this->game_away_yellow    = !empty($properties['game_away_yellow']) ? TextHelper::unserialize($properties['game_away_yellow']) : [];
		$this->game_away_red       = !empty($properties['game_away_red']) ? TextHelper::unserialize($properties['game_away_red']) : [];
		$this->game_away_empty     = $properties['game_away_empty'];
		$this->game_date           = !empty($properties['game_date']) ? new Date($properties['game_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->game_video          = new Url($properties['game_video']);
		$this->game_summary        = $properties['game_summary'];
		$this->game_status         = $properties['game_status'];
		$this->game_stadium        = $properties['game_stadium'];
		$this->game_stadium_name   = $properties['game_stadium_name'];
	}

	public function init_default_properties()
	{
        $this->game_date  = new Date();
		$this->game_video = new Url('');
	}

	public function get_template_vars()
	{
        $lang = LangLoader::get_module_langs('scm');
        $c_home_score = $this->game_home_score != '';
        $c_home_pen   = $this->game_home_pen != '';
        $c_away_pen   = $this->game_away_pen != '';
        $c_away_score = $this->game_away_score != '';
        $event_slug   = ScmEventService::get_event_slug($this->game_event_id);
		$summary = FormatingHelper::second_parse($this->game_summary);

        $event = ScmEventService::get_event($this->game_event_id);
        $division = ScmDivisionService::get_division($event->get_division_id());
        $season = ScmSeasonService::get_season($event->get_season_id());
        $category = $event->get_category();

        switch ($this->get_game_status()) {
            case ScmGame::DELAYED :
                $status = $lang['scm.event.status.delayed'];
                break;
            case ScmGame::STOPPED :
                $status = $lang['scm.event.status.stopped'];
                break;
            case '' :
                $status = '';
                break;
        }

		$address = ScmConfig::load()->is_googlemaps_available() && $this->game_stadium_name ? $this->stadium_map()->display() : $this->game_stadium_name;

        return array_merge(
            Date::get_array_tpl_vars($this->game_date, 'game_date'),
            [
                'GAME_DIVISION'   => $division->get_division_name(),
                'GAME_SEASON'     => $season->get_season_name(),
                'GAME_CATEGORY'   => $category->get_name(),

                'C_IS_LIVE'       => ScmGameService::is_live($this->game_event_id, $this->id_game),
                'C_STATUS'        => $this->game_status,
                'C_HAS_SCORE'     => $c_home_score && $c_away_score,
                'C_HAS_HOME_LOGO' => $this->game_home_id && ScmTeamService::get_team_logo($this->game_home_id) !== '#',
                'C_HAS_AWAY_LOGO' => $this->game_away_id && ScmTeamService::get_team_logo($this->game_away_id) !== '#',
                'WIN_COLOR'       => ScmConfig::load()->get_promotion_color(),
                'C_HAS_PEN'       => $c_home_pen && $c_away_pen,
                'C_HOME_FAV'      => ScmParamsService::check_fav($this->game_event_id, $this->game_home_id) && $this->game_home_id,
                'C_HOME_WIN'      => $this->game_home_score > $this->game_away_score || $this->game_home_pen > $this->game_away_pen,
                'C_HOME_EMPTY'    => $this->game_home_id == 0,
                'C_AWAY_FAV'      => ScmParamsService::check_fav($this->game_event_id, $this->game_away_id) && $this->game_away_id,
                'C_AWAY_WIN'      => $this->game_home_score < $this->game_away_score || $this->game_home_pen < $this->game_away_pen,
                'C_AWAY_EMPTY'    => $this->game_away_id == 0,
                'GAME_ID'         => $this->game_type.$this->game_group.$this->game_round.$this->game_order,
                'MATCHDAY'        => $this->game_round,
                'PLAYGROUND'      => $this->game_playground,
                'HOME_ID'         => $this->game_home_id,
                'U_HOME_CLUB'     => $this->game_home_id ? ScmTeamService::get_team_link($this->game_home_id) : '#',
                'HOME_LOGO'       => $this->game_home_id ? ScmTeamService::get_team_logo($this->game_home_id) : '',
                'HOME_TEAM'       => $this->game_home_id ? ScmTeamService::get_team_name($this->game_home_id) : '',
                'U_HOME_CALENDAR' => $this->game_home_id ? ScmUrlBuilder::display_team_calendar($this->game_event_id, $event_slug, $this->game_home_id)->rel() : '#',
                'HOME_SCORE'      => $this->game_home_score,
                'HOME_PEN'        => $this->game_home_pen,
                'HOME_EMPTY'      => $this->game_home_empty,
                'AWAY_EMPTY'      => $this->game_away_empty,
                'AWAY_PEN'        => $this->game_away_pen,
                'AWAY_SCORE'      => $this->game_away_score,
                'U_AWAY_CALENDAR' => $this->game_away_id ? ScmUrlBuilder::display_team_calendar($this->game_event_id, $event_slug, $this->game_away_id)->rel() : '#',
                'AWAY_TEAM'       => $this->game_away_id ? ScmTeamService::get_team_name($this->game_away_id) : '',
                'AWAY_LOGO'       => $this->game_away_id ? ScmTeamService::get_team_logo($this->game_away_id) : '',
                'U_AWAY_CLUB'     => $this->game_away_id ? ScmTeamService::get_team_link($this->game_away_id) : '#',
                'AWAY_ID'         => $this->game_away_id,
                'C_VIDEO'         => !empty($this->game_video->absolute()),
                'U_VIDEO'         => $this->game_video->absolute(),
                'SUMMARY'         => $summary,
                'STATUS'          => $status,
                'STADIUM'         => $address
            ]
        );
    }

    public function get_details_template($view, $index)
    {
        foreach ($this->get_game_home_goals() as $details)
		{
			$view->assign_block_vars($index . '.home_goals', [
				'PLAYER' => $details['player'],
				'TIME' => $details['time'],
			]);
		}
        foreach ($this->get_game_away_goals() as $details)
		{
			$view->assign_block_vars($index . '.away_goals', [
				'PLAYER' => $details['player'],
				'TIME' => $details['time'],
			]);
		}
        foreach ($this->get_game_home_yellow() as $details)
		{
			$view->assign_block_vars($index . '.home_yellow', [
				'PLAYER' => $details['player'],
				'TIME' => $details['time'],
			]);
		}
        foreach ($this->get_game_away_yellow() as $details)
		{
			$view->assign_block_vars($index . '.away_yellow', [
				'PLAYER' => $details['player'],
				'TIME' => $details['time'],
			]);
		}
        foreach ($this->get_game_home_red() as $details)
		{
			$view->assign_block_vars($index . '.home_red', [
				'PLAYER' => $details['player'],
				'TIME' => $details['time'],
			]);
		}
        foreach ($this->get_game_away_red() as $details)
		{
			$view->assign_block_vars($index . '.away_red', [
				'PLAYER' => $details['player'],
				'TIME' => $details['time'],
			]);
		}
    }

    private function stadium_map()
    {
        $team = ScmTeamService::get_team($this->game_home_id);
        $club = ScmClubCache::load()->get_club($team->get_team_club_id());
        $real_id = $club['club_affiliate'] ? $club['club_affiliation'] : $club['id_club'];
        $real_club = new ScmClub();
        $real_club->set_properties(ScmClubCache::load()->get_club($real_id));

        $real_stadium = [];
        foreach (TextHelper::deserialize($real_club->get_club_locations()) as $options)
        {
            if ($options['name'] == $this->get_game_stadium_name() && $this->get_game_stadium_name() && $real_club->get_club_map_display())
                $real_stadium[] = $options;
        }

        return new GoogleMapsDisplayMap(TextHelper::serialize($real_stadium), 'game_stadium_' . $this->id_game, $this->get_game_stadium_name());
    }

}