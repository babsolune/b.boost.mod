<?php
/**
 * @copyright   &copy; 2005-2021 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.1 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

class ScmFormFieldDistrictSelect extends AbstractFormFieldChoice
{
	private $default_value;

    /**
     * Constructs a FormFieldSimpleSelectChoice.
     * @param string $id Field id
     * @param string $label Field label
     * @param mixed $value Default value (either a FormFieldEnumOption object or a string corresponding to the FormFieldEnumOption's raw value)
     * @param FormFieldEnumOption[] $options Enumeration of the possible values
     * @param array $field_options Map of the field options (this field has no specific option, there are only the inherited ones)
     * @param FormFieldConstraint List of the constraints
     */
    public function __construct($id, $label, $value, array $options, array $field_options = [], array $constraints = [])
    {
        $this->default_value = $value;
        parent::__construct($id, $label, $value, $options, $field_options, $constraints);
        $this->set_css_form_field_class('form-field-select');
    }

    /**
     * @return string The html code for the select.
     */
    public function display()
    {
        $template = $this->get_template_to_use();

        $this->assign_common_template_variables($template);

        $template->assign_block_vars('fieldelements', [
            'ELEMENT' => $this->get_html_code()->render(),
        ]);

        return $template;
    }

    private function get_html_code()
    {
        $view = new FileTemplate('scm/fields/ScmFormFieldDistrictSelect.tpl');

        $view->put_all([
            'NAME' => $this->get_html_id(),
            'ID' => $this->get_id(),
            'HTML_ID' => $this->get_html_id(),
            'CSS_CLASS' => $this->get_css_class(),
            'C_DISABLED' => $this->is_disabled(),
            'C_SELECT_TO_LIST' => $this->is_select_to_list()
        ]);

        foreach ($this->get_options() as $option)
        {
            $view->assign_block_vars('options', [], [
                'OPTION' => $option->display()
            ]);
        }

		// foreach ($this->get_value() as $options)
		// {
		// 	$view->put_all([
		// 		'COUNTRY' => $options['country'],
		// 		'LEAGUE' => $options['league'],
		// 		'DISTRICT' => $options['district'],
		// 	]);
		// }

        return $view;
    }

    protected function get_option($raw_value)
    {
        foreach ($this->get_options() as $option)
        {
            $result = $option->get_option($raw_value);
            if ($result !== null)
            {
                return $result;
            }
        }
        return null;
    }

	public function retrieve_value()
	{
		$request = AppContext::get_request();
		$values = [];
		$field_country_id = $this->get_html_id() . '-country';
        if ($request->has_postparameter($field_country_id))
        {
            $field_country = $request->get_poststring($field_country_id);
            $field_league_id = $this->get_html_id() . '-league';
            $field_league = $request->get_poststring($field_league_id);
            $field_district_id = $this->get_html_id() . '-district';
            $field_district = $request->get_poststring($field_district_id);

            if (!empty($field_country))
                $values[] = [
                    'country' => $field_country,
                    'league' => $field_league,
                    'district' => $field_district
                ];
        }
		$this->set_value($values);
	}

    protected function assign_common_template_variables(Template $template)
    {
        parent::assign_common_template_variables($template);
        $template->put('C_REQUIRED_AND_HAS_VALUE', $this->is_required() && $this->default_value);
    }

    protected function get_default_template()
    {
        return new FileTemplate('framework/builder/form/FormField.tpl');
    }

    protected function get_js_specialization_code()
    {
        return ($this->is_required() ? '
        jQuery("#'. $this->get_html_id() .'_field").change(function() {
            HTMLForms.get("' . $this->get_form_id() . '").getField("'. $this->get_id() . '").enableValidationMessage();
            HTMLForms.get("' . $this->get_form_id() . '").getField("'. $this->get_id() . '").liveValidate();
        });' : '');
    }
}
?>