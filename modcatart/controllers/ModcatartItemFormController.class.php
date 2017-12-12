<?php
/*##################################################
 *                       ModcatartItemFormController.class.php
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

class ModcatartItemFormController extends ModuleController
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

	private $itemcatart;
	private $is_new_itemcatart;

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		$this->check_authorizations();
		$this->build_form($request);

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$this->redirect();
		}

		$this->tpl->put_all(array(
			'FORM' => $this->form->display(),
			'C_TINYMCE_EDITOR' => AppContext::get_current_user()->get_editor() == 'TinyMCE'
		));

		return $this->build_response($this->tpl);
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'modcatart');
		$this->tpl = new FileTemplate('modcatart/ModcatartItemFormController.tpl');
		$this->tpl->add_lang($this->lang);
		$this->common_lang = LangLoader::get('common');
	}

	private function build_form(HTTPRequestCustom $request)
	{
		$form = new HTMLForm(__CLASS__);

		$fieldset = new FormFieldsetHTML('modcatart', $this->lang['modcatart.module.title']);
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldTextEditor('title', $this->common_lang['form.title'], $this->get_itemcatart()->get_title(),
			array('required' => true)
		));

		if (ModcatartAuthorizationsService::check_authorizations($this->get_itemcatart()->get_category_id())->moderation())
		{
			$fieldset->add_field(new FormFieldCheckbox('personalize_rewrited_title', $this->common_lang['form.rewrited_name.personalize'], $this->get_itemcatart()->rewrited_title_is_personalized(),
				array('events' => array('click' =>'
					if (HTMLForms.getField("personalize_rewrited_title").getValue()) {
						HTMLForms.getField("rewrited_title").enable();
					} else {
						HTMLForms.getField("rewrited_title").disable();
					}'
				))
			));

			$fieldset->add_field(new FormFieldTextEditor('rewrited_title', $this->common_lang['form.rewrited_name'], $this->get_itemcatart()->get_rewrited_title(),
				array('description' => $this->common_lang['form.rewrited_name.description'],
				      'hidden' => !$this->get_itemcatart()->rewrited_title_is_personalized()),
				array(new FormFieldConstraintRegex('`^[a-z0-9\-]+$`iu'))
			));
		}

		if (ModcatartService::get_categories_manager()->get_categories_cache()->has_categories())
		{
			$search_category_children_options = new SearchCategoryChildrensOptions();
			$search_category_children_options->add_authorizations_bits(Category::CONTRIBUTION_AUTHORIZATIONS);
			$search_category_children_options->add_authorizations_bits(Category::WRITE_AUTHORIZATIONS);
			$fieldset->add_field(ModcatartService::get_categories_manager()->get_select_categories_form_field('category_id', $this->common_lang['form.category'], $this->get_itemcatart()->get_category_id(), $search_category_children_options));
		}

		$fieldset->add_field(new FormFieldCheckbox('enable_description', $this->lang['modcatart.form.enabled.description'], $this->get_itemcatart()->get_description_enabled(),
			array('description' => StringVars::replace_vars($this->lang['modcatart.form.enabled.description.description'],
			array('number' => ModcatartConfig::load()->get_characters_number_to_cut())),
				'events' => array('click' => '
					if (HTMLForms.getField("enable_description").getValue()) {
						HTMLForms.getField("description").enable();
					} else {
						HTMLForms.getField("description").disable();
					}'
		))));

		$fieldset->add_field(new FormFieldRichTextEditor('description', StringVars::replace_vars($this->lang['modcatart.form.description'],
			array('number' =>ModcatartConfig::load()->get_characters_number_to_cut())), $this->get_itemcatart()->get_description(),
			array('rows' => 3, 'hidden' => !$this->get_itemcatart()->get_description_enabled())
		));

		$fieldset->add_field(new FormFieldRichTextEditor('contents', $this->common_lang['form.contents'], $this->get_itemcatart()->get_contents(),
			array('rows' => 15, 'required' => true)
		));

		$fieldset->add_field(new FormFieldActionLink('add_page', $this->lang['modcatart.form.add.page'] , 'javascript:bbcode_page();', 'fa-pagebreak'));

		if ($this->get_itemcatart()->get_displayed_author_name() == true)
		{
			$fieldset->add_field(new FormFieldCheckbox('enabled_author_name_customization', $this->lang['modcatart.form.enabled.author.name.customisation'], $this->get_itemcatart()->is_enabled_author_name_customization(),
				array('events' => array('click' => '
				if (HTMLForms.getField("enabled_author_name_customization").getValue()) {
					HTMLForms.getField("custom_author_name").enable();
				} else {
					HTMLForms.getField("custom_author_name").disable();
				}'))
			));

			$fieldset->add_field(new FormFieldTextEditor('custom_author_name', $this->lang['modcatart.form.custom.author.name'], $this->get_itemcatart()->get_custom_author_name(), array(
				'hidden' => !$this->get_itemcatart()->is_enabled_author_name_customization(),
			)));
		}

		$other_fieldset = new FormFieldsetHTML('other', $this->common_lang['form.other']);
		$form->add_fieldset($other_fieldset);

		$other_fieldset->add_field(new FormFieldCheckbox('displayed_author_name', LangLoader::get_message('config.author_displayed', 'admin-common'), $this->get_itemcatart()->get_displayed_author_name()));

		$other_fieldset->add_field(new FormFieldUploadPictureFile('thumbnail', $this->common_lang['form.picture'], $this->get_itemcatart()->get_thumbnail()->relative()));

		$other_fieldset->add_field(ModcatartService::get_keywords_manager()->get_form_field($this->get_itemcatart()->get_id(), 'keywords', $this->common_lang['form.keywords'],
			array('description' => $this->common_lang['form.keywords.description'])
		));

		$other_fieldset->add_field(new ModcatartFormFieldSelectSources('sources', $this->common_lang['form.sources'], $this->get_itemcatart()->get_sources()));

		$other_fieldset->add_field(new ModcatartFormFieldCarousel('carousel', $this->lang['modcatart.form.carousel'], $this->get_itemcatart()->get_carousel()));

		if (ModcatartAuthorizationsService::check_authorizations($this->get_itemcatart()->get_category_id())->moderation())
		{
			$publication_fieldset = new FormFieldsetHTML('publication', $this->common_lang['form.approbation']);
			$form->add_fieldset($publication_fieldset);

			$publication_fieldset->add_field(new FormFieldDateTime('creation_date', $this->common_lang['form.date.creation'], $this->get_itemcatart()->get_creation_date(),
				array('required' => true)
			));

			if (!$this->get_itemcatart()->is_published())
			{
				$publication_fieldset->add_field(new FormFieldCheckbox('update_creation_date', $this->common_lang['form.update.date.creation'], false, array('hidden' => $this->get_itemcatart()->get_status() != Itemcatart::NOT_PUBLISHED)
				));
			}

			$publication_fieldset->add_field(new FormFieldSimpleSelectChoice('publication_state', $this->common_lang['form.approbation'], $this->get_itemcatart()->get_publication_state(),
				array(
					new FormFieldSelectChoiceOption($this->common_lang['form.approbation.not'], Itemcatart::NOT_PUBLISHED),
					new FormFieldSelectChoiceOption($this->common_lang['form.approbation.now'], Itemcatart::PUBLISHED_NOW),
					new FormFieldSelectChoiceOption($this->common_lang['status.approved.date'], Itemcatart::PUBLICATION_DATE),
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
				($this->get_itemcatart()->get_publication_start_date() === null ? new Date() : $this->get_itemcatart()->get_publication_start_date()),
				array('hidden' => ($this->get_itemcatart()->get_publication_state() != Itemcatart::PUBLICATION_DATE))
			));

			$publication_fieldset->add_field(new FormFieldCheckbox('end_date_enable', $this->common_lang['form.date.end.enable'], $this->get_itemcatart()->enabled_end_date(),
				array('hidden' => ($this->get_itemcatart()->get_publication_state() != Itemcatart::PUBLICATION_DATE),
					'events' => array('click' => '
						if (HTMLForms.getField("end_date_enable").getValue()) {
							HTMLForms.getField("publication_end_date").enable();
						} else {
							HTMLForms.getField("publication_end_date").disable();
						}'
				))
			));

			$publication_fieldset->add_field(new FormFieldDateTime('publication_end_date', $this->common_lang['form.date.end'],
				($this->get_itemcatart()->get_publication_end_date() === null ? new date() : $this->get_itemcatart()->get_publication_end_date()),
				array('hidden' => !$this->get_itemcatart()->enabled_end_date())
			));
		}

		$this->build_contribution_fieldset($form);

		$fieldset->add_field(new FormFieldHidden('referrer', $request->get_url_referrer()));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;

		// Positionnement à la bonne page quand on édite un item avec plusieurs pages
		if ($this->get_itemcatart()->get_id() !== null)
		{
			$current_page = $request->get_getstring('page', '');

			$this->tpl->put('C_PAGE', !empty($current_page));

			if (!empty($current_page))
			{
				$itemcatart_contents = $this->itemcatart->get_contents();

				//If item doesn't begin with a page, we insert one
				if (TextHelper::substr(trim($itemcatart_contents), 0, 6) != '[page]')
				{
					$itemcatart_contents = '[page]&nbsp;[/page]' . $itemcatart_contents;
				}

				//Removing [page] bbcode
				$itemcatart_contents_clean = preg_split('`\[page\].+\[/page\](.*)`usU', $itemcatart_contents, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

				//Retrieving pages
				preg_match_all('`\[page\]([^[]+)\[/page\]`uU', $itemcatart_contents, $array_page);

				$page_name = (isset($array_page[1][$current_page-1]) && $array_page[1][$current_page-1] != '&nbsp;') ? $array_page[1][($current_page-1)] : '';

				$this->tpl->put('PAGE', TextHelper::to_js_string($page_name));
			}
		}
	}

	private function build_contribution_fieldset($form)
	{
		if ($this->get_itemcatart()->get_id() === null && $this->is_contributor_member())
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
		return (!ModcatartAuthorizationsService::check_authorizations()->write() && ModcatartAuthorizationsService::check_authorizations()->contribution());
	}

	private function get_itemcatart()
	{
		if ($this->itemcatart === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try
				{
					$this->itemcatart = ModcatartService::get_itemcatart('WHERE modcatart.id=:id', array('id' => $id));
				}
				catch(RowNotFoundException $e)
				{
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_itemcatart = true;
				$this->itemcatart = new Itemcatart();
				$this->itemcatart->init_default_properties(AppContext::get_request()->get_getint('category_id', Category::ROOT_CATEGORY));
			}
		}
		return $this->itemcatart;
	}

	private function check_authorizations()
	{
		$itemcatart = $this->get_itemcatart();

		if ($itemcatart->get_id() === null)
		{
			if (!$itemcatart->is_authorized_to_add())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!$itemcatart->is_authorized_to_edit())
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
		$itemcatart = $this->get_itemcatart();

		$itemcatart->set_title($this->form->get_value('title'));

		if (ModcatartService::get_categories_manager()->get_categories_cache()->has_categories())
			$itemcatart->set_category_id($this->form->get_value('category_id')->get_raw_value());

		$itemcatart->set_description(($this->form->get_value('enable_description') ? $this->form->get_value('description') : ''));
		$itemcatart->set_contents($this->form->get_value('contents'));

		$displayed_author_name = $this->form->get_value('displayed_author_name') ? $this->form->get_value('displayed_author_name') : Itemcatart::NOTDISPLAYED_AUTHOR_NAME;
		$itemcatart->set_displayed_author_name($displayed_author_name);
		$itemcatart->set_thumbnail(new Url($this->form->get_value('thumbnail')));

		if ($this->get_itemcatart()->get_displayed_author_name() == true)
			$itemcatart->set_custom_author_name(($this->form->get_value('custom_author_name') && $this->form->get_value('custom_author_name') !== $itemcatart->get_author_user()->get_display_name() ? $this->form->get_value('custom_author_name') : ''));

		$itemcatart->set_sources($this->form->get_value('sources'));
		$itemcatart->set_carousel($this->form->get_value('carousel'));

		if (!ModcatartAuthorizationsService::check_authorizations($itemcatart->get_category_id())->moderation())
		{
			if ($itemcatart->get_id() === null)
				$itemcatart->set_creation_date(new Date());

			$itemcatart->set_rewrited_title(Url::encode_rewrite($itemcatart->get_title()));
			$itemcatart->clean_publication_start_and_end_date();

			if (ModcatartAuthorizationsService::check_authorizations($itemcatart->get_category_id())->contribution() && !ModcatartAuthorizationsService::check_authorizations($itemcatart->get_category_id())->write())
				$itemcatart->set_publication_state(Itemcatart::NOT_PUBLISHED);
		}
		else
		{
			if ($this->form->get_value('update_creation_date'))
			{
				$itemcatart->set_creation_date(new Date());
			}
			else
			{
				$itemcatart->set_creation_date($this->form->get_value('creation_date'));
			}

			$rewrited_title = $this->form->get_value('rewrited_title', '');
			$rewrited_title = $this->form->get_value('personalize_rewrited_title') && !empty($rewrited_title) ? $rewrited_title : Url::encode_rewrite($itemcatart->get_title());
			$itemcatart->set_rewrited_title($rewrited_title);

			$itemcatart->set_publication_state($this->form->get_value('publication_state')->get_raw_value());
			if ($itemcatart->get_publication_state() == Itemcatart::PUBLICATION_DATE)
			{
				$config = ModcatartConfig::load();
				$deferred_operations = $config->get_deferred_operations();

				$old_start_date = $itemcatart->get_publication_start_date();
				$start_date = $this->form->get_value('publication_start_date');
				$itemcatart->set_publication_start_date($start_date);

				if ($old_start_date !== null && $old_start_date->get_timestamp() != $start_date->get_timestamp() && in_array($old_start_date->get_timestamp(), $deferred_operations))
				{
					$key = array_search($old_start_date->get_timestamp(), $deferred_operations);
					unset($deferred_operations[$key]);
				}

				if (!in_array($start_date->get_timestamp(), $deferred_operations))
					$deferred_operations[] = $start_date->get_timestamp();

				if ($this->form->get_value('end_date_enable'))
				{
					$old_end_date = $itemcatart->get_publication_end_date();
					$end_date = $this->form->get_value('publication_end_date');
					$itemcatart->set_publication_end_date($end_date);

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
					$itemcatart->clean_publication_end_date();
				}

				$config->set_deferred_operations($deferred_operations);
				ModcatartConfig::save();
			}
			else
			{
				$itemcatart->clean_publication_start_and_end_date();
			}
		}

		if ($itemcatart->get_id() === null)
		{
			$itemcatart->set_author_user(AppContext::get_current_user());
			$id_itemcatart = ModcatartService::add($itemcatart);
		}
		else
		{
			$now = new Date();
			$itemcatart->set_updated_date($now);
			$id_itemcatart = $itemcatart->get_id();
			ModcatartService::update($itemcatart);
		}

		$this->contribution_actions($itemcatart, $id_itemcatart);

		ModcatartService::get_keywords_manager()->put_relations($id_itemcatart, $this->form->get_value('keywords'));

		Feed::clear_cache('modcatart');
		ModcatartCategoriesCache::invalidate();
	}

	private function contribution_actions(Itemcatart $itemcatart, $id_itemcatart)
	{
		if ($itemcatart->get_id() === null)
		{
			if ($this->is_contributor_member())
			{
				$contribution = new Contribution();
				$contribution->set_id_in_module($id_itemcatart);
				$contribution->set_description(stripslashes($this->form->get_value('contribution_description')));
				$contribution->set_entitled($itemcatart->get_title());
				$contribution->set_fixing_url(ModcatartUrlBuilder::edit_item($id_itemcatart)->relative());
				$contribution->set_poster_id(AppContext::get_current_user()->get_id());
				$contribution->set_module('modcatart');
				$contribution->set_auth(
					Authorizations::capture_and_shift_bit_auth(
						ModcatartService::get_categories_manager()->get_heritated_authorizations($itemcatart->get_category_id(), Category::MODERATION_AUTHORIZATIONS, Authorizations::AUTH_CHILD_PRIORITY),
						Category::MODERATION_AUTHORIZATIONS, Contribution::CONTRIBUTION_AUTH_BIT
					)
				);
				ContributionService::save_contribution($contribution);
			}
		}
		else
		{
			$corresponding_contributions = ContributionService::find_by_criteria('modcatart', $id_itemcatart);
			if (count($corresponding_contributions) > 0)
			{
				foreach ($corresponding_contributions as $contribution)
				{
					$contribution->set_status(Event::EVENT_STATUS_PROCESSED);
					ContributionService::save_contribution($contribution);
				}
			}
		}
		$itemcatart->set_id($id_itemcatart);
	}

	private function redirect()
	{
		$itemcatart = $this->get_itemcatart();
		$category = $itemcatart->get_category();

		if ($this->is_new_itemcatart && $this->is_contributor_member() && !$itemcatart->is_published())
		{
			DispatchManager::redirect(new UserContributionSuccessController());
		}
		elseif ($itemcatart->is_published())
		{
			if ($this->is_new_itemcatart)
				AppContext::get_response()->redirect(ModcatartUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $itemcatart->get_id(), $itemcatart->get_rewrited_title(), AppContext::get_request()->get_getint('page', 1)), StringVars::replace_vars($this->lang['modcatart.message.success.add'], array('title' => $itemcatart->get_title())));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : ModcatartUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $itemcatart->get_id(), $itemcatart->get_rewrited_title(), AppContext::get_request()->get_getint('page', 1))), StringVars::replace_vars($this->lang['modcatart.message.success.edit'], array('title' => $itemcatart->get_title())));
		}
		else
		{
			if ($this->is_new_itemcatart)
				AppContext::get_response()->redirect(ModcatartUrlBuilder::display_pending_items(), StringVars::replace_vars($this->lang['modcatart.message.success.add'], array('title' => $itemcatart->get_title())));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : ModcatartUrlBuilder::display_pending_items()), StringVars::replace_vars($this->lang['modcatart.message.success.edit'], array('title' => $itemcatart->get_title())));
		}
	}

	private function build_response(View $tpl)
	{
		$itemcatart = $this->get_itemcatart();

		$response = new SiteDisplayResponse($tpl);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['modcatart.module.title'], ModcatartUrlBuilder::home());

		if ($itemcatart->get_id() === null)
		{
			$breadcrumb->add($this->lang['modcatart.add'], ModcatartUrlBuilder::add_item($itemcatart->get_category_id()));
			$graphical_environment->set_page_title($this->lang['modcatart.add'], $this->lang['modcatart.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['modcatart.add']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ModcatartUrlBuilder::add_item($itemcatart->get_category_id()));
		}
		else
		{
			$categories = array_reverse(ModcatartService::get_categories_manager()->get_parents($itemcatart->get_category_id(), true));
			foreach ($categories as $id => $category)
			{
				if ($category->get_id() != Category::ROOT_CATEGORY)
					$breadcrumb->add($category->get_name(), ModcatartUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
			}
			$breadcrumb->add($itemcatart->get_title(), ModcatartUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $itemcatart->get_id(), $itemcatart->get_rewrited_title()));

			$breadcrumb->add($this->lang['modcatart.edit'], ModcatartUrlBuilder::edit_item($itemcatart->get_id()));
			$graphical_environment->set_page_title($this->lang['modcatart.edit'], $this->lang['modcatart.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['modcatart.edit']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ModcatartUrlBuilder::edit_item($itemcatart->get_id()));
		}

		return $response;
	}
}
?>
