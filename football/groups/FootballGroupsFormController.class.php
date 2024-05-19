<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballGroupsFormController extends DefaultModuleController
{
    private $compet;
    private $params;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

        $this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$this->redirect();
		}

		$this->view->put_all(array(
            'MENU' => FootballCompetMenuService::build_compet_menu($this->id_compet()),
            'CONTENT' => $this->form->display()
        ));

		return $this->generate_response($this->view);
	}

	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);
        $form->set_css_class('cell-flex cell-columns-4');
		$form->set_layout_title('<div class="align-center small">' . $this->lang['football.groups.management'] . '</div>');

		$teams_number = FootballTeamService::get_compet_teams_number($this->id_compet());
        $teams_per_group = $this->get_params()->get_teams_per_group();
        $groups_number = (int)($teams_number / $teams_per_group);

        for ($i = 1; $i <= $groups_number; $i++)
        {
            $fieldset = new FormFieldsetHTML('group_' . $i, $this->lang['football.group'] . ' ' . FootballGroupService::ntl($i));
            $form->add_fieldset($fieldset);

            for ($j = 1; $j <= $teams_per_group; $j++)
            {
                $team_id = FootballTeamService::get_team_in_group($this->id_compet(), $i . $j) ? FootballTeamService::get_team_in_group($this->id_compet(), $i . $j)->get_id_team() : '';
                $fieldset->add_field(new FormFieldSimpleSelectChoice('group_teams_' . $i . $j, '', $team_id,
                    $this->get_teams_list($this->id_compet()),
                    array('class' => 'groups-select')
                ));
            }
        }

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function save()
	{
        $teams_number = FootballTeamService::get_compet_teams_number($this->id_compet());
        $teams_per_group = $this->get_params()->get_teams_per_group();
        $groups_number = (int)($teams_number / $teams_per_group);

        // unselect all teams to manage changes
        foreach(FootballTeamService::get_teams($this->id_compet()) as $team)
        {
            FootballTeamService::update_team_group($team['id_team'], 0);
        }

        for ($i = 1; $i <= $groups_number; $i++)
        {
            for ($j = 1; $j <= $teams_per_group; $j++)
            {
                $id = $this->form->get_value('group_teams_' . $i . $j)->get_raw_value();
                FootballTeamService::update_team_group($id, $i.$j);
            }
        }

        if (!$this->get_compet()->get_has_matches())
            FootballGroupService::build_matches_from_groups($this->id_compet());
        FootballCompetService::set_matches_flag($this->id_compet());

		FootballCompetService::clear_cache();
	}

    private function get_params()
	{
        $id = AppContext::get_request()->get_getint('id', 0);
        if (!empty($id))
        {
            try {
                $this->params = FootballParamsService::get_params($id);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        }
		return $this->params;
	}

    private function get_teams_list()
    {
        $options = array();

        $options[] = new FormFieldSelectChoiceOption('', '');
		foreach(FootballTeamService::get_teams($this->id_compet()) as $team)
		{
			$options[] = new FormFieldSelectChoiceOption($team['team_club_name'], $team['id_team']);
		}

		return $options;
    }

	private function get_compet()
	{
		$id = AppContext::get_request()->get_getint('id', 0);
		try {
            $this->compet = FootballCompetService::get_compet($id);
        } catch (RowNotFoundException $e) {
            $error_controller = PHPBoostErrors::unexisting_page();
            DispatchManager::redirect($error_controller);
        }
		return $this->compet;
	}

    private function id_compet()
    {
        return $this->get_compet()->get_id_compet();
    }

	private function check_authorizations()
	{
		// $compet = $this->get_compet();

		// if ($compet->get_id_compet() === null)
		// {
		// 	if (!$compet->is_authorized_to_manage())
		// 	{
		// 		$error_controller = PHPBoostErrors::user_not_authorized();
		// 		DispatchManager::redirect($error_controller);
		// 	}
		// }
		// else
		// {
		// 	if (!$compet->is_authorized_to_manage())
		// 	{
		// 		$error_controller = PHPBoostErrors::user_not_authorized();
		// 		DispatchManager::redirect($error_controller);
		// 	}
		// }
		if (AppContext::get_current_user()->is_readonly())
		{
			$controller = PHPBoostErrors::user_in_read_only();
			DispatchManager::redirect($controller);
		}
	}

	private function redirect()
	{
		AppContext::get_response()->redirect(FootballUrlBuilder::groups($this->id_compet()));
	}

	protected function get_template_string_content()
	{
		return '
            # INCLUDE MESSAGE_HELPER #
            # INCLUDE MENU #
            # INCLUDE CONTENT #
        ';
	}

	private function generate_response(View $view)
	{
		$compet = $this->get_compet();

		$location_id = $compet->get_id_compet() ? 'football-edit-'. $compet->get_id_compet() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'], FootballUrlBuilder::home());

		if (!AppContext::get_session()->location_id_already_exists($location_id))
            $graphical_environment->set_location_id($location_id);

        $graphical_environment->set_page_title($this->lang['football.teams.management'], $this->lang['football.module.title']);
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['football.teams.management']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::groups($compet->get_id_compet()));

        $categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($compet->get_id_category(), true));
        foreach ($categories as $id => $category)
        {
            if ($category->get_id() != Category::ROOT_CATEGORY)
                $breadcrumb->add($category->get_name(), RecipeUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
        }
        $category = $compet->get_category();
        $breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $compet->get_id_compet(), $compet->get_compet_slug()));
        $breadcrumb->add($this->lang['football.teams.management'], FootballUrlBuilder::groups($compet->get_id_compet()));

		return $response;
	}
}
?>
