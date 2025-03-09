<?php
/**
 * This class represents an icon fields
 * @package     Builder
 * @subpackage  Form\field
 * @copyright   &copy; 2005-2025 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2025 02 28
 * @since       PHPBoost 6.0 - 2025 02 21
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
*/

class ScmFormFieldClock extends AbstractFormField
{
    public function __construct($id, $label, $value, array $field_options = array(), array $constraints = array())
    {
        parent::__construct($id, $label, $value, $field_options, $constraints);
        $this->set_css_form_field_class('form-field-icon');
    }

    public function display()
    {
        $template = $this->get_template_to_use();

        $view = new FileTemplate('scm/fields/ScmFormFieldClock.tpl');
        $view->add_lang(LangLoader::get_all_langs());

        $this->assign_common_template_variables($template);

        $value = explode(':', $this->get_value() ?? '0:0');

        $view->put_all([
            'C_HOUR_' . $value[0] => $value[0],
            'C_MINUTES_' . $value[1] => $value[1],
            'ID'      => $this->get_html_id(),
            'HOUR'    => $value[0],
            'MINUTES' => $value[1],
        ]);

        $template->assign_block_vars('fieldelements', [
            'ELEMENT' => $view->render()
        ]);

        return $template;
    }

    public function validate()
    {
        try
		{
			$this->retrieve_value();
			return true;
		}
		catch(Exception $ex)
		{
			return $this->is_required() ? false : true;
		}
    }

    public function retrieve_value()
    {
        $this->enable();
        $request = AppContext::get_request();

        $value = '';
        $hour_id = $this->get_html_id() . '_hour';
        $minutes_id = $this->get_html_id() . '_minutes';
        if ($request->has_postparameter($hour_id) && $request->has_postparameter($minutes_id))
        {
            $hour = $request->get_poststring($hour_id);
            $minutes = $request->get_poststring($minutes_id);
            $value = ($hour . ':' . $minutes);
        }
        $this->set_value($value);
    }

    protected function get_default_template()
    {
        return new FileTemplate('framework/builder/form/FormField.tpl');
    }
}
?>
