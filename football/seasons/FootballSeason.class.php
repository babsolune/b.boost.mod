<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballSeason
{
	private $id_season;
	private $season_name;
	private $season_slug;
	private $season_year;
	private $season_calendar_year;

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

	public function get_season_slug()
	{
		return $this->season_slug;
	}

	public function set_season_slug($season_slug)
	{
		$this->season_slug = $season_slug;
	}

	public function get_season_year()
	{
		return $this->season_year;
	}

	public function set_season_year($season_year)
	{
		$this->season_year = $season_year;
	}

	public function get_season_calendar_year()
	{
		return $this->season_calendar_year;
	}

	public function set_season_calendar_year($season_calendar_year)
	{
		$this->season_calendar_year = $season_calendar_year;
	}

	public function is_authorized_to_manage()
	{
		return FootballAuthorizationsService::check_authorizations()->manage_seasons();
	}

	public function get_properties()
	{
		return array(
			'id_season' => $this->get_id_season(),
			'season_name' => $this->get_season_name(),
			'season_slug' => $this->get_season_slug(),
			'season_year' => $this->get_season_year(),
			'season_calendar_year' => $this->get_season_calendar_year()
		);
	}

	public function set_properties(array $properties)
	{
		$this->id_season = $properties['id_season'];
		$this->season_name = $properties['season_name'];
		$this->season_slug = $properties['season_slug'];
		$this->season_year = !empty($properties['year']) ? new Date($properties['season_year'], Timezone::SERVER_TIMEZONE) : null;
		$this->season_calendar_year = !empty($properties['season_calendar_year']) ? new Date($properties['season_calendar_year'], Timezone::SERVER_TIMEZONE) : null;
	}

	public function init_default_properties()
	{
	}

	public function get_template_vars()
	{
		return array_merge(
			array(
				// Conditions

				// Item
				'ID'                  => $this->id_season,
				'NAME'               => $this->season_name,

				// Links
				'U_EDIT_SEASON'           => FootballUrlBuilder::edit($this->id_season)->rel(),
				'U_DELETE_SEASON'         => FootballUrlBuilder::delete($this->id_season)->rel(),
			)
		);
	}
}
?>
