<?php
/*##################################################
 *                       ModlistItemFormController.class.php
 *                            -------------------
 *   begin                : Month XX, 2017
 *   copyright            : (C) 2017 Firstname LASTNAME
 *   email                : nickname@phpboost.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

/**
 * @author Firstname LASTNAME <nickname@phpboost.com>
 */

class ModlistItemFormController extends ModuleController
{
	/**
	 * @var HTMLForm
	 */
	private $form;
	/**
	 * @var FormButtonSubmit
	 */
	private $submit_button;

	private $tpl;

	private $lang;
	private $common_lang;

	private $itemlist;
	private $is_new_itemlist;

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		$this->check_authorizations();
		$this->build_form($request);

		$tpl = new StringTemplate('# INCLUDE FORM #');
		$tpl->add_lang($this->lang);

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$this->redirect();
		}

		$tpl->put('FORM', $this->form->display());

		return $this->generate_response($tpl);
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'modlist');
		$this->common_lang = LangLoader::get('common');
	}

	private function build_form(HTTPRequestCustom $request)
	{
		$form = new HTMLForm(__CLASS__);

		$fieldset = new FormFieldsetHTML('modlist', $this->lang['modlist.module.title']);
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldTextEditor('title', $this->common_lang['form.title'], $this->get_itemlist()->get_title(),
			array('required' => true)
		));

		if (ModlistAuthorizationsService::check_authorizations($this->get_itemlist()->get_category_id())->moderation())
		{
			$fieldset->add_field(new FormFieldCheckbox('personalize_rewrited_title', $this->common_lang['form.rewrited_name.personalize'], $this->get_itemlist()->rewrited_title_is_personalized(),
				array('events' => array('click' =>'
					if (HTMLForms.getField("personalize_rewrited_title").getValue()) {
						HTMLForms.getField("rewrited_title").enable();
					} else {
						HTMLForms.getField("rewrited_title").disable();
					}'
				))
			));

			$fieldset->add_field(new FormFieldTextEditor('rewrited_title', $this->common_lang['form.rewrited_name'], $this->get_itemlist()->get_rewrited_title(),
				array('description' => $this->common_lang['form.rewrited_name.description'],
				      'hidden' => !$this->get_itemlist()->rewrited_title_is_personalized()),
				array(new FormFieldConstraintRegex('`^[a-z0-9\-]+$`iu'))
			));
		}

		if (ModlistService::get_categories_manager()->get_categories_cache()->has_categories())
		{
			$search_category_children_options = new SearchCategoryChildrensOptions();
			$search_category_children_options->add_authorizations_bits(Category::CONTRIBUTION_AUTHORIZATIONS);
			$search_category_children_options->add_authorizations_bits(Category::WRITE_AUTHORIZATIONS);
			$fieldset->add_field(ModlistService::get_categories_manager()->get_select_categories_form_field('category_id', $this->common_lang['form.category'], $this->get_itemlist()->get_category_id(), $search_category_children_options));
		}

		$fieldset->add_field(new FormFieldCheckbox('enable_description', $this->lang['modlist.form.enabled.description'], $this->get_itemlist()->get_description_enabled(),
			array('description' => StringVars::replace_vars($this->lang['modlist.form.enabled.description.description'],
			array('number' => ModlistConfig::load()->get_characters_number_to_cut())),
				'events' => array('click' => '
					if (HTMLForms.getField("enable_description").getValue()) {
						HTMLForms.getField("description").enable();
					} else {
						HTMLForms.getField("description").disable();
					}'
		))));

		$fieldset->add_field(new FormFieldRichTextEditor('description', StringVars::replace_vars($this->lang['modlist.form.description'],
			array('number' =>ModlistConfig::load()->get_characters_number_to_cut())), $this->get_itemlist()->get_description(),
			array('rows' => 3, 'hidden' => !$this->get_itemlist()->get_description_enabled())
		));

		$fieldset->add_field(new FormFieldRichTextEditor('contents', $this->common_lang['form.contents'], $this->get_itemlist()->get_contents(),
			array('rows' => 15, 'required' => true)
		));

		if ($this->get_itemlist()->get_displayed_author_name() == true)
		{
			$fieldset->add_field(new FormFieldCheckbox('enabled_author_name_customization', $this->lang['modlist.form.enabled.author.name.customisation'], $this->get_itemlist()->is_enabled_author_name_customization(),
				array('events' => array('click' => '
				if (HTMLForms.getField("enabled_author_name_customization").getValue()) {
					HTMLForms.getField("custom_author_name").enable();
				} else {
					HTMLForms.getField("custom_author_name").disable();
				}'))
			));

			$fieldset->add_field(new FormFieldTextEditor('custom_author_name', $this->lang['modlist.form.custom.author.name'], $this->get_itemlist()->get_custom_author_name(), array(
				'hidden' => !$this->get_itemlist()->is_enabled_author_name_customization(),
			)));
		}

		$other_fieldset = new FormFieldsetHTML('other', $this->common_lang['form.other']);
		$form->add_fieldset($other_fieldset);

		$other_fieldset->add_field(new FormFieldCheckbox('displayed_author_name', LangLoader::get_message('config.author_displayed', 'admin-common'), $this->get_itemlist()->get_displayed_author_name()));

		$other_fieldset->add_field(new FormFieldUploadPictureFile('thumbnail', $this->common_lang['form.picture'], $this->get_itemlist()->get_thumbnail()->relative()));

		$other_fieldset->add_field(ModlistService::get_keywords_manager()->get_form_field($this->get_itemlist()->get_id(), 'keywords', $this->common_lang['form.keywords'],
			array('description' => $this->common_lang['form.keywords.description'])
		));

		$other_fieldset->add_field(new ModlistFormFieldSelectSources('sources', $this->common_lang['form.sources'], $this->get_itemlist()->get_sources()));

		$other_fieldset->add_field(new ModlistFormFieldCarousel('carousel', $this->lang['modlist.form.carousel'], $this->get_itemlist()->get_carousel()));

		if (ModlistAuthorizationsService::check_authorizations($this->get_itemlist()->get_category_id())->moderation())
		{
			$publication_fieldset = new FormFieldsetHTML('publication', $this->common_lang['form.approbation']);
			$form->add_fieldset($publication_fieldset);

			$publication_fieldset->add_field(new FormFieldDateTime('creation_date', $this->common_lang['form.date.creation'], $this->get_itemlist()->get_creation_date(),
				array('required' => true)
			));

			if (!$this->get_itemlist()->is_published())
			{
				$publication_fieldset->add_field(new FormFieldCheckbox('update_creation_date', $this->common_lang['form.update.date.creation'], false, array('hidden' => $this->get_itemlist()->get_status() != Itemlist::NOT_PUBLISHED)
				));
			}

			$publication_fieldset->add_field(new FormFieldSimpleSelectChoice('publication_state', $this->common_lang['form.approbation'], $this->get_itemlist()->get_publication_state(),
				array(
					new FormFieldSelectChoiceOption($this->common_lang['form.approbation.not'], Itemlist::NOT_PUBLISHED),
					new FormFieldSelectChoiceOption($this->common_lang['form.approbation.now'], Itemlist::PUBLISHED_NOW),
					new FormFieldSelectChoiceOption($this->common_lang['status.approved.date'], Itemlist::PUBLICATION_DATE),
				),
				array('events' => array('change' => '
				if (HTMLForms.getField("publication_state").getValue() == 2) {
					jQuery("#' . __CLASS__ . '_publication_start_date_field").show();
					HTMLForms.getField("end_date_enable").enable();
				} else {
					jQuery("#' . __CLASS__ . '_publication_start_date_field").hide();
					HTMLForms.getField("end_date_enable").disable();
				}'))
			));

			$publication_fieldset->add_field(new FormFieldDateTime('publication_start_date', $this->common_lang['form.date.start'],
				($this->get_itemlist()->get_publication_start_date() === null ? new Date() : $this->get_itemlist()->get_publication_start_date()),
				array('hidden' => ($this->get_itemlist()->get_publication_state() != Itemlist::PUBLICATION_DATE))
			));

			$publication_fieldset->add_field(new FormFieldCheckbox('end_date_enable', $this->common_lang['form.date.end.enable'], $this->get_itemlist()->enabled_end_date(),
				array('hidden' => ($this->get_itemlist()->get_publication_state() != Itemlist::PUBLICATION_DATE),
					'events' => array('click' => '
						if (HTMLForms.getField("end_date_enable").getValue()) {
							HTMLForms.getField("publication_end_date").enable();
						} else {
							HTMLForms.getField("publication_end_date").disable();
						}'
				))
			));

			$publication_fieldset->add_field(new FormFieldDateTime('publication_end_date', $this->common_lang['form.date.end'],
				($this->get_itemlist()->get_publication_end_date() === null ? new date() : $this->get_itemlist()->get_publication_end_date()),
				array('hidden' => !$this->get_itemlist()->enabled_end_date())
			));
		}

		$this->build_contribution_fieldset($form);

		$fieldset->add_field(new FormFieldHidden('referrer', $request->get_url_referrer()));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function build_contribution_fieldset($form)
	{
		if ($this->get_itemlist()->get_id() === null && $this->is_contributor_member())
		{
			$fieldset = new FormFieldsetHTML('contribution', LangLoader::get_message('contribution', 'user-common'));
			$fieldset->set_description(MessageHelper::display(LangLoader::get_message('contribution.explain', 'user-common'), MessageHelper::WARNING)->render());
			$form->add_fieldset($fieldset);

			$fieldset->add_field(new FormFieldRichTextEditor('contribution_description', LangLoader::get_message('contribution.description', 'user-common'), '',
				array('description' => LangLoader::get_message('contribution.description.explain', 'user-common'))));
		}
	}

	private function is_contributor_member()
	{
		return (!ModlistAuthorizationsService::check_authorizations()->write() && ModlistAuthorizationsService::check_authorizations()->contribution());
	}

	private function get_itemlist()
	{
		if ($this->itemlist === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try
				{
					$this->itemlist = ModlistService::get_itemlist('WHERE modlist.id=:id', array('id' => $id));
				}
				catch(RowNotFoundException $e)
				{
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_itemlist = true;
				$this->itemlist = new Itemlist();
				$this->itemlist->init_default_properties(AppContext::get_request()->get_getint('category_id', Category::ROOT_CATEGORY));
			}
		}
		return $this->itemlist;
	}

	private function check_authorizations()
	{
		$itemlist = $this->get_itemlist();

		if ($itemlist->get_id() === null)
		{
			if (!$itemlist->is_authorized_to_add())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!$itemlist->is_authorized_to_edit())
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
		$itemlist = $this->get_itemlist();

		$itemlist->set_title($this->form->get_value('title'));

		if (ModlistService::get_categories_manager()->get_categories_cache()->has_categories())
			$itemlist->set_category_id($this->form->get_value('category_id')->get_raw_value());

		$itemlist->set_description(($this->form->get_value('enable_description') ? $this->form->get_value('description') : ''));
		$itemlist->set_contents($this->form->get_value('contents'));

		$displayed_author_name = $this->form->get_value('displayed_author_name') ? $this->form->get_value('displayed_author_name') : Itemlist::NOTDISPLAYED_AUTHOR_NAME;
		$itemlist->set_displayed_author_name($displayed_author_name);
		$itemlist->set_thumbnail(new Url($this->form->get_value('thumbnail')));

		if ($this->get_itemlist()->get_displayed_author_name() == true)
			$itemlist->set_custom_author_name(($this->form->get_value('custom_author_name') && $this->form->get_value('custom_author_name') !== $itemlist->get_author_user()->get_display_name() ? $this->form->get_value('custom_author_name') : ''));

		$itemlist->set_sources($this->form->get_value('sources'));
		$itemlist->set_carousel($this->form->get_value('carousel'));

		if (!ModlistAuthorizationsService::check_authorizations($itemlist->get_category_id())->moderation())
		{
			if ($itemlist->get_id() === null)
				$itemlist->set_creation_date(new Date());

			$itemlist->set_rewrited_title(Url::encode_rewrite($itemlist->get_title()));
			$itemlist->clean_publication_start_and_end_date();

			if (ModlistAuthorizationsService::check_authorizations($itemlist->get_category_id())->contribution() && !ModlistAuthorizationsService::check_authorizations($itemlist->get_category_id())->write())
				$itemlist->set_publication_state(Itemlist::NOT_PUBLISHED);
		}
		else
		{
			if ($this->form->get_value('update_creation_date'))
			{
				$itemlist->set_creation_date(new Date());
			}
			else
			{
				$itemlist->set_creation_date($this->form->get_value('creation_date'));
			}

			$rewrited_title = $this->form->get_value('rewrited_title', '');
			$rewrited_title = $this->form->get_value('personalize_rewrited_title') && !empty($rewrited_title) ? $rewrited_title : Url::encode_rewrite($itemlist->get_title());
			$itemlist->set_rewrited_title($rewrited_title);

			$itemlist->set_publication_state($this->form->get_value('publication_state')->get_raw_value());
			if ($itemlist->get_publication_state() == Itemlist::PUBLICATION_DATE)
			{
				$config = ModlistConfig::load();
				$deferred_operations = $config->get_deferred_operations();

				$old_start_date = $itemlist->get_publication_start_date();
				$start_date = $this->form->get_value('publication_start_date');
				$itemlist->set_publication_start_date($start_date);

				if ($old_start_date !== null && $old_start_date->get_timestamp() != $start_date->get_timestamp() && in_array($old_start_date->get_timestamp(), $deferred_operations))
				{
					$key = array_search($old_start_date->get_timestamp(), $deferred_operations);
					unset($deferred_operations[$key]);
				}

				if (!in_array($start_date->get_timestamp(), $deferred_operations))
					$deferred_operations[] = $start_date->get_timestamp();

				if ($this->form->get_value('end_date_enable'))
				{
					$old_end_date = $itemlist->get_publication_end_date();
					$end_date = $this->form->get_value('publication_end_date');
					$itemlist->set_publication_end_date($end_date);

					if ($old_end_date !== null && $old_end_date->get_timestamp() != $end_date->get_timestamp() && in_array($old_end_date->get_timestamp(), $deferred_operations))
					{
						$key = array_search($old_end_date->get_timestamp(), $deferred_operations);
						unset($deferred_operations[$key]);
					}

					if (!in_array($end_date->get_timestamp(), $deferred_operations))
						$deferred_operations[] = $end_date->get_timestamp();
				}
				else
				{
					$itemlist->clean_publication_end_date();
				}

				$config->set_deferred_operations($deferred_operations);
				ModlistConfig::save();
			}
			else
			{
				$itemlist->clean_publication_start_and_end_date();
			}
		}

		if ($itemlist->get_id() === null)
		{
			$itemlist->set_author_user(AppContext::get_current_user());
			$id_itemlist = ModlistService::add($itemlist);
		}
		else
		{
			$now = new Date();
			$itemlist->set_updated_date($now);
			$id_itemlist = $itemlist->get_id();
			ModlistService::update($itemlist);
		}

		$this->contribution_actions($itemlist, $id_itemlist);

		ModlistService::get_keywords_manager()->put_relations($id_itemlist, $this->form->get_value('keywords'));

		Feed::clear_cache('modlist');
		ModlistCategoriesCache::invalidate();
	}

	private function contribution_actions(Itemlist $itemlist, $id_itemlist)
	{
		if ($itemlist->get_id() === null)
		{
			if ($this->is_contributor_member())
			{
				$contribution = new Contribution();
				$contribution->set_id_in_module($id_itemlist);
				$contribution->set_description(stripslashes($this->form->get_value('contribution_description')));
				$contribution->set_entitled($itemlist->get_title());
				$contribution->set_fixing_url(ModlistUrlBuilder::edit_item($id_itemlist)->relative());
				$contribution->set_poster_id(AppContext::get_current_user()->get_id());
				$contribution->set_module('modlist');
				$contribution->set_auth(
					Authorizations::capture_and_shift_bit_auth(
						ModlistService::get_categories_manager()->get_heritated_authorizations($itemlist->get_category_id(), Category::MODERATION_AUTHORIZATIONS, Authorizations::AUTH_CHILD_PRIORITY),
						Category::MODERATION_AUTHORIZATIONS, Contribution::CONTRIBUTION_AUTH_BIT
					)
				);
				ContributionService::save_contribution($contribution);
			}
		}
		else
		{
			$corresponding_contributions = ContributionService::find_by_criteria('modlist', $id_itemlist);
			if (count($corresponding_contributions) > 0)
			{
				foreach ($corresponding_contributions as $contribution)
				{
					$contribution->set_status(Event::EVENT_STATUS_PROCESSED);
					ContributionService::save_contribution($contribution);
				}
			}
		}
		$itemlist->set_id($id_itemlist);
	}

	private function redirect()
	{
		$itemlist = $this->get_itemlist();
		$category = $itemlist->get_category();

		if ($this->is_new_itemlist && $this->is_contributor_member() && !$itemlist->is_published())
		{
			DispatchManager::redirect(new UserContributionSuccessController());
		}
		elseif ($itemlist->is_published())
		{
			if ($this->is_new_itemlist)
				AppContext::get_response()->redirect(ModlistUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $itemlist->get_id(), $itemlist->get_rewrited_title(), AppContext::get_request()->get_getint('page', 1)), StringVars::replace_vars($this->lang['modlist.message.success.add'], array('title' => $itemlist->get_title())));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : ModlistUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $itemlist->get_id(), $itemlist->get_rewrited_title(), AppContext::get_request()->get_getint('page', 1))), StringVars::replace_vars($this->lang['modlist.message.success.edit'], array('title' => $itemlist->get_title())));
		}
		else
		{
			if ($this->is_new_itemlist)
				AppContext::get_response()->redirect(ModlistUrlBuilder::display_pending_items(), StringVars::replace_vars($this->lang['modlist.message.success.add'], array('title' => $itemlist->get_title())));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : ModlistUrlBuilder::display_pending_items()), StringVars::replace_vars($this->lang['modlist.message.success.edit'], array('title' => $itemlist->get_title())));
		}
	}

	private function generate_response(View $tpl)
	{
		$itemlist = $this->get_itemlist();

		$response = new SiteDisplayResponse($tpl);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['modlist.module.title'], ModlistUrlBuilder::home());

		if ($itemlist->get_id() === null)
		{
			$breadcrumb->add($this->lang['modlist.add'], ModlistUrlBuilder::add_item($itemlist->get_category_id()));
			$graphical_environment->set_page_title($this->lang['modlist.add'], $this->lang['modlist.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['modlist.add']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ModlistUrlBuilder::add_item($itemlist->get_category_id()));
		}
		else
		{
			$categories = array_reverse(ModlistService::get_categories_manager()->get_parents($itemlist->get_category_id(), true));
			foreach ($categories as $id => $category)
			{
				if ($category->get_id() != Category::ROOT_CATEGORY)
					$breadcrumb->add($category->get_name(), ModlistUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
			}
			$breadcrumb->add($itemlist->get_title(), ModlistUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $itemlist->get_id(), $itemlist->get_rewrited_title()));

			$breadcrumb->add($this->lang['modlist.edit'], ModlistUrlBuilder::edit_item($itemlist->get_id()));
			$graphical_environment->set_page_title($this->lang['modlist.edit'], $this->lang['modlist.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['modlist.edit']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ModlistUrlBuilder::edit_item($itemlist->get_id()));
		}

		return $response;
	}
}
?>
