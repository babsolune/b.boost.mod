<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballBracketsFormController extends DefaultModuleController
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
            $this->view->put('MESSAGE_HELPER', MessageHelper::display($this->lang['football.warning.bracket.update'], MessageHelper::SUCCESS, 4));
		}

		$this->view->put_all(array(
            'MENU' => FootballMenuService::build_compet_menu($this->compet_id()),
            'CONTENT' => $this->form->display(),
            'HAS_MATCHES_WARNING' => FootballMatchService::has_matches($this->compet_id()) ? MessageHelper::display($this->lang['football.warning.has.matches'], MessageHelper::NOTICE) : MessageHelper::display('', '')
        ));

		return $this->generate_response($this->view);
	}

	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);
        $form->set_css_class('cell-flex cell-columns-4');
		$form->set_layout_title('<div class="align-center small">' . $this->lang['football.brackets.management'] . '</div>');

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function save()
	{
        if (!FootballMatchService::has_matches($this->compet_id()))
        {
            FootballBracketService::set_bracket_matches($this->compet_id(), $this->get_params()->get_rounds_number());
        }
        else
        {
            FootballMatchService::delete_matches($this->compet_id());
            FootballBracketService::set_bracket_matches($this->compet_id(), $this->get_params()->get_rounds_number());
        }

		FootballCompetService::clear_cache();
	}

    private function get_params()
	{
        $id = AppContext::get_request()->get_getint('compet_id', 0);
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
		foreach(FootballTeamService::get_teams($this->compet_id()) as $team)
		{
			$options[] = new FormFieldSelectChoiceOption($team['team_club_name'], $team['id_team']);
		}

		return $options;
    }

	private function get_compet()
	{
		$id = AppContext::get_request()->get_getint('compet_id', 0);
		try {
            $this->compet = FootballCompetService::get_compet($id);
        } catch (RowNotFoundException $e) {
            $error_controller = PHPBoostErrors::unexisting_page();
            DispatchManager::redirect($error_controller);
        }
		return $this->compet;
	}

    private function compet_id()
    {
        return $this->get_compet()->get_id_compet();
    }

	private function check_authorizations()
	{
		if (!$this->get_compet()->is_authorized_to_manage_compets())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
        }

		if (AppContext::get_current_user()->is_readonly())
		{
			$controller = PHPBoostErrors::user_in_read_only();
			DispatchManager::redirect($controller);
		}
	}

	protected function get_template_string_content()
	{
		return '
            # INCLUDE MESSAGE_HELPER #
            # INCLUDE MENU #
            # INCLUDE HAS_MATCHES_WARNING #
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
        $graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::edit_brackets($compet->get_id_compet()));

        $categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($compet->get_id_category(), true));
        foreach ($categories as $id => $category)
        {
            if ($category->get_id() != Category::ROOT_CATEGORY)
                $breadcrumb->add($category->get_name(), FootballUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
        }
        $category = $compet->get_category();
        $breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::compet_home($compet->get_id_compet()));
        $breadcrumb->add($this->lang['football.teams.management'], FootballUrlBuilder::edit_brackets($compet->get_id_compet()));

		return $response;
	}
}
?>
