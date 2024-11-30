<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmSeason
{
	private $id_season;
	private $season_name;
	private $first_year;
	private $calendar_year;

	public function get_id_season()
	{
		return $this->id_season;
	}

	public function set_id_season($id_season)
	{
		$this->id_season = $id_season;
	}

	public function get_season_name()
	{
		return $this->season_name;
	}

	public function set_season_name($season_name)
	{
		$this->season_name = $season_name;
	}

	public function get_first_year()
	{
		return $this->first_year;
	}

	public function set_first_year($first_year)
	{
		$this->first_year = $first_year;
	}

	public function get_calendar_year()
	{
		return $this->calendar_year;
	}

	public function set_calendar_year($calendar_year)
	{
		$this->calendar_year = $calendar_year;
	}

	public function is_authorized_to_manage()
	{
		return ScmAuthorizationsService::check_authorizations()->manage_seasons();
	}

	public function get_properties()
	{
		return [
			'id_season'     => $this->get_id_season(),
			'season_name'   => $this->get_season_name(),
			'first_year'    => $this->get_first_year(),
			'calendar_year' => $this->get_calendar_year()
        ];
	}

	public function set_properties(array $properties)
	{
		$this->id_season = $properties['id_season'];
		$this->season_name = $properties['season_name'];
		$this->first_year = $properties['first_year'];
		$this->calendar_year = $properties['calendar_year'];
	}
}
?>
