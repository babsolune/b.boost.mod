<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmDivision
{
	private $id_division;
	private $division_name;

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

	public function is_authorized_to_manage()
	{
		return ScmAuthorizationsService::check_authorizations()->manage_divisions();
	}

	public function get_properties()
	{
		return [
			'id_division'   => $this->get_id_division(),
			'division_name' => $this->get_division_name()
        ];
	}

	public function set_properties(array $properties)
	{
		$this->id_division   = $properties['id_division'];
		$this->division_name = $properties['division_name'];
	}

    public function init_default_properties() {}
}
?>
