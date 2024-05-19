<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class AdminFootballConfigController extends DefaultAdminModuleController
{
	// private $comments_config;
	// private $content_management_config;

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

	// private function init()
	// {
	// 	$this->comments_config = CommentsConfig::load();
	// 	$this->content_management_config = ContentManagementConfig::load();
	// }

	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);

		$fieldset = new FormFieldsetHTML('configuration', StringVars::replace_vars($this->lang['form.module.title'], array('module_name' => self::get_module()->get_configuration()->get_name())));
		$form->add_fieldset($fieldset);

		$fieldset_authorizations = new FormFieldsetHTML('authorizations_fieldset', $this->lang['form.authorizations'],
			array('description' => $this->lang['form.authorizations.clue'])
		);
		$form->add_fieldset($fieldset_authorizations);

		$auth_settings = new AuthorizationsSettings(
			array_merge(
				RootCategory::get_authorizations_settings(), 
                array(
					new VisitorDisabledActionAuthorization($this->lang['football.manage.divisions.auth'],  FootballAuthorizationsService::MANAGE_DIVISIONS_AUTHORIZATIONS),
					new VisitorDisabledActionAuthorization($this->lang['football.manage.clubs.auth'],  FootballAuthorizationsService::MANAGE_CLUBS_AUTHORIZATIONS),
					new VisitorDisabledActionAuthorization($this->lang['football.manage.seasons.auth'],  FootballAuthorizationsService::MANAGE_SEASONS_AUTHORIZATIONS),
					new VisitorDisabledActionAuthorization($this->lang['football.manage.params.auth'],  FootballAuthorizationsService::MANAGE_PARAMETERS_AUTHORIZATIONS),
					new VisitorDisabledActionAuthorization($this->lang['football.manage.teams.auth'], FootballAuthorizationsService::MANAGE_TEAMS_AUTHORIZATIONS),
					new VisitorDisabledActionAuthorization($this->lang['football.manage.matches.auth'], FootballAuthorizationsService::MANAGE_MATCHES_AUTHORIZATIONS),
					new VisitorDisabledActionAuthorization($this->lang['football.manage.results.auth'], FootballAuthorizationsService::MANAGE_RESULTS_AUTHORIZATIONS)
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
		$this->config->set_authorizations($this->form->get_value('authorizations')->build_auth_array());

		FootballConfig::save();
		CategoriesService::get_categories_manager()->regenerate_cache();

		HooksService::execute_hook_action('edit_config', self::$module_id, array('title' => StringVars::replace_vars($this->lang['form.module.title'], array('module_name' => self::get_module_configuration()->get_name())), 'url' => ModulesUrlBuilder::configuration()->rel()));
	}
}
?>
