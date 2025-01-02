<?php
/**
 * @copyright   &copy; 2005-2021 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 07 24
 * @since       PHPBoost 5.0 - 2024 07 24
*/

class ScmFormFieldGameEvents extends AbstractFormField
{
	private $max_input = 200;

	public function __construct($id, $label, array $value = [], array $field_options = [], array $constraints = [])
	{
		parent::__construct($id, $label, $value, $field_options, $constraints);
	}

	function display()
	{
		$template = $this->get_template_to_use();

		$view = new FileTemplate('scm/fields/ScmFormFieldGameEvents.tpl');
		$view->add_lang(array_merge(LangLoader::get('common', 'scm'), LangLoader::get('common-lang')));

		$view->put_all([
			'NAME'       => $this->get_html_id(),
			'ID'         => $this->get_html_id(),
			'C_DISABLED' => $this->is_disabled()
		]);

		$this->assign_common_template_variables($template);

		$i = 0;
		foreach ($this->get_value() as $id => $options)
		{
			$view->assign_block_vars('fieldelements', [
				'ID'    => $i,
				'PLAYER'  => $options['player'],
				'TIME'  => $options['time'],
			]);
			$i++;
		}

		if ($i == 0)
		{
			$view->assign_block_vars('fieldelements', [
				'ID'    => $i,
				'PLAYER'  => '',
				'TIME'  => '',
			]);
		}

		$view->put_all([
			'NAME'          => $this->get_html_id(),
			'ID'            => $this->get_html_id(),
			'C_DISABLED'    => $this->is_disabled(),
			'MAX_INPUT'     => $this->max_input,
			'FIELDS_NUMBER' => $i == 0 ? 1 : $i
		]);

		$template->assign_block_vars('fieldelements', [
			'ELEMENT' => $view->render()
		]);

		return $template;
	}

	public function retrieve_value()
	{
		$request = AppContext::get_request();

        $values = [];
		for ($i = 0; $i < $this->max_input; $i++)
		{
            $field_player_id = 'field_player_' . $this->get_html_id() . '_' . $i;
            $field_time_id = 'field_time_' . $this->get_html_id() . '_' . $i;
			if ($request->has_postparameter($field_player_id) || $request->has_postparameter($field_time_id))
			{
				$field_player = $request->get_poststring($field_player_id);
				$field_time = $request->get_poststring($field_time_id);

				if (!empty($field_action) || !empty($field_player) || !empty($field_time))
					$values[] = ['player' => $field_player, 'time' => $field_time];
			}
		}
		$this->set_value($values);
	}

	protected function compute_options(array &$field_options)
	{
		foreach($field_options as $attribute => $value)
		{
			$attribute = strtolower($attribute);
			switch ($attribute)
			{
				case 'max_input':
					$this->max_input = $value;
					unset($field_options['max_input']);
					break;
			}
		}
		parent::compute_options($field_options);
	}

	protected function get_default_template()
	{
		return new FileTemplate('framework/builder/form/FormField.tpl');
	}
}
?>