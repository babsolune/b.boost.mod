<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

// namespace PHPBoost\Scm\Controllers;
// use PHPBoost;

class ScmEventFormController extends DefaultModuleController
{
    private $event;
    private $is_new_event;

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
		$form = new HTMLForm(self::class);
		$form->set_layout_title($this->event_id() === null ? $this->lang['scm.add.event'] : ($this->lang['scm.edit.event']));

		$fieldset = new FormFieldsetHTML('scm', $this->lang['form.parameters']);
		$form->add_fieldset($fieldset);

		if (CategoriesService::get_categories_manager()->get_categories_cache()->has_categories())
		{
			$search_category_children_options = new SearchCategoryChildrensOptions();
			$search_category_children_options->add_authorizations_bits(Category::CONTRIBUTION_AUTHORIZATIONS);
			$search_category_children_options->add_authorizations_bits(Category::WRITE_AUTHORIZATIONS);
			$fieldset->add_field(CategoriesService::get_categories_manager()->get_select_categories_form_field('id_category', $this->lang['category.category'], $this->get_event()->get_id_category(), $search_category_children_options));
		}

		$fieldset->add_field(new FormFieldSimpleSelectChoice('division', $this->lang['scm.division'], $this->event->get_division_id(), $this->divisions_list(),
			['required' => true]
		));

		$fieldset->add_field(new FormFieldSimpleSelectChoice('season', $this->lang['scm.season'], $this->event->get_season_id(), $this->seasons_list(),
			['required' => true]
		));

		$fieldset->add_field(new FormFieldSimpleSelectChoice('scoring_type', $this->lang['scm.event.scoring.type'], $this->event->get_scoring_type(), 
            [
                new FormFieldSelectChoiceOption('', ''),
                new FormFieldSelectChoiceOption($this->lang['scm.event.scoring.goals'], ScmEvent::SCORING_GOALS),
                new FormFieldSelectChoiceOption($this->lang['scm.event.scoring.tries'], ScmEvent::SCORING_TRIES),
                new FormFieldSelectChoiceOption($this->lang['scm.event.scoring.points'], ScmEvent::SCORING_POINTS),
                new FormFieldSelectChoiceOption($this->lang['scm.event.scoring.sets'], ScmEvent::SCORING_SETS)
            ],
			['required' => true]
		));

