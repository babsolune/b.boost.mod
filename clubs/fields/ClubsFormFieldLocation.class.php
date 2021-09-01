<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

class ClubsFormFieldLocation extends AbstractFormField
{
	private $max_input = 20;

	public function __construct($id, $label, array $value = array(), array $field_options = array(), array $constraints = array())
	{
		parent::__construct($id, $label, $value, $field_options, $constraints);
	}

	function display()
	{
		$template = $this->get_template_to_use();

		$view = new FileTemplate('clubs/fields/ClubsFormFieldLocation.tpl');
		$view->add_lang(array_merge(LangLoader::get('common', 'clubs'), LangLoader::get('common-lang')));
		$config = ClubsConfig::load();

		$view->put_all(array(
			'C_GMAP_API' => ModulesManager::is_module_installed('GoogleMaps') && ModulesManager::is_module_activated('GoogleMaps'),
			'NAME' 		 => $this->get_html_id(),
			'ID' 		 => $this->get_html_id(),
			'C_DISABLED' => $this->is_disabled()
		));

		$this->assign_common_template_variables($template);

		$i = 0;
		foreach ($this->get_value() as $id => $options)
		{
			$view->assign_block_vars('fieldelements', array(
				'ID' 			=> $i,
				'STREET_NUMBER' => $options['street_number'],
				'ROUTE' 		=> $options['route'],
				'CITY' 			=> $options['city'],
				'POSTAL_CODE' 	=> $options['postal_code']
			));
			$i++;
		}

		if ($i == 0)
		{
			$view->assign_block_vars('fieldelements', array(
				'ID' 			=> $i,
				'STREET_NUMBER' => '',
				'ROUTE' 		=> '',
				'CITY' 			=> '',
				'POSTAL_CODE' 	=> ''
			));
		}

		$view->put_all(array(
			'MAX_INPUT' => $this->max_input,
			'FIELDS_NUMBER' => $i == 0 ? 1 : $i
		));

		$template->assign_block_vars('fieldelements', array(
			'ELEMENT' => $view->render()
		));

		return $template;
	}

	public function retrieve_value()
	{
		$request = AppContext::get_request();
		$values = array();
		for ($i = 0; $i < $this->max_input; $i++)
		{
			$field_street_number_id = 'field_street_number_' . $this->get_html_id() . '_' . $i;
			$field_route_id         = 'field_route_' . $this->get_html_id() . '_' . $i;
			$field_postal_code_id   = 'field_postal_code_' . $this->get_html_id() . '_' . $i;
			$field_city_id          = 'field_city_' . $this->get_html_id() . '_' . $i;

			if ($request->has_postparameter($field_city_id))
			{
				$field_street_number = $request->get_poststring($field_street_number_id);
				$field_route         = $request->get_poststring($field_route_id);
				$field_postal_code   = $request->get_poststring($field_postal_code_id);
				$field_city          = $request->get_poststring($field_city_id);

				if (!empty($field_city)) {
					$values[] = array(
						'street_number' => $field_street_number,
						'route' 		=> $field_route,
						'city' 			=> $field_city,
						'postal_code' 	=> $field_postal_code
					);
				}
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
