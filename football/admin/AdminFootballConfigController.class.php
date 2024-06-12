<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class AdminFootballConfigController extends DefaultAdminModuleController
{
	public function execute(HTTPRequestCustom $request)
	{
		// $this->init();

		$this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$this->view->put('MESSAGE_HELPER', MessageHelper::display($this->lang['warning.success.config'], MessageHelper::SUCCESS, 5));
		}

		$this->view->put('CONTENT', $this->form->display());

		return new DefaultAdminDisplayResponse($this->view);
	}

	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);

		$fieldset = new FormFieldsetHTML('configuration', StringVars::replace_vars($this->lang['form.module.title'], array('module_name' => self::get_module()->get_configuration()->get_name())));
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldCheckbox('left_column_disabled', StringVars::replace_vars($this->lang['form.hide.left.column'], array('module' => $this->lang['football.module.title'])), $this->config->is_left_column_disabled(),
            array('class' => 'custom-checkbox')
        ));

        $fieldset->add_field(new FormFieldCheckbox('right_column_disabled', StringVars::replace_vars($this->lang['form.hide.right.column'], array('module' => $this->lang['football.module.title'])), $this->config->is_right_column_disabled(),
            array('class' => 'custom-checkbox')
        ));

		$fieldset_authorizations = new FormFieldsetHTML('authorizations_fieldset', $this->lang['form.authorizations'],
			array('description' => $this->lang['form.authorizations.clue'])
		);
		$form->add_fieldset($fieldset_authorizations);

        $fieldset_authorizations->add_field(new FormFieldFree('hide_authorizations', '', '
			<script>
				<!--
					jQuery(document).ready(function() {
						jQuery("#' . __CLASS__ . '_authorizations > div").eq(2).hide();
						jQuery("#' . __CLASS__ . '_authorizations > div").eq(4).hide();
					});
				-->
			</script>
        '));

		$auth_settings = new AuthorizationsSettings(
			array_merge(
				RootCategory::get_authorizations_settings(), 
                array(
					new MemberDisabledActionAuthorization($this->lang['football.manage.clubs.auth'],  FootballAuthorizationsService::CLUBS_AUTHORIZATIONS),
					new MemberDisabledActionAuthorization($this->lang['football.manage.divisions.auth'],  FootballAuthorizationsService::DIVISIONS_AUTHORIZATIONS),
					new MemberDisabledActionAuthorization($this->lang['football.manage.seasons.auth'],  FootballAuthorizationsService::SEASONS_AUTHORIZATIONS),
					new MemberDisabledActionAuthorization($this->lang['football.manage.compets.auth'],  FootballAuthorizationsService::COMPETITION_AUTHORIZATIONS)
				)
			)
		);
		$auth_settings->build_from_auth_array($this->config->get_authorizations());
		$fieldset_authorizations->add_field(new FormFieldAuthorizationsSetter('authorizations', $auth_settings));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function save()
	{
		if ($this->form->get_value('left_column_disabled'))
			$this->config->disable_left_column();
		else
			$this->config->enable_left_column();

		if ($this->form->get_value('right_column_disabled'))
			$this->config->disable_right_column();
		else
			$this->config->enable_right_column();

		$this->config->set_authorizations($this->form->get_value('authorizations')->build_auth_array());

		FootballConfig::save();
		CategoriesService::get_categories_manager()->regenerate_cache();

		HooksService::execute_hook_action('edit_config', self::$module_id, array('title' => StringVars::replace_vars($this->lang['form.module.title'], array('module_name' => self::get_module_configuration()->get_name())), 'url' => ModulesUrlBuilder::configuration()->rel()));
	}
}
?>
