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
		$this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$this->form->get_field_by_id('next_matches_number')->set_hidden(!$this->config->get_next_matches());
			$this->view->put('MESSAGE_HELPER', MessageHelper::display($this->lang['warning.success.config'], MessageHelper::SUCCESS, 5));
		}

		$this->view->put('CONTENT', $this->form->display());

		return new DefaultAdminDisplayResponse($this->view);
	}

	private function build_form()
	{
		$form = new HTMLForm(self::class);

		$fieldset = new FormFieldsetHTML('configuration', StringVars::replace_vars($this->lang['form.module.title'], array('module_name' => self::get_module()->get_configuration()->get_name())));
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldCheckbox('left_column_disabled', StringVars::replace_vars($this->lang['form.hide.left.column'], array('module' => $this->lang['football.module.title'])), $this->config->is_left_column_disabled(),
            array('class' => 'custom-checkbox')
        ));

        $fieldset->add_field(new FormFieldCheckbox('right_column_disabled', StringVars::replace_vars($this->lang['form.hide.right.column'], array('module' => $this->lang['football.module.title'])), $this->config->is_right_column_disabled(),
            array('class' => 'custom-checkbox')
        ));

        $fieldset->add_field(new FormFieldSpacer('display', ''));
        $fieldset->add_field(new FormFieldCheckbox('current_matches', $this->lang['football.config.current.matches'], $this->config->get_current_matches(),
            array('class' => 'custom-checkbox')
        ));
        $fieldset->add_field(new FormFieldCheckbox('next_matches', $this->lang['football.config.next.matches'], $this->config->get_next_matches(),
            array(
                'class' => 'custom-checkbox',
                'events' => array('click' => '
                if (HTMLForms.getField("next_matches").getValue()) {
                        HTMLForms.getField("next_matches_number").enable();
                    } else {
                        HTMLForms.getField("next_matches_number").disable();
                    }
                ')
            )
        ));
        $fieldset->add_field(new FormFieldNumberEditor('next_matches_number', $this->lang['football.config.next.matches.number'], $this->config->get_next_matches_number(),
            array(
                'min' => 0, 'max' => 10, 'required' => true,
                'hidden' => !$this->config->get_next_matches()
            )
        ));

        $fieldset->add_field(new FormFieldSpacer('ranking_colors', ''));
        $fieldset->add_field(new FormFieldColorPicker('live_color', $this->lang['football.live.color'], $this->config->get_live_color()));
        $fieldset->add_field(new FormFieldColorPicker('played_color', $this->lang['football.played.color'], $this->config->get_played_color()));
        $fieldset->add_field(new FormFieldColorPicker('win_color', $this->lang['football.win.color'], $this->config->get_win_color()));

        $fieldset->add_field(new FormFieldSpacer('colors', ''));
        $fieldset->add_field(new FormFieldColorPicker('promotion_color', $this->lang['football.promotion.color'], $this->config->get_promotion_color()));
        $fieldset->add_field(new FormFieldColorPicker('playoff_color', $this->lang['football.playoff.color'], $this->config->get_playoff_color()));
        $fieldset->add_field(new FormFieldColorPicker('relegation_color', $this->lang['football.relegation.color'], $this->config->get_relegation_color()));

		$fieldset_authorizations = new FormFieldsetHTML('authorizations_fieldset', $this->lang['form.authorizations'],
			array('description' => $this->lang['form.authorizations.clue'])
		);
		$form->add_fieldset($fieldset_authorizations);

        $fieldset_authorizations->add_field(new FormFieldFree('hide_authorizations', '', '
			<script>
					jQuery(document).ready(function() {
						jQuery("#' . self::class . '_authorizations > div").eq(2).hide(); // Contributions
					});
			</script>
        '));

		$auth_settings = new AuthorizationsSettings(
			array_merge(
				RootCategory::get_authorizations_settings(), 
                [
					new MemberDisabledActionAuthorization($this->lang['football.manage.clubs.auth'],  FootballAuthorizationsService::CLUBS_AUTH),
					new MemberDisabledActionAuthorization($this->lang['football.manage.divisions.auth'],  FootballAuthorizationsService::DIVISIONS_AUTH),
					new MemberDisabledActionAuthorization($this->lang['football.manage.seasons.auth'],  FootballAuthorizationsService::SEASONS_AUTH),
					new MemberDisabledActionAuthorization($this->lang['football.manage.compets.auth'],  FootballAuthorizationsService::COMPETITIONS_AUTH)
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

        $this->config->set_current_matches($this->form->get_value('current_matches'));
        $this->config->set_next_matches($this->form->get_value('next_matches'));
        if($this->form->get_value('next_matches'))
            $this->config->set_next_matches_number($this->form->get_value('next_matches_number'));
        $this->config->set_live_color($this->form->get_value('live_color'));
        $this->config->set_played_color($this->form->get_value('played_color'));
        $this->config->set_win_color($this->form->get_value('win_color'));
        $this->config->set_promotion_color($this->form->get_value('promotion_color'));
        $this->config->set_playoff_color($this->form->get_value('playoff_color'));
        $this->config->set_relegation_color($this->form->get_value('relegation_color'));

		$this->config->set_authorizations($this->form->get_value('authorizations')->build_auth_array());

		FootballConfig::save();
		CategoriesService::get_categories_manager()->regenerate_cache();

		HooksService::execute_hook_action('edit_config', self::$module_id, array('title' => StringVars::replace_vars($this->lang['form.module.title'], array('module_name' => self::get_module_configuration()->get_name())), 'url' => ModulesUrlBuilder::configuration()->rel()));
	}
}
?>
