<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class DocsheetCommentsTopic extends CommentsTopic
{
	private $item;

	public function __construct(DocsheetItem $item = null)
	{
		parent::__construct('docsheet');
		$this->item = $item;
	}

	public function get_authorizations()
	{
		$authorizations = new CommentsAuthorizations();
		$authorizations->set_authorized_access_module(DocsheetAuthorizationsService::check_authorizations($this->get_item()->get_id_category())->read());
		return $authorizations;
	}

	public function is_displayed()
	{
		return $this->get_item()->is_published();
	}

	private function get_item()
	{
		if ($this->item === null)
		{
			$this->item = DocsheetService::get_item($this->get_id_in_module());
		}
		return $this->item;
	}
}
?>
