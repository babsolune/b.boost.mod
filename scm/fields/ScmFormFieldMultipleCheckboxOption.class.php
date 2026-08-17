<?php
/**
 * @package     Builder
 * @subpackage  Form\field\enum
 * @copyright   &copy; 2005-2026 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Benoit SAUTEL <ben.popeye@phpboost.com>
 * @version     PHPBoost 6.1 - last update: 2026 05 19
 * @since       PHPBoost 3.0 - 2010 11 20
 * @author      Arnaud GENET <elenwii@phpboost.com>
*/

class ScmFormFieldMultipleCheckboxOption
{
	private $id;
	private $label;
	private $sort_id;

	public function __construct(string $id, string $label, string $sort_id)
	{
		$this->id = $id;
		$this->label = $label;
		$this->sort_id = $sort_id;
	}

	public function get_id()
	{
		return $this->id;
	}

	public function get_label()
	{
		return $this->label;
	}

	public function get_sort_id()
	{
		return $this->sort_id;
	}
}

?>
