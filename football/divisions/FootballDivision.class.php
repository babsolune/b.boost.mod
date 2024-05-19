<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballDivision
{
	private $id_division;
	private $division_name;
	private $division_compet_type;
	private $division_match_type;

	const SINGLE_MATCHES = 'single_matches';
	const RETURN_MATCHES = 'return_matches';

	const CHAMPIONSHIP = 'championship';
	const CUP = 'cup';
	const TOURNAMENT = 'tournament';

	public function get_id_division()
	{
		return $this->id_division;
	}

	public function set_id_division($id_division)
	{
		$this->id_division = $id_division;
	}

	public function get_division_name()
	{
		return $this->division_name;
	}

	public function set_division_name($division_name)
	{
		$this->division_name = $division_name;
	}

	public function get_division_compet_type()
	{
		return $this->division_compet_type;
	}

	public function set_division_compet_type($division_compet_type)
	{
		$this->division_compet_type = $division_compet_type;
	}

	public function get_division_match_type()
	{
		return $this->division_match_type;
	}

	public function set_division_match_type($division_match_type)
	{
		$this->division_match_type = $division_match_type;
	}

	public function is_authorized_to_manage()
	{
		return FootballAuthorizationsService::check_authorizations()->manage_divisions();
	}

	public function get_properties()
	{
		return array(
			'id_division' => $this->get_id_division(),
			'division_name' => $this->get_division_name(),
			'division_compet_type' => $this->get_division_compet_type(),
			'division_match_type' => $this->get_division_match_type()
		);
	}

	public function set_properties(array $properties)
	{
		$this->id_division = $properties['id_division'];
		$this->division_name = $properties['division_name'];
		$this->division_compet_type = $properties['division_compet_type'];
		$this->division_match_type = $properties['division_match_type'];
	}

    public function init_default_properties() {}
}
?>
