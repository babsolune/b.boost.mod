<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballDay
{
	private $id_day;
	private $day_compet_id;
	private $day_round;
	private $day_date;
	private $day_played;

	public function get_id_day()
	{
		return $this->id_day;
	}

	public function set_id_day($id_day)
	{
		$this->id_day = $id_day;
	}

	public function get_day_compet_id()
	{
		return $this->day_compet_id;
	}

	public function set_day_compet_id($day_compet_id)
	{
		$this->day_compet_id = $day_compet_id;
	}

	public function get_day_round()
	{
		return $this->day_round;
	}

	public function set_day_round($day_round)
	{
		$this->day_round = $day_round;
	}

	public function get_day_date()
	{
		return $this->day_date;
	}

	public function set_day_date(Date $day_date)
	{
		$this->day_date = $day_date;
	}

	public function get_day_played()
	{
		return $this->day_played;
	}

	public function set_day_played($day_played)
	{
		$this->day_played = $day_played;
	}

	public function is_authorized_to_manage_days()
	{
		return FootballAuthorizationsService::check_authorizations()->manage_compets();
	}

	public function get_properties()
	{
		return array(
			'id_day' => $this->get_id_day(),
			'day_compet_id' => $this->get_day_compet_id(),
			'day_round' => $this->get_day_round(),
			'day_date' => $this->get_day_date() !== null ? $this->get_day_date()->get_timestamp() : 0,
			'day_played' => $this->get_day_played(),
		);
	}

	public function set_properties(array $properties)
	{
		$this->id_day = $properties['id_day'];
		$this->day_compet_id = $properties['day_compet_id'];
		$this->day_round = $properties['day_round'];
		$this->day_date = !empty($properties['day_date']) ? new Date($properties['day_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->day_played = $properties['day_played'];
	}

	public function init_default_properties()
	{
        $this->day_date = new Date();
	}
}
?>
