<?php
/**
 * @package     Builder
 * @subpackage  Form\field\constraint
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 07 04
 * @since       PHPBoost 4.1 - 2015 07 12
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

class ScmConstraintClubNameExists extends AbstractFormFieldConstraint
{
	private $club_id = 0;
	private $error_message;

	public function __construct($club_id = 0, $error_message = '')
	{
		if (!empty($club_id))
		{
			$this->club_id = $club_id;
		}

		if (empty($error_message))
		{
			$error_message = LangLoader::get_message('scm.warning.club.exists', 'clubs', 'scm');
		}
		$this->set_validation_error_message($error_message);
		$this->error_message = TextHelper::to_js_string($error_message);
	}

	public function validate(FormField $field)
	{
		return !$this->display_name_exists($field);
	}

	public function display_name_exists(FormField $field)
	{
		if (!empty($this->club_id))
		{
			return PersistenceContext::get_querier()->row_exists(ScmSetup::$scm_club_table, 'WHERE club_name = :club_name AND id_club != :club_id', array(
				'club_name' => $field->get_value(),
				'club_id' => $this->club_id
			));
		}
		else if ($field->get_value())
		{
			return PersistenceContext::get_querier()->row_exists(ScmSetup::$scm_club_table, 'WHERE club_name=:club_name', array(
				'club_name' => $field->get_value()
			));
		}
		return false;
	}

	public function get_js_validation(FormField $field)
	{
		return 'DisplayNameExistValidator(' . TextHelper::to_js_string($field->get_id()) .', '. $this->error_message . ', ' . $this->club_id . ')';
	}
}

?>
