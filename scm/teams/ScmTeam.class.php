<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmTeam
{
	private $id_team;
	private $team_event_id;
	private $team_group;
	private $team_order;
	private $team_club_id;
	private $team_penalty;
	private $team_status;

	public function get_id_team()
	{
		return $this->id_team;
	}

	public function set_id_team($id_team)
	{
		$this->id_team = $id_team;
	}

	public function get_team_event_id()
	{
		return $this->team_event_id;
	}

	public function set_team_event_id($team_event_id)
	{
		$this->team_event_id = $team_event_id;
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

	public function get_team_status()
	{
		return $this->team_status;
	}

	public function set_team_status($team_status)
	{
		$this->team_status = $team_status;
	}

	public function is_authorized_to_manage_teams()
	{
		return ScmAuthorizationsService::check_authorizations()->manage_teams();
	}

	public function get_properties()
	{
		return [
			'id_team'       => $this->get_id_team(),
			'team_event_id' => $this->get_team_event_id(),
			'team_group'    => $this->get_team_group(),
			'team_order'    => $this->get_team_order(),
			'team_club_id'  => $this->get_team_club_id(),
			'team_penalty'  => $this->get_team_penalty(),
			'team_status'   => $this->get_team_status()
        ];
	}

	public function set_properties(array $properties)
	{
		$this->id_team       = $properties['id_team'];
		$this->team_event_id = $properties['team_event_id'];
		$this->team_group    = $properties['team_group'];
		$this->team_order    = $properties['team_order'];
		$this->team_club_id  = $properties['team_club_id'];
		$this->team_penalty  = $properties['team_penalty'];
		$this->team_status   = $properties['team_status'];
	}
}
?>
