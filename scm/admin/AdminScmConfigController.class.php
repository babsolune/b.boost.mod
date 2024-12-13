<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class AdminScmConfigController extends DefaultAdminModuleController
{
	public function execute(HTTPRequestCustom $request)
	{
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
		$form = new HTMLForm(self::class);
        $form->set_css_class('config-form');

		$fieldset = new FormFieldsetHTML('configuration', StringVars::replace_vars($this->lang['form.module.title'], ['module_name' => self::get_module()->get_configuration()->get_name()]));
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldCheckbox('left_column_disabled', StringVars::replace_vars($this->lang['form.hide.left.column'], ['module' => $this->lang['scm.module.title']]), $this->config->is_left_column_disabled(),
            ['class' => 'custom-checkbox']
        ));

        $fieldset->add_field(new FormFieldCheckbox('right_column_disabled', StringVars::replace_vars($this->lang['form.hide.right.column'], ['module' => $this->lang['scm.module.title']]), $this->config->is_right_column_disabled(),
            ['class' => 'custom-checkbox']
        ));

        $fieldset->add_field(new FormFieldSimpleSelectChoice('homepage', $this->lang['scm.config.homepage'], $this->config->get_homepage(),
			array(
				new FormFieldSelectChoiceOption($this->lang['scm.config.homepage.list'], SCMConfig::EVENT_LIST, array('data_option_icon' => 'fa fa-th-list')),
				new FormFieldSelectChoiceOption($this->lang['scm.config.homepage.games'], SCMConfig::GAME_LIST, array('data_option_icon' => 'fa fa-layer-group')),
				new FormFieldSelectChoiceOption($this->lang['scm.config.homepage.cats'], SCMConfig::CATEGORIES, array('data_option_icon' => 'fa fa-table-cells-large')),
			)
        ));

        $fieldset->add_field(new FormFieldCheckbox('current_games', $this->lang['scm.config.current.games'], $this->config->get_current_games(),
            ['class' => 'custom-checkbox']
        ));

        $fieldset->add_field(new FormFieldSpacer('colors', ''));
        $fieldset->add_field(new FormFieldColorPicker('promotion_color', $this->lang['scm.promotion.color'], $this->config->get_promotion_color()));
        $fieldset->add_field(new FormFieldColorPicker('playoff_prom_color', $this->lang['scm.playoff.prom.color'], $this->config->get_playoff_prom_color()));
        $fieldset->add_field(new FormFieldColorPicker('playoff_releg_color', $this->lang['scm.playoff.releg.color'], $this->config->get_playoff_releg_color()));
        $fieldset->add_field(new FormFieldColorPicker('relegation_color', $this->lang['scm.relegation.color'], $this->config->get_relegation_color()));

		$fieldset_authorizations = new FormFieldsetHTML('authorizations_fieldset', $this->lang['form.authorizations'],
			['description' => $this->lang['form.authorizations.clue']]
		);
		$form->add_fieldset($fieldset_authorizations);

        $fieldset_authorizations->add_field(new FormFieldFree('hide_authorizations', '', '
			<script>
                document.addEventListener("DOMContentLoaded", () => {
                    const div = document.querySelector("#' . self::class . '_authorizations > div:nth-child(3)"); // Contributions
                    div.style.display = "none";
                });
			</script>
        '));

		$auth_settings = new AuthorizationsSettings(
			array_merge(
				RootCategory::get_authorizations_settings(),
                [
					new MemberDisabledActionAuthorization($this->lang['scm.manage.clubs.auth'], ScmAuthorizationsService::CLUBS_AUTH),
					new MemberDisabledActionAuthorization($this->lang['scm.manage.divisions.auth'], ScmAuthorizationsService::DIVISIONS_AUTH),
					new MemberDisabledActionAuthorization($this->lang['scm.manage.seasons.auth'], ScmAuthorizationsService::SEASONS_AUTH),
					new MemberDisabledActionAuthorization($this->lang['scm.manage.events.auth'], ScmAuthorizationsService::EVENTS_AUTH)
				]
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

		$this->config->set_homepage($this->form->get_value('homepage')->get_raw_value());
        $this->config->set_current_games($this->form->get_value('current_games'));
        $this->config->set_promotion_color($this->form->get_value('promotion_color'));
        $this->config->set_playoff_prom_color($this->form->get_value('playoff_prom_color'));
        $this->config->set_playoff_releg_color($this->form->get_value('playoff_releg_color'));
        $this->config->set_relegation_color($this->form->get_value('relegation_color'));

		$this->config->set_authorizations($this->form->get_value('authorizations')->build_auth_array());

		ScmConfig::save();
		CategoriesService::get_categories_manager()->regenerate_cache();

		HooksService::execute_hook_action('edit_config', self::$module_id, ['title' => StringVars::replace_vars($this->lang['form.module.title'], ['module_name' => self::get_module_configuration()->get_name()]), 'url' => ModulesUrlBuilder::configuration()->rel()]);
	}
}
?>
