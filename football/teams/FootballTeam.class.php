<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballTeam
{
	private $id_team;
	private $team_compet_id;
	private $team_group;
	private $team_order;
	private $team_club_id;
	private $team_club_name;
	private $team_penalty;

	public function get_id_team()
	{
		return $this->id_team;
	}

	public function set_id_team($id_team)
	{
		$this->id_team = $id_team;
	}

	public function get_team_club_name()
	{
		return $this->team_club_name;
	}

	public function set_team_club_name($team_club_name)
	{
		$this->team_club_name = $team_club_name;
	}

	public function get_team_compet_id()
	{
		return $this->team_compet_id;
	}

	public function set_team_compet_id($team_compet_id)
	{
		$this->team_compet_id = $team_compet_id;
	}

	public function get_team_group()
	{
		return $this->team_group;
	}

	public function set_team_group($team_group)
	{
		$this->team_group = $team_group;
	}

	public function get_team_order()
	{
		return $this->team_order;
	}

	public function set_team_order($team_order)
	{
		$this->team_order = $team_order;
	}

	public function get_team_club_id()
	{
		return $this->team_club_id;
	}

	public function set_team_club_id($team_club_id)
	{
		$this->team_club_id = $team_club_id;
	}

	public function get_team_penalty()
	{
		return $this->team_penalty;
	}

	public function set_team_penalty($team_penalty)
	{
		$this->team_penalty = $team_penalty;
	}

	public function is_authorized_to_manage_teams()
	{
		return FootballAuthorizationsService::check_authorizations()->manage_teams();
	}

	public function get_properties()
	{
		return array(
			'id_team' => $this->get_id_team(),
			'team_compet_id' => $this->get_team_compet_id(),
			'team_group' => $this->get_team_group(),
			'team_order' => $this->get_team_order(),
			'team_club_id' => $this->get_team_club_id(),
			'team_club_name' => $this->get_team_club_name(),
			'team_penalty' => $this->get_team_penalty()
		);
	}

	public function set_properties(array $properties)
	{
		$this->id_team = $properties['id_team'];
		$this->team_compet_id = $properties['team_compet_id'];
		$this->team_group = $properties['team_group'];
		$this->team_order = $properties['team_order'];
		$this->team_club_id = $properties['team_club_id'];
		$this->team_club_name = $properties['team_club_name'];
		$this->team_penalty = $properties['team_penalty'];
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
				'TEAM_ID' => $this->id_team,
				'TEAM_NAME' => $this->team_club_name,

				// Links
				// 'U_EDIT_PARAMETERS'           => FootballUrlBuilder::edit_params($this->team_compet_id)->rel(),
			)
		);
	}
}
?>
