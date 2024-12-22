<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmRanking
{
	private $id_ranking;
	private $ranking_event_id;
	private $content;

	public function get_id_ranking()
	{
		return $this->id_ranking;
	}

	public function set_id_ranking($id_ranking)
	{
		$this->id_ranking = $id_ranking;
	}

	public function get_ranking_event_id()
	{
		return $this->ranking_event_id;
	}

	public function set_ranking_event_id($ranking_event_id)
	{
		$this->ranking_event_id = $ranking_event_id;
	}

	public function get_content()
	{
		return $this->content;
	}

	public function set_content($content)
	{
		$this->content = $content;
	}

	public function get_properties()
	{
		return [
			'id_ranking'       => $this->get_id_ranking(),
			'ranking_event_id' => $this->get_ranking_event_id(),
			'content'  => $this->get_content()
        ];
	}

	public function set_properties(array $properties)
	{
		$this->id_ranking = $properties['id_ranking'];
		$this->ranking_event_id = $properties['ranking_event_id'];
		$this->content = !empty($properties['content']) ? $properties['content'] : [];;
	}
}
?>
