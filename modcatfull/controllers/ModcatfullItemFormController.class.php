<?php
/*##################################################
 *                       ModcatfullItemFormController.class.php
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

class ModcatfullItemFormController extends ModuleController
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

	private $itemcatfull;
	private $is_new_itemcatfull;

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
		$this->lang = LangLoader::get('common', 'modcatfull');
		$this->tpl = new FileTemplate('modcatfull/ModcatfullItemFormController.tpl');
		$this->tpl->add_lang($this->lang);
		$this->common_lang = LangLoader::get('common');
	}

	private function build_form(HTTPRequestCustom $request)
	{
		$form = new HTMLForm(__CLASS__);

		$fieldset = new FormFieldsetHTML('modcatfull', $this->lang['modcatfull.module.title']);
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldTextEditor('title', $this->common_lang['form.title'], $this->get_itemcatfull()->get_title(),
			array('required' => true)
		));

		if (ModcatfullAuthorizationsService::check_authorizations($this->get_itemcatfull()->get_category_id())->moderation())
		{
			$fieldset->add_field(new FormFieldCheckbox('personalize_rewrited_title', $this->common_lang['form.rewrited_name.personalize'], $this->get_itemcatfull()->rewrited_title_is_personalized(),
				array('events' => array('click' =>'
					if (HTMLForms.getField("personalize_rewrited_title").getValue()) {
						HTMLForms.getField("rewrited_title").enable();
					} else {
						HTMLForms.getField("rewrited_title").disable();
					}'
				))
			));

			$fieldset->add_field(new FormFieldTextEditor('rewrited_title', $this->common_lang['form.rewrited_name'], $this->get_itemcatfull()->get_rewrited_title(),
				array('description' => $this->common_lang['form.rewrited_name.description'],
				      'hidden' => !$this->get_itemcatfull()->rewrited_title_is_personalized()),
				array(new FormFieldConstraintRegex('`^[a-z0-9\-]+$`iu'))
			));
		}

		if (ModcatfullService::get_categories_manager()->get_categories_cache()->has_categories())
		{
			$search_category_children_options = new SearchCategoryChildrensOptions();
			$search_category_children_options->add_authorizations_bits(Category::CONTRIBUTION_AUTHORIZATIONS);
			$search_category_children_options->add_authorizations_bits(Category::WRITE_AUTHORIZATIONS);
			$fieldset->add_field(ModcatfullService::get_categories_manager()->get_select_categories_form_field('category_id', $this->common_lang['form.category'], $this->get_itemcatfull()->get_category_id(), $search_category_children_options));
		}

		$fieldset->add_field(new FormFieldCheckbox('enable_description', $this->lang['modcatfull.form.enabled.description'], $this->get_itemcatfull()->get_description_enabled(),
			array('description' => StringVars::replace_vars($this->lang['modcatfull.form.enabled.description.description'],
			array('number' => ModcatfullConfig::load()->get_characters_number_to_cut())),
				'events' => array('click' => '
					if (HTMLForms.getField("enable_description").getValue()) {
						HTMLForms.getField("description").enable();
					} else {
						HTMLForms.getField("description").disable();
					}'
		))));

		$fieldset->add_field(new FormFieldRichTextEditor('description', StringVars::replace_vars($this->lang['modcatfull.form.description'],
			array('number' =>ModcatfullConfig::load()->get_characters_number_to_cut())), $this->get_itemcatfull()->get_description(),
			array('rows' => 3, 'hidden' => !$this->get_itemcatfull()->get_description_enabled())
		));

		$fieldset->add_field(new FormFieldRichTextEditor('contents', $this->common_lang['form.contents'], $this->get_itemcatfull()->get_contents(),
			array('rows' => 15, 'required' => true)
		));

	$fieldset->add_field(new FormFieldActionLink('add_page', $this->lang['modcatfull.form.add.page'] , 'javascript:bbcode_page();', 'fa-pagebreak'));

		if ($this->get_itemcatfull()->get_displayed_author_name() == true)
		{
			$fieldset->add_field(new FormFieldCheckbox('enabled_author_name_customization', $this->lang['modcatfull.form.enabled.author.name.customisation'], $this->get_itemcatfull()->is_enabled_author_name_customization(),
				array('events' => array('click' => '
				if (HTMLForms.getField("enabled_author_name_customization").getValue()) {
					HTMLForms.getField("custom_author_name").enable();
				} else {
					HTMLForms.getField("custom_author_name").disable();
				}'))
			));

			$fieldset->add_field(new FormFieldTextEditor('custom_author_name', $this->lang['modcatfull.form.custom.author.name'], $this->get_itemcatfull()->get_custom_author_name(), array(
				'hidden' => !$this->get_itemcatfull()->is_enabled_author_name_customization(),
			)));
		}

		$other_fieldset = new FormFieldsetHTML('other', $this->common_lang['form.other']);
		$form->add_fieldset($other_fieldset);

		$other_fieldset->add_field(new FormFieldCheckbox('links_visibility', $this->lang['modcatfull.form.enable.links.visibility'], $this->get_itemcatfull()->get_links_visibility()));

		$other_fieldset->add_field(new FormFieldUploadFile('file_url', $this->lang['modcatfull.form.file.url'], $this->get_itemcatfull()->get_file_url()->relative()));

		if ($this->get_itemcatfull()->get_id() !== null && $this->get_itemcatfull()->get_downloads_number() > 0)
		{
			$other_fieldset->add_field(new FormFieldCheckbox('reset_downloads_number', $this->lang['modcatfull.form.reset.downloads.number']));
		}

		$other_fieldset->add_field(new FormFieldUrlEditor('website_url', $this->lang['modcatfull.form.website.url'], $this->get_itemcatfull()->get_website_url()->absolute()));

		$other_fieldset->add_field(new FormFieldCheckbox('displayed_author_name', LangLoader::get_message('config.author_displayed', 'admin-common'), $this->get_itemcatfull()->get_displayed_author_name()));

		$other_fieldset->add_field(new FormFieldUploadPictureFile('thumbnail', $this->common_lang['form.picture'], $this->get_itemcatfull()->get_thumbnail()->relative()));

		$other_fieldset->add_field(ModcatfullService::get_keywords_manager()->get_form_field($this->get_itemcatfull()->get_id(), 'keywords', $this->common_lang['form.keywords'],
			array('description' => $this->common_lang['form.keywords.description'])
		));

		$other_fieldset->add_field(new ModcatfullFormFieldSelectSources('sources', $this->common_lang['form.sources'], $this->get_itemcatfull()->get_sources()));

		$other_fieldset->add_field(new ModcatfullFormFieldCarousel('carousel', $this->lang['modcatfull.form.carousel'], $this->get_itemcatfull()->get_carousel()));

		if (ModcatfullAuthorizationsService::check_authorizations($this->get_itemcatfull()->get_category_id())->moderation())
		{
			$publication_fieldset = new FormFieldsetHTML('publication', $this->common_lang['form.approbation']);
			$form->add_fieldset($publication_fieldset);

			$publication_fieldset->add_field(new FormFieldDateTime('creation_date', $this->common_lang['form.date.creation'], $this->get_itemcatfull()->get_creation_date(),
				array('required' => true)
			));

			if (!$this->get_itemcatfull()->is_published())
			{
				$publication_fieldset->add_field(new FormFieldCheckbox('update_creation_date', $this->common_lang['form.update.date.creation'], false, array('hidden' => $this->get_itemcatfull()->get_status() != Itemcatfull::NOT_PUBLISHED)
				));
			}

			$publication_fieldset->add_field(new FormFieldSimpleSelectChoice('publication_state', $this->common_lang['form.approbation'], $this->get_itemcatfull()->get_publication_state(),
				array(
					new FormFieldSelectChoiceOption($this->common_lang['form.approbation.not'], Itemcatfull::NOT_PUBLISHED),
					new FormFieldSelectChoiceOption($this->common_lang['form.approbation.now'], Itemcatfull::PUBLISHED_NOW),
					new FormFieldSelectChoiceOption($this->common_lang['status.approved.date'], Itemcatfull::PUBLICATION_DATE),
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
				($this->get_itemcatfull()->get_publication_start_date() === null ? new Date() : $this->get_itemcatfull()->get_publication_start_date()),
				array('hidden' => ($this->get_itemcatfull()->get_publication_state() != Itemcatfull::PUBLICATION_DATE))
			));

			$publication_fieldset->add_field(new FormFieldCheckbox('end_date_enable', $this->common_lang['form.date.end.enable'], $this->get_itemcatfull()->enabled_end_date(),
				array('hidden' => ($this->get_itemcatfull()->get_publication_state() != Itemcatfull::PUBLICATION_DATE),
					'events' => array('click' => '
						if (HTMLForms.getField("end_date_enable").getValue()) {
							HTMLForms.getField("publication_end_date").enable();
						} else {
							HTMLForms.getField("publication_end_date").disable();
						}'
				))
			));

			$publication_fieldset->add_field(new FormFieldDateTime('publication_end_date', $this->common_lang['form.date.end'],
				($this->get_itemcatfull()->get_publication_end_date() === null ? new date() : $this->get_itemcatfull()->get_publication_end_date()),
				array('hidden' => !$this->get_itemcatfull()->enabled_end_date())
			));
		}

		$this->build_contribution_fieldset($form);

		$fieldset->add_field(new FormFieldHidden('referrer', $request->get_url_referrer()));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;

		// Positionnement à la bonne page quand on édite un item avec plusieurs pages
		if ($this->get_itemcatfull()->get_id() !== null)
		{
			$current_page = $request->get_getstring('page', '');

			$this->tpl->put('C_PAGE', !empty($current_page));

			if (!empty($current_page))
			{
				$itemcatfull_contents = $this->itemcatfull->get_contents();

				//If item doesn't begin with a page, we insert one
				if (TextHelper::substr(trim($itemcatfull_contents), 0, 6) != '[page]')
				{
					$itemcatfull_contents = '[page]&nbsp;[/page]' . $itemcatfull_contents;
				}

				//Removing [page] bbcode
				$itemcatfull_contents_clean = preg_split('`\[page\].+\[/page\](.*)`usU', $itemcatfull_contents, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

				//Retrieving pages
				preg_match_all('`\[page\]([^[]+)\[/page\]`uU', $itemcatfull_contents, $array_page);

				$page_name = (isset($array_page[1][$current_page-1]) && $array_page[1][$current_page-1] != '&nbsp;') ? $array_page[1][($current_page-1)] : '';

				$this->tpl->put('PAGE', TextHelper::to_js_string($page_name));
			}
		}
	}

	private function build_contribution_fieldset($form)
	{
		if ($this->get_itemcatfull()->get_id() === null && $this->is_contributor_member())
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
		return (!ModcatfullAuthorizationsService::check_authorizations()->write() && ModcatfullAuthorizationsService::check_authorizations()->contribution());
	}

	private function get_itemcatfull()
	{
		if ($this->itemcatfull === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try
				{
					$this->itemcatfull = ModcatfullService::get_itemcatfull('WHERE modcatfull.id=:id', array('id' => $id));
				}
				catch(RowNotFoundException $e)
				{
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_itemcatfull = true;
				$this->itemcatfull = new Itemcatfull();
				$this->itemcatfull->init_default_properties(AppContext::get_request()->get_getint('category_id', Category::ROOT_CATEGORY));
			}
		}
		return $this->itemcatfull;
	}

	private function check_authorizations()
	{
		$itemcatfull = $this->get_itemcatfull();

		if ($itemcatfull->get_id() === null)
		{
			if (!$itemcatfull->is_authorized_to_add())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!$itemcatfull->is_authorized_to_edit())
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
		$itemcatfull = $this->get_itemcatfull();

		$itemcatfull->set_title($this->form->get_value('title'));

		if (ModcatfullService::get_categories_manager()->get_categories_cache()->has_categories())
			$itemcatfull->set_category_id($this->form->get_value('category_id')->get_raw_value());

		$itemcatfull->set_description(($this->form->get_value('enable_description') ? $this->form->get_value('description') : ''));
		$itemcatfull->set_contents($this->form->get_value('contents'));

		$links_visibility = $this->form->get_value('links_visibility') ? $this->form->get_value('links_visibility') : Itemcatfull::DISABLE_LINKS_VISIBILITY;
		$itemcatfull->set_links_visibility($links_visibility);

		$itemcatfull->set_website_url(new Url($this->form->get_value('website_url')));

		$itemcatfull->set_file_url(new Url($this->form->get_value('file_url')));

		$file_size = Url::get_url_file_size($itemcatfull->get_file_url());
		$file_size = (empty($file_size) && $itemcatfull->get_file_size()) ? $itemcatfull->get_file_size() : $file_size;
		$itemcatfull->set_file_size($file_size);

		if ($itemcatfull->get_id() !== null && $itemcatfull->get_downloads_number() > 0 && $this->form->get_value('reset_downloads_number'))
			$itemcatfull->set_downloads_number(0);

		$displayed_author_name = $this->form->get_value('displayed_author_name') ? $this->form->get_value('displayed_author_name') : Itemcatfull::NOTDISPLAYED_AUTHOR_NAME;
		$itemcatfull->set_displayed_author_name($displayed_author_name);
		$itemcatfull->set_thumbnail(new Url($this->form->get_value('thumbnail')));

		if ($this->get_itemcatfull()->get_displayed_author_name() == true)
			$itemcatfull->set_custom_author_name(($this->form->get_value('custom_author_name') && $this->form->get_value('custom_author_name') !== $itemcatfull->get_author_user()->get_display_name() ? $this->form->get_value('custom_author_name') : ''));

		$itemcatfull->set_sources($this->form->get_value('sources'));
		$itemcatfull->set_carousel($this->form->get_value('carousel'));

		if (!ModcatfullAuthorizationsService::check_authorizations($itemcatfull->get_category_id())->moderation())
		{
			if ($itemcatfull->get_id() === null)
				$itemcatfull->set_creation_date(new Date());

			$itemcatfull->set_rewrited_title(Url::encode_rewrite($itemcatfull->get_title()));
			$itemcatfull->clean_publication_start_and_end_date();

			if (ModcatfullAuthorizationsService::check_authorizations($itemcatfull->get_category_id())->contribution() && !ModcatfullAuthorizationsService::check_authorizations($itemcatfull->get_category_id())->write())
				$itemcatfull->set_publication_state(Itemcatfull::NOT_PUBLISHED);
		}
		else
		{
			if ($this->form->get_value('update_creation_date'))
			{
				$itemcatfull->set_creation_date(new Date());
			}
			else
			{
				$itemcatfull->set_creation_date($this->form->get_value('creation_date'));
			}

			$rewrited_title = $this->form->get_value('rewrited_title', '');
			$rewrited_title = $this->form->get_value('personalize_rewrited_title') && !empty($rewrited_title) ? $rewrited_title : Url::encode_rewrite($itemcatfull->get_title());
			$itemcatfull->set_rewrited_title($rewrited_title);

			$itemcatfull->set_publication_state($this->form->get_value('publication_state')->get_raw_value());
			if ($itemcatfull->get_publication_state() == Itemcatfull::PUBLICATION_DATE)
			{
				$config = ModcatfullConfig::load();
				$deferred_operations = $config->get_deferred_operations();

				$old_start_date = $itemcatfull->get_publication_start_date();
				$start_date = $this->form->get_value('publication_start_date');
				$itemcatfull->set_publication_start_date($start_date);

				if ($old_start_date !== null && $old_start_date->get_timestamp() != $start_date->get_timestamp() && in_array($old_start_date->get_timestamp(), $deferred_operations))
				{
					$key = array_search($old_start_date->get_timestamp(), $deferred_operations);
					unset($deferred_operations[$key]);
				}

				if (!in_array($start_date->get_timestamp(), $deferred_operations))
					$deferred_operations[] = $start_date->get_timestamp();

				if ($this->form->get_value('end_date_enable'))
				{
					$old_end_date = $itemcatfull->get_publication_end_date();
					$end_date = $this->form->get_value('publication_end_date');
					$itemcatfull->set_publication_end_date($end_date);

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
					$itemcatfull->clean_publication_end_date();
				}

				$config->set_deferred_operations($deferred_operations);
				ModcatfullConfig::save();
			}
			else
			{
				$itemcatfull->clean_publication_start_and_end_date();
			}
		}

		if ($itemcatfull->get_id() === null)
		{
			$itemcatfull->set_author_user(AppContext::get_current_user());
			$id_itemcatfull = ModcatfullService::add($itemcatfull);
		}
		else
		{
			$now = new Date();
			$itemcatfull->set_updated_date($now);
			$id_itemcatfull = $itemcatfull->get_id();
			ModcatfullService::update($itemcatfull);
		}

		$this->contribution_actions($itemcatfull, $id_itemcatfull);

		ModcatfullService::get_keywords_manager()->put_relations($id_itemcatfull, $this->form->get_value('keywords'));

		Feed::clear_cache('modcatfull');
		ModcatfullCategoriesCache::invalidate();
	}

	private function contribution_actions(Itemcatfull $itemcatfull, $id_itemcatfull)
	{
		if ($itemcatfull->get_id() === null)
		{
			if ($this->is_contributor_member())
			{
				$contribution = new Contribution();
				$contribution->set_id_in_module($id_itemcatfull);
				$contribution->set_description(stripslashes($this->form->get_value('contribution_description')));
				$contribution->set_entitled($itemcatfull->get_title());
				$contribution->set_fixing_url(ModcatfullUrlBuilder::edit_item($id_itemcatfull)->relative());
				$contribution->set_poster_id(AppContext::get_current_user()->get_id());
				$contribution->set_module('modcatfull');
				$contribution->set_auth(
					Authorizations::capture_and_shift_bit_auth(
						ModcatfullService::get_categories_manager()->get_heritated_authorizations($itemcatfull->get_category_id(), Category::MODERATION_AUTHORIZATIONS, Authorizations::AUTH_CHILD_PRIORITY),
						Category::MODERATION_AUTHORIZATIONS, Contribution::CONTRIBUTION_AUTH_BIT
					)
				);
				ContributionService::save_contribution($contribution);
			}
		}
		else
		{
			$corresponding_contributions = ContributionService::find_by_criteria('modcatfull', $id_itemcatfull);
			if (count($corresponding_contributions) > 0)
			{
				foreach ($corresponding_contributions as $contribution)
				{
					$contribution->set_status(Event::EVENT_STATUS_PROCESSED);
					ContributionService::save_contribution($contribution);
				}
			}
		}
		$itemcatfull->set_id($id_itemcatfull);
	}

	private function redirect()
	{
		$itemcatfull = $this->get_itemcatfull();
		$category = $itemcatfull->get_category();

		if ($this->is_new_itemcatfull && $this->is_contributor_member() && !$itemcatfull->is_published())
		{
			DispatchManager::redirect(new UserContributionSuccessController());
		}
		elseif ($itemcatfull->is_published())
		{
			if ($this->is_new_itemcatfull)
				AppContext::get_response()->redirect(ModcatfullUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $itemcatfull->get_id(), $itemcatfull->get_rewrited_title(), AppContext::get_request()->get_getint('page', 1)), StringVars::replace_vars($this->lang['modcatfull.message.success.add'], array('title' => $itemcatfull->get_title())));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : ModcatfullUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $itemcatfull->get_id(), $itemcatfull->get_rewrited_title(), AppContext::get_request()->get_getint('page', 1))), StringVars::replace_vars($this->lang['modcatfull.message.success.edit'], array('title' => $itemcatfull->get_title())));
		}
		else
		{
			if ($this->is_new_itemcatfull)
				AppContext::get_response()->redirect(ModcatfullUrlBuilder::display_pending_items(), StringVars::replace_vars($this->lang['modcatfull.message.success.add'], array('title' => $itemcatfull->get_title())));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : ModcatfullUrlBuilder::display_pending_items()), StringVars::replace_vars($this->lang['modcatfull.message.success.edit'], array('title' => $itemcatfull->get_title())));
		}
	}

	private function build_response(View $tpl)
	{
		$itemcatfull = $this->get_itemcatfull();

		$response = new SiteDisplayResponse($tpl);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['modcatfull.module.title'], ModcatfullUrlBuilder::home());

		if ($itemcatfull->get_id() === null)
		{
			$breadcrumb->add($this->lang['modcatfull.add'], ModcatfullUrlBuilder::add_item($itemcatfull->get_category_id()));
			$graphical_environment->set_page_title($this->lang['modcatfull.add'], $this->lang['modcatfull.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['modcatfull.add']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ModcatfullUrlBuilder::add_item($itemcatfull->get_category_id()));
		}
		else
		{
			$categories = array_reverse(ModcatfullService::get_categories_manager()->get_parents($itemcatfull->get_category_id(), true));
			foreach ($categories as $id => $category)
			{
				if ($category->get_id() != Category::ROOT_CATEGORY)
					$breadcrumb->add($category->get_name(), ModcatfullUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
			}
			$breadcrumb->add($itemcatfull->get_title(), ModcatfullUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $itemcatfull->get_id(), $itemcatfull->get_rewrited_title()));

			$breadcrumb->add($this->lang['modcatfull.edit'], ModcatfullUrlBuilder::edit_item($itemcatfull->get_id()));
			$graphical_environment->set_page_title($this->lang['modcatfull.edit'], $this->lang['modcatfull.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['modcatfull.edit']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ModcatfullUrlBuilder::edit_item($itemcatfull->get_id()));
		}

		return $response;
	}
}
?>
