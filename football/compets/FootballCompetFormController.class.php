<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

// namespace PHPBoost\Football\Controllers;
// use PHPBoost;

class FootballCompetFormController extends DefaultModuleController
{
    private $compet;
    private $is_new_compet;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_form($request);

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$this->redirect();
		}

		$this->view->put('CONTENT', $this->form->display());

		return $this->generate_response($this->view);
	}

	private function build_form(HTTPRequestCustom $request)
	{
		$form = new HTMLForm(__CLASS__);
		$form->set_layout_title($this->compet_id() === null ? $this->lang['football.add.compet'] : ($this->lang['football.edit.compet']));

		$fieldset = new FormFieldsetHTML('football', $this->lang['form.parameters']);
		$form->add_fieldset($fieldset);

		if (CategoriesService::get_categories_manager()->get_categories_cache()->has_categories())
		{
			$search_category_children_options = new SearchCategoryChildrensOptions();
			$search_category_children_options->add_authorizations_bits(Category::CONTRIBUTION_AUTHORIZATIONS);
			$search_category_children_options->add_authorizations_bits(Category::WRITE_AUTHORIZATIONS);
			$fieldset->add_field(CategoriesService::get_categories_manager()->get_select_categories_form_field('id_category', $this->lang['category.category'], $this->get_compet()->get_id_category(), $search_category_children_options));
		}

		$fieldset->add_field(new FormFieldSimpleSelectChoice('division', $this->lang['football.division'], $this->compet->get_compet_division_id(), $this->divisions_list(),
			array('required' => true)
		));

		$fieldset->add_field(new FormFieldSimpleSelectChoice('season', $this->lang['football.season'], $this->compet->get_compet_season_id(), $this->seasons_list(),
			array('required' => true)
		));

		if (FootballAuthorizationsService::check_authorizations($this->get_compet()->get_id_category())->moderation())
		{
			$publication_fieldset = new FormFieldsetHTML('publication', $this->lang['form.publication']);
			$form->add_fieldset($publication_fieldset);

			$publication_fieldset->add_field(new FormFieldDateTime('creation_date', $this->lang['form.creation.date'], $this->get_compet()->get_creation_date(),
				array('required' => true)
			));

			if (!$this->get_compet()->is_published())
			{
				$publication_fieldset->add_field(new FormFieldCheckbox('update_creation_date', $this->lang['form.update.creation.date'], false,
					array('hidden' => $this->get_compet()->get_publishing_state() != FootballCompet::NOT_PUBLISHED)
				));
			}

			$publication_fieldset->add_field(new FormFieldSimpleSelectChoice('published', $this->lang['form.publication'], $this->get_compet()->get_publishing_state(),
				array(
					new FormFieldSelectChoiceOption($this->lang['form.publication.draft'], FootballCompet::NOT_PUBLISHED),
					new FormFieldSelectChoiceOption($this->lang['form.publication.now'], FootballCompet::PUBLISHED),
					new FormFieldSelectChoiceOption($this->lang['form.publication.deffered'], FootballCompet::DEFERRED_PUBLICATION),
				),
				array(
					'events' => array('change' => '
						if (HTMLForms.getField("published").getValue() == ' . FootballCompet::DEFERRED_PUBLICATION . ') {
							jQuery("#' . __CLASS__ . '_publishing_start_date_field").show();
							HTMLForms.getField("end_date_enabled").enable();
							if (HTMLForms.getField("end_date_enabled").getValue()) {
								HTMLForms.getField("publishing_end_date").enable();
							}
						} else {
							jQuery("#' . __CLASS__ . '_publishing_start_date_field").hide();
							HTMLForms.getField("end_date_enabled").disable();
							HTMLForms.getField("publishing_end_date").disable();
						}'
					)
				)
			));

			$publication_fieldset->add_field($publishing_start_date = new FormFieldDateTime('publishing_start_date', $this->lang['form.start.date'], ($this->get_compet()->get_publishing_start_date() === null ? new Date() : $this->get_compet()->get_publishing_start_date()),
				array('hidden' => ($request->is_post_method() ? ($request->get_postint(__CLASS__ . '_publication_state', 0) != FootballCompet::DEFERRED_PUBLICATION) : ($this->get_compet()->get_publishing_state() != FootballCompet::DEFERRED_PUBLICATION)))
			));

			$publication_fieldset->add_field(new FormFieldCheckbox('end_date_enabled', $this->lang['form.enable.end.date'], $this->get_compet()->is_end_date_enabled(),
				array(
					'hidden' => ($request->is_post_method() ? ($request->get_postint(__CLASS__ . '_publication_state', 0) != FootballCompet::DEFERRED_PUBLICATION) : ($this->get_compet()->get_publishing_state() != FootballCompet::DEFERRED_PUBLICATION)),
					'events' => array('click' => '
						if (HTMLForms.getField("end_date_enabled").getValue()) {
							HTMLForms.getField("publishing_end_date").enable();
						} else {
							HTMLForms.getField("publishing_end_date").disable();
						}'
					)
				)
			));

			$publication_fieldset->add_field($publishing_end_date = new FormFieldDateTime('publishing_end_date', $this->lang['form.end.date'], ($this->get_compet()->get_publishing_end_date() === null ? new Date() : $this->get_compet()->get_publishing_end_date()),
				array('hidden' => ($request->is_post_method() ? !$request->get_postbool(__CLASS__ . '_end_date_enabled', false) : !$this->get_compet()->is_end_date_enabled()))
			));

			$publishing_end_date->add_form_constraint(new FormConstraintFieldsDifferenceSuperior($publishing_start_date, $publishing_end_date));
		}

		$fieldset->add_field(new FormFieldHidden('referrer', $request->get_url_referrer()));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function seasons_list()
	{
		$options = array();
		$cache = FootballSeasonCache::load();
		$seasons_list = $cache->get_seasons();

		// laisser un vide en début de liste
		$options[] = new FormFieldSelectChoiceOption('', '');

		$i = 1;
		foreach($seasons_list as $season)
		{
			$options[] = new FormFieldSelectChoiceOption($season['season_name'], $season['id_season']);
			$i++;
		}

		return $options;
	}

	private function divisions_list()
	{
		$options = array();
		$cache = FootballDivisionCache::load();
		$divisions_list = $cache->get_divisions();

		// laisser un vide en début de liste
		$options[] = new FormFieldSelectChoiceOption('', '');

		$i = 1;
		foreach($divisions_list as $division)
		{
			$options[] = new FormFieldSelectChoiceOption($division['division_name'], $division['id_division']);
			$i++;
		}

		return $options;
	}

	private function get_compet()
	{
		if ($this->compet === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try {
					$this->compet = FootballCompetService::get_compet($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_compet = true;
				$this->compet = new FootballCompet();
				$this->compet->init_default_properties(AppContext::get_request()->get_getint('id_category', Category::ROOT_CATEGORY));
			}
		}
		return $this->compet;
	}

    private function compet_id()
    {
        return $this->get_compet()->get_id_compet();
    }

	private function check_authorizations()
	{
		$compet = $this->get_compet();

		if ($compet->get_id_compet() === null)
		{
			if (!$compet->is_authorized_to_add())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!$compet->is_authorized_to_edit())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		if (AppContext::get_current_user()->is_readonly())
		{
			$controller = PHPBoostErrors::user_in_read_only();
			DispatchManager::redirect($controller);
		}
	}

	private function save()
	{
		$compet = $this->get_compet();

		$compet->set_compet_division_id($this->form->get_value('division')->get_raw_value());
		$compet->set_compet_season_id($this->form->get_value('season')->get_raw_value());

		$division_title = $this->form->get_value('division')->get_label();
		$season_title = $this->form->get_value('season')->get_label();
		$compet->set_compet_name($season_title . ' - ' . $division_title);

		if (CategoriesService::get_categories_manager()->get_categories_cache()->has_categories())
			$compet->set_id_category($this->form->get_value('id_category')->get_raw_value());

		$compet->set_sources($this->form->get_value('sources'));

		if (!FootballAuthorizationsService::check_authorizations($compet->get_id_category())->moderation())
		{
			$compet->clean_publishing_start_and_end_date();

			if (FootballAuthorizationsService::check_authorizations($compet->get_id_category())->contribution() && !FootballAuthorizationsService::check_authorizations($compet->get_id_category())->write())
				$compet->set_publishing_state(FootballCompet::NOT_PUBLISHED);
		}
		else
		{
			if ($this->form->get_value('update_creation_date'))
				$compet->set_creation_date(new Date());
			else
				$compet->set_creation_date($this->form->get_value('creation_date'));

			$compet->set_publishing_state($this->form->get_value('published')->get_raw_value());
			if ($compet->get_publishing_state() == FootballCompet::DEFERRED_PUBLICATION)
			{
				$deferred_operations = $this->config->get_deferred_operations();

				$old_publishing_start_date = $compet->get_publishing_start_date();
				$publishing_start_date = $this->form->get_value('publishing_start_date');
				$compet->set_publishing_start_date($publishing_start_date);

				if ($old_publishing_start_date !== null && $old_publishing_start_date->get_timestamp() != $publishing_start_date->get_timestamp() && in_array($old_publishing_start_date->get_timestamp(), $deferred_operations))
				{
					$key = array_search($old_publishing_start_date->get_timestamp(), $deferred_operations);
					unset($deferred_operations[$key]);
				}

				if (!in_array($publishing_start_date->get_timestamp(), $deferred_operations))
					$deferred_operations[] = $publishing_start_date->get_timestamp();

				if ($this->form->get_value('end_date_enabled'))
				{
					$old_publishing_end_date = $compet->get_publishing_end_date();
					$publishing_end_date = $this->form->get_value('publishing_end_date');
					$compet->set_publishing_end_date($publishing_end_date);

					if ($old_publishing_end_date !== null && $old_publishing_end_date->get_timestamp() != $publishing_end_date->get_timestamp() && in_array($old_publishing_end_date->get_timestamp(), $deferred_operations))
					{
						$key = array_search($old_publishing_end_date->get_timestamp(), $deferred_operations);
						unset($deferred_operations[$key]);
					}

					if (!in_array($publishing_end_date->get_timestamp(), $deferred_operations))
						$deferred_operations[] = $publishing_end_date->get_timestamp();
				}
				else
					$compet->clean_publishing_end_date();

				$this->config->set_deferred_operations($deferred_operations);
				FootballConfig::save();
			}
			else
				$compet->clean_publishing_start_and_end_date();
		}

		if ($this->is_new_compet)
		{
			$id = FootballCompetService::add($compet);
			$compet->set_id($id);

			HooksService::execute_hook_action('add', self::$module_id, array_merge($compet->get_properties(), array('compet_url' => $compet->get_compet_url())));
		}
		else
		{
			$compet->set_update_date(new Date());
			FootballCompetService::update($compet);

			HooksService::execute_hook_action('edit', self::$module_id, array_merge($compet->get_properties(), array('compet_url' => $compet->get_compet_url())));
		}

		FootballCompetService::clear_cache();
	}

	private function redirect()
	{
		$compet = $this->get_compet();

		if ($compet->is_published())
		{
			if ($this->is_new_compet)
				AppContext::get_response()->redirect(FootballUrlBuilder::compet_home($compet->get_id_compet()), StringVars::replace_vars($this->lang['football.message.success.add'], array('title' => $compet->get_compet_name())));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : FootballUrlBuilder::compet_home($compet->get_id_compet())), StringVars::replace_vars($this->lang['football.message.success.edit'], array('title' => $compet->get_compet_name())));
		}
		else
		{
			// if ($this->is_new_compet)
			// 	AppContext::get_response()->redirect(FootballUrlBuilder::display_pending(), StringVars::replace_vars($this->lang['football.message.success.add'], array('title' => $compet->get_compet_name())));
			// else
			// 	AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : FootballUrlBuilder::display_pending()), StringVars::replace_vars($this->lang['football.message.success.edit'], array('title' => $compet->get_compet_name())));
		}
	}

	private function generate_response(View $view)
	{
		$compet = $this->get_compet();

		$location_id = $compet->get_id_compet() ? 'football-edit-'. $compet->get_id_compet() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'], FootballUrlBuilder::home());

		if ($compet->get_id_compet() === null)
		{
			$breadcrumb->add($this->lang['football.add.compet'], FootballUrlBuilder::add($compet->get_id_category()));
			$graphical_environment->set_page_title($this->lang['football.add.compet'], $this->lang['football.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['football.add.compet']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::add($compet->get_id_category()));
		}
		else
		{
			if (!AppContext::get_session()->location_id_already_exists($location_id))
				$graphical_environment->set_location_id($location_id);

			$graphical_environment->set_page_title($this->lang['football.edit.compet'], $this->lang['football.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['football.edit.compet']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::edit($compet->get_id_compet()));

			$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($compet->get_id_category(), true));
			foreach ($categories as $id => $category)
			{
				if ($category->get_id() != Category::ROOT_CATEGORY)
					$breadcrumb->add($category->get_name(), FootballUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
			}
			$category = $compet->get_category();
			$breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::compet_home($compet->get_id_compet()));
			$breadcrumb->add($this->lang['football.edit.compet'], FootballUrlBuilder::edit($compet->get_id_compet()));
		}

		return $response;
	}
}
?>