        $fieldset->add_field(new FormFieldCheckbox('is_sub', $this->lang['scm.event.is.sub'], $this->event->get_is_sub(),
            [
                'events' => ['click' => '
                    if (HTMLForms.getField("is_sub").getValue()) {
                        HTMLForms.getField("master_id").enable();
                        HTMLForms.getField("sub_order").enable();
                    } else {
                        HTMLForms.getField("master_id").disable();
                        HTMLForms.getField("sub_order").disable();
                    }
                ']
            ]
        ));

		$fieldset->add_field(new FormFieldSimpleSelectChoice('master_id', $this->lang['scm.event.master.id'], $this->event->get_master_id(),
            $this->get_events_list(),
			[
                'required' => true,
                'hidden' => !$this->event->get_is_sub()
            ]
		));

        $fieldset->add_field($start_date = new FormFieldNumberEditor('sub_order', $this->lang['scm.event.sub.order'], $this->event->get_sub_order(),
            [
                'min' => 0, 'required' => true,
                'hidden' => !$this->event->get_is_sub()
            ]
        ));

        $fieldset->add_field($start_date = new FormFieldDateTime('start_date', $this->lang['scm.event.start.date'], $this->get_event()->get_start_date(),
            ['required' => true]
        ));

        $fieldset->add_field($end_date = new FormFieldDateTime('end_date', $this->lang['scm.event.end.date'], $this->get_event()->get_end_date(),
            ['required' => true]
        ));

        $end_date->add_form_constraint(new FormConstraintFieldsDifferenceSuperior($start_date, $end_date));

        $fieldset->add_field(new FormFieldSelectSources('sources', $this->lang['form.sources'], $this->get_event()->get_sources(),
            ['description' => $this->lang['scm.source.clue']]
        ));

		if (ScmAuthorizationsService::check_authorizations($this->get_event()->get_id_category())->moderation())
		{
			$publication_fieldset = new FormFieldsetHTML('publication', $this->lang['form.publication']);
			$form->add_fieldset($publication_fieldset);

			$publication_fieldset->add_field(new FormFieldDateTime('creation_date', $this->lang['form.creation.date'], $this->get_event()->get_creation_date(),
				['required' => true]
			));

			if (!$this->get_event()->is_published())
			{
				$publication_fieldset->add_field(new FormFieldCheckbox('update_creation_date', $this->lang['form.update.creation.date'], false,
					['hidden' => $this->get_event()->get_publishing_state() != ScmEvent::NOT_PUBLISHED]
				));
			}

			$publication_fieldset->add_field(new FormFieldSimpleSelectChoice('published', $this->lang['form.publication'], $this->get_event()->get_publishing_state(),
				[
					new FormFieldSelectChoiceOption($this->lang['form.publication.draft'], ScmEvent::NOT_PUBLISHED),
					new FormFieldSelectChoiceOption($this->lang['form.publication.now'], ScmEvent::PUBLISHED),
					new FormFieldSelectChoiceOption($this->lang['form.publication.deffered'], ScmEvent::DEFERRED_PUBLICATION),
                ],
				[
					'events' => ['change' => '
						if (HTMLForms.getField("published").getValue() == ' . ScmEvent::DEFERRED_PUBLICATION . ') {
							jQuery("#' . self::class . '_publishing_start_date_field").show();
							HTMLForms.getField("end_date_enabled").enable();
							if (HTMLForms.getField("end_date_enabled").getValue()) {
								HTMLForms.getField("publishing_end_date").enable();
							}
						} else {
							jQuery("#' . self::class . '_publishing_start_date_field").hide();
							HTMLForms.getField("end_date_enabled").disable();
							HTMLForms.getField("publishing_end_date").disable();
						}'
                    ]
                ]
			));

			$publication_fieldset->add_field($publishing_start_date = new FormFieldDateTime('publishing_start_date', $this->lang['form.start.date'], ($this->get_event()->get_publishing_start_date() === null ? new Date() : $this->get_event()->get_publishing_start_date()),
				['hidden' => ($request->is_post_method() ? ($request->get_postint(self::class . '_publication_state', 0) != ScmEvent::DEFERRED_PUBLICATION) : ($this->get_event()->get_publishing_state() != ScmEvent::DEFERRED_PUBLICATION))]
			));

			$publication_fieldset->add_field(new FormFieldCheckbox('end_date_enabled', $this->lang['form.enable.end.date'], $this->get_event()->is_end_date_enabled(),
				[
					'hidden' => ($request->is_post_method() ? ($request->get_postint(self::class . '_publication_state', 0) != ScmEvent::DEFERRED_PUBLICATION) : ($this->get_event()->get_publishing_state() != ScmEvent::DEFERRED_PUBLICATION)),
					'events' => ['click' => '
						if (HTMLForms.getField("end_date_enabled").getValue()) {
							HTMLForms.getField("publishing_end_date").enable();
						} else {
							HTMLForms.getField("publishing_end_date").disable();
						}'
                    ]
                ]
			));

			$publication_fieldset->add_field($publishing_end_date = new FormFieldDateTime('publishing_end_date', $this->lang['form.end.date'], ($this->get_event()->get_publishing_end_date() === null ? new Date() : $this->get_event()->get_publishing_end_date()),
				['hidden' => ($request->is_post_method() ? !$request->get_postbool(self::class . '_end_date_enabled', false) : !$this->get_event()->is_end_date_enabled())]
			));

			$publishing_end_date->add_form_constraint(new FormConstraintFieldsDifferenceSuperior($publishing_start_date, $publishing_end_date));
		}

		$fieldset->add_field(new FormFieldHidden('referrer', $request->get_url_referrer()));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function save()
	{
		$event = $this->get_event();

		$event->set_division_id($this->form->get_value('division')->get_raw_value());
		$event->set_season_id($this->form->get_value('season')->get_raw_value());
		$event->set_scoring_type($this->form->get_value('scoring_type')->get_raw_value());
		$event->set_is_sub($this->form->get_value('is_sub'));
        if($this->form->get_value('is_sub'))
        {
            $event->set_master_id($this->form->get_value('master_id')->get_raw_value());
            $event->set_sub_order($this->form->get_value('sub_order'));
        }

		$division_title = $this->form->get_value('division')->get_label();
		$season_title = $this->form->get_value('season')->get_label();
		$event->set_event_slug(Url::encode_rewrite($division_title . '-' . $season_title));

		$event->set_start_date($this->form->get_value('start_date'));
		$event->set_end_date($this->form->get_value('end_date'));
		$event->set_sources($this->form->get_value('sources'));

		if (CategoriesService::get_categories_manager()->get_categories_cache()->has_categories())
			$event->set_id_category($this->form->get_value('id_category')->get_raw_value());

		$event->set_sources($this->form->get_value('sources'));

		if (!ScmAuthorizationsService::check_authorizations($event->get_id_category())->moderation())
		{
			$event->clean_publishing_start_and_end_date();

			if (ScmAuthorizationsService::check_authorizations($event->get_id_category())->contribution() && !ScmAuthorizationsService::check_authorizations($event->get_id_category())->write())
				$event->set_publishing_state(ScmEvent::NOT_PUBLISHED);
		}
		else
		{
			if ($this->form->get_value('update_creation_date'))
				$event->set_creation_date(new Date());
			else
				$event->set_creation_date($this->form->get_value('creation_date'));

			$event->set_publishing_state($this->form->get_value('published')->get_raw_value());
			if ($event->get_publishing_state() == ScmEvent::DEFERRED_PUBLICATION)
			{
				$deferred_operations = $this->config->get_deferred_operations();

				$old_publishing_start_date = $event->get_publishing_start_date();
				$publishing_start_date = $this->form->get_value('publishing_start_date');
				$event->set_publishing_start_date($publishing_start_date);

				if ($old_publishing_start_date !== null && $old_publishing_start_date->get_timestamp() != $publishing_start_date->get_timestamp() && in_array($old_publishing_start_date->get_timestamp(), $deferred_operations))
				{
					$key = array_search($old_publishing_start_date->get_timestamp(), $deferred_operations);
					unset($deferred_operations[$key]);
				}

				if (!in_array($publishing_start_date->get_timestamp(), $deferred_operations))
					$deferred_operations[] = $publishing_start_date->get_timestamp();

				if ($this->form->get_value('end_date_enabled'))
				{
					$old_publishing_end_date = $event->get_publishing_end_date();
					$publishing_end_date = $this->form->get_value('publishing_end_date');
					$event->set_publishing_end_date($publishing_end_date);

					if ($old_publishing_end_date !== null && $old_publishing_end_date->get_timestamp() != $publishing_end_date->get_timestamp() && in_array($old_publishing_end_date->get_timestamp(), $deferred_operations))
					{
						$key = array_search($old_publishing_end_date->get_timestamp(), $deferred_operations);
						unset($deferred_operations[$key]);
					}

					if (!in_array($publishing_end_date->get_timestamp(), $deferred_operations))
						$deferred_operations[] = $publishing_end_date->get_timestamp();
				}
				else
					$event->clean_publishing_end_date();

				$this->config->set_deferred_operations($deferred_operations);
				ScmConfig::save();
			}
			else
				$event->clean_publishing_start_and_end_date();
		}

		if ($this->is_new_event)
		{
			$id = ScmEventService::add($event);
			$event->set_id($id);

			HooksService::execute_hook_action('event_add', 'scm', array_merge($event->get_properties(), [
                'title' => $event->get_event_name(),
                'url' => $event->get_event_url()
            ]));
		}
		else
		{
			$event->set_update_date(new Date());
			ScmEventService::update($event);

			HooksService::execute_hook_action('event_edit', 'scm', array_merge($event->get_properties(), [
                'title' => $event->get_event_name(),
                'url' => $event->get_event_url()
            ]));
		}

		ScmEventService::clear_cache();
	}

    private function get_events_list() : array
    {
        $options = [];
		$cache = ScmEventCache::load();
		$events_list = $cache->get_events();
        $options[] = new FormFieldSelectChoiceOption($this->lang['common.none.alt'], 0);

        usort($events_list, function($a, $b) {
            return strcmp($b['id_category'], $a['id_category']);
        });
		$i = 1;
		foreach($events_list as $event)
		{
            $category = CategoriesService::get_categories_manager()->get_categories_cache()->get_category($event['id_category'])->get_name();
            $season = ScmSeasonService::get_season($event['season_id'])->get_season_name();
            $division = ScmDivisionService::get_division($event['division_id'])->get_division_name();

            if (!$event['is_sub'])
                $options[] = new FormFieldSelectChoiceOption($category . ' - ' . $division . ' - ' . $season, $event['id']);
			$i++;
		}

		return $options;
    }

	private function seasons_list()
	{
		$options = [];
		$cache = ScmSeasonCache::load();
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
		$options = [];
		$cache = ScmDivisionCache::load();
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

	private function get_event()
	{
		if ($this->event === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try {
					$this->event = ScmEventService::get_event($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_event = true;
				$this->event = new ScmEvent();
				$this->event->init_default_properties(AppContext::get_request()->get_getint('id_category', Category::ROOT_CATEGORY));
			}
		}
		return $this->event;
	}

    private function event_id()
    {
        return $this->get_event()->get_id();
    }

	private function check_authorizations()
	{
		$event = $this->get_event();

		if ($event->get_id() === null)
		{
			if (!$event->is_authorized_to_add())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!$event->is_authorized_to_edit())
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

	private function redirect()
	{
		$event = $this->get_event();

		if ($event->is_published())
		{
			if ($this->is_new_event)
				AppContext::get_response()->redirect(ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()), StringVars::replace_vars($this->lang['scm.message.success.add'], ['title' => $event->get_event_name()]));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug())), StringVars::replace_vars($this->lang['scm.message.success.edit'], ['title' => $event->get_event_name()]));
		}
		else
		{
			// if ($this->is_new_event)
			// 	AppContext::get_response()->redirect(ScmUrlBuilder::display_pending(), StringVars::replace_vars($this->lang['scm.message.success.add'], ['title' => $event->get_event_name()]));
			// else
			// 	AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : ScmUrlBuilder::display_pending()), StringVars::replace_vars($this->lang['scm.message.success.edit'], ['title' => $event->get_event_name()]));
		}
	}

	private function generate_response(View $view)
	{
		$event = $this->get_event();

		$location_id = $event->get_id() ? 'scm-event-'. $event->get_id() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());

		if ($event->get_id() === null)
		{
			$breadcrumb->add($this->lang['scm.add.event'], ScmUrlBuilder::add($event->get_id_category()));
			$graphical_environment->set_page_title($this->lang['scm.add.event'], $this->lang['scm.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.add.event']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::add($event->get_id_category()));
		}
		else
		{
			if (!AppContext::get_session()->location_id_already_exists($location_id))
				$graphical_environment->set_location_id($location_id);

			$graphical_environment->set_page_title($this->lang['scm.edit.event'], $this->lang['scm.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.edit.event']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit($event->get_id(), $event->get_event_slug()));

			$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($event->get_id_category(), true));
			foreach ($categories as $id => $category)
			{
				if ($category->get_id() != Category::ROOT_CATEGORY)
					$breadcrumb->add($category->get_name(), ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
			}
			$category = $event->get_category();
			$breadcrumb->add($event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));
			$breadcrumb->add($this->lang['scm.edit.event'], ScmUrlBuilder::edit($event->get_id(), $event->get_event_slug()));
		}

		return $response;
	}
}
?>
