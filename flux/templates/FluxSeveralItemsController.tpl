<section id="module-flux">
	<header class="section-header">
		<div class="controls align-right">
			<a class="offload" href="${relative_url(SyndicationUrlBuilder::rss('flux', ID_CAT))}" aria-label="{@common.syndication}"><i class="fa fa-rss warning"></i></a>
			# IF NOT C_ROOT_CATEGORY #{MODULE_NAME}# ENDIF #
			# IF IS_ADMIN #<a class="offload" href="{U_EDIT_CATEGORY}" aria-label="{@common.edit}"><i class="fa fa-edit"></i></a># ENDIF #
		</div>
		<h1>
			# IF C_PENDING #
				{@flux.pending.items}
			# ELSE #
				# IF C_MEMBER_ITEMS #
					# IF C_MY_ITEMS #{@flux.my.items}# ELSE #{@flux.member.items} {MEMBER_NAME}# ENDIF #
				# ELSE #
					# IF C_ROOT_CATEGORY #{MODULE_NAME}# ELSE #{CATEGORY_NAME}# ENDIF #
				# ENDIF #
			# ENDIF #
		</h1>
	</header>

	# IF C_ROOT_CATEGORY #
		<div class="sub-section">
			<div class="content-container">
				<div class="cat-description">
					{ROOT_CATEGORY_DESC}
				</div>
			</div>
		</div>
		<div
			data-listorder-control="dropdown-sort"
			class="listorder-drop-down hidden"
			data-group="feed-items"
			data-name="sorttitle">
			<div data-type="panel" class="listorder-dd-panel"></div>
			<ul data-type="content" class="dropdown-container">
				<li> {@common.sort.by.date}
					<em class="sort-type bgc-full link-color" data-path=".lo-date" data-order="asc" data-type="number"><span class="sr-only">{@common.sort.by.date} &#8593;</span> <i class="fa fa-sort-numeric-up-alt"></i></em>
					<em class="sort-type bgc-full logo-color" data-path=".lo-date" data-order="desc" data-type="number" data-selected="true"><span class="sr-only">{@common.sort.by.date} &#8595;</span> <i class="fa fa-sort-numeric-down-alt"></i></em>
				</li>
			</ul>
		</div>
		<ul data-type="content" data-listorder-group="feed-items">
			# START feed_items #
				<li data-listorder-item>
					<span class="lo-date hidden">{feed_items.SORT_DATE}</span>
					<span class="flex-between">
						<span>
							<a class="big" href="{feed_items.U_ITEM}"# IF feed_items.C_NEW_WINDOW # target="_blank" rel="noopener noreferrer"# ENDIF #>
								{feed_items.TITLE}
							</a>

						</span>
						<span class="small align-right"><span class="text-italic">{feed_items.ITEM_HOST}</span> | {feed_items.DATE}</span>
					</span>
					<p>
						# IF feed_items.C_HAS_THUMBNAIL #
							<img src="{feed_items.U_THUMBNAIL}" class="align-left" alt="{feed_items.TITLE}" />
						# ENDIF #
						{feed_items.SUMMARY}# IF feed_items.C_READ_MORE #...# ENDIF #
					</p>
				</li>
			# END feed_items #
		</ul>
		<div class="sub-section items-pagination">
			<div class="content-container">
				<nav
				   	class="listorder-pagination pagination"
				   	data-listorder-control="pagination"
			        data-group="feed-items"
			        data-items-per-page="{ITEMS_NUMBER}"
			        data-current-page="0"
			        data-name="pagination1"
					data-id="paging">
					<!-- <p data-type="info" class="align-center">
						{@common.listorder.item.start} - {@common.listorder.item.end} / {@common.listorder.items.number} ${TextHelper::lcfirst(@items)}
					</p>
					<ul>
						<li class="pagination-item" data-type="first" aria-label="{@common.pagination.first}"><a href="#"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i></a> </li>
					    <li class="pagination-item" data-type="prev" aria-label="{@common.pagination.previous}"><a href="#"><i class="fa fa-chevron-left" aria-hidden="true"></i></a> </li>

					    <ul class="listorder-holder" data-type="pages">
					        <li class="pagination-item" data-type="page"><a href="#">{@common.listorder.page.number}</a></li>
					    </ul>

					    <li class="pagination-item" data-type="next" aria-label="{@common.pagination.next}"><a href="#"><i class="fa fa-chevron-right" aria-hidden="true"></i></a> </li>
					    <li class="pagination-item" data-type="last" aria-label="{@common.pagination.last}"><a href="#"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></a> </li>
					</ul>
					<div class="align-center">
			            <select data-type="items-per-page">
			                <option value="{ITEMS_NUMBER}"> {ITEMS_NUMBER} {@common.pagination.per}</option>
			                <option value="25"> 25 {@common.pagination.per}</option>
			                <option value="50"> 50 {@common.pagination.per}</option>
			                <option value="0"> {@common.all.alt} </option>
			            </select>
					</div> -->
				</nav>
			</div>
		</div>
	# ENDIF #

	# IF C_SUB_CATEGORIES #
		<div class="sub-section">
			<div class="content-container">
				<div class="cell-flex cell-tile cell-columns-{CATEGORIES_PER_ROW}">
					# START sub_categories_list #
						<div class="cell category-{sub_categories_list.CATEGORY_ID}" itemscope>
							<div class="cell-header colored-category marker-container" data-color-surround="{sub_categories_list.CATEGORY_COLOR}">
								<h5 class="cell-name" itemprop="about">
									<i class="inner-marker ${sub_categories_list.CATEGORY_INNER_ICON}" aria-hidden="true"></i>
									<a class="offload" href="{sub_categories_list.U_CATEGORY}">
										{sub_categories_list.CATEGORY_NAME}
									</a>
								</h5>
								<span class="small pinned notice" role="contentinfo" aria-label="{@flux.items.number}">
									{sub_categories_list.ITEMS_NUMBER}
								</span>
							</div>
						</div>
					# END sub_categories_list #
				</div>
				# IF C_SUBCATEGORIES_PAGINATION #<div class="align-center"># INCLUDE SUBCATEGORIES_PAGINATION #</div># ENDIF #
			</div>
		</div>
	# ENDIF #

	# IF C_ITEMS #
		# IF C_SEVERAL_ITEMS #
			<div class="spacer"></div>
		# ENDIF #
		<div class="content-container">
			# IF C_TABLE_VIEW #
				<table class="table">
					<thead>
						<tr>
							<th>{@common.name}</th>
							<th class="coll-small" aria-label="{@common.website}"><i class="fa fa-link" aria-hidden="true"></i><span class="hidden-large-screens">{@common.website}</span></th>
							<th class="col-small" aria-label="{@common.views.number}"><i class="fa fa-eye" aria-hidden="true"></i><span class="hidden-large-screens">{@common.views.number}</span></th>
							<th class="col-small" aria-label="{@common.visits.number}"><i class="fa fa-external-link-alt" aria-hidden="true"></i><span class="hidden-large-screens">{@common.visits.number}</span></th>
							# IF C_CONTROLS #<th class="col-small" aria-label="{@common.moderation}"><i class="fa fa-cog" aria-hidden="true"></i><span class="hidden-large-screens">{@common.moderation}</span></th># ENDIF #
						</tr>
					</thead>
					<tbody>
						# START items #
							<tr>
								<td>
									<a class="offload" href="{items.U_ITEM}"><span itemprop="name" aria-label="{@common.see.details}">{items.TITLE}</span></a>
								</td>
								<td>
									# IF items.C_VISIT #
										<a class="basic-button" aria-label="{@common.visit}" # IF items.C_NEW_WINDOW #target="_blank" rel="noopener noreferrer"# ENDIF # href="{items.U_VISIT}">{@common.visit}</a>
									# ELSE #
										{@flux.no.website}
									# ENDIF #
								</td>
								<td>
									{items.VIEWS_NUMBER}
								</td>
								<td>
									{items.VISITS_NUMBER}
								</td>
								# IF C_CONTROLS #
									<td>
										# IF items.C_EDIT #
											<a class="offload" href="{items.U_EDIT}" aria-label="{@common.edit}"><i class="fa fa-edit"></i></a>
										# ENDIF #
										# IF items.C_DELETE #
											<a href="{items.U_DELETE}" aria-label="{@common.delete}" data-confirmation="delete-element"><i class="far fa-trash-alt"></i></a>
										# ENDIF #
									</td>
								# ENDIF #
							</tr>
						# END items #
					</tbody>
				</table>
			# ELSE #
				<div class="cell-flex cell-columns-{ITEMS_PER_ROW}">
					# START items #
						<article id="article-flux-{items.ID}" class="flux-item several-items cell# IF items.C_IS_PARTNER # content-friends# ENDIF ## IF items.C_IS_PRIVILEGED_PARTNER # content-privileged-friends# ENDIF ## IF items.C_NEW_CONTENT # new-content# ENDIF#" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
							<header class="cell-header">
								<h2>
									<a class="offload" href="{items.U_ITEM}" itemprop="name">{items.TITLE}</a>
								</h2>
							</header>
							<div class="cell-body">
								<div class="cell-infos">
									<div class="more">
										<span class="pinned" aria-label="{@common.views.number}"> <i class="fa fa-eye" aria-hidden="true"></i> {items.VIEWS_NUMBER}</span>
										# IF items.C_VISIT #<span class="pinned" aria-label="{@common.visits.number}"> <i class="fa fa-external-link-alt" aria-hidden="true"></i> {items.VISITS_NUMBER}</span># ENDIF #
										<span class="pinned-category" data-color-surround="{items.CATEGORY_COLOR}" aria-label="{@common.category}"><i class="far fa-folder" aria-hidden="true"></i> <a class="offload" itemprop="about" href="{items.U_CATEGORY}">{items.CATEGORY_NAME}</a></span>
									</div>
									# IF items.C_CONTROLS #
										<div class="controls align-right">
											# IF items.C_EDIT #<a class="offload" href="{items.U_EDIT}" aria-label="{@common.edit}"><i class="far fa-edit"></i></a># ENDIF #
											# IF items.C_DELETE #<a href="{items.U_DELETE}" aria-label="{@common.delete}" data-confirmation="delete-element"><i class="far fa-trash-alt"></i></a># ENDIF #
										</div>
									# ENDIF #
								</div>
								# IF items.C_HAS_THUMBNAIL #
									<div class="cell-thumbnail cell-landscape cell-center">
										<img src="{items.U_THUMBNAIL}" alt="{items.TITLE}" itemprop="image" />
										<a href="{items.U_ITEM}" class="cell-thumbnail-caption offload">
											{@common.see.details}
										</a>
									</div>
								# ENDIF #
								<div class="cell-content">
									<div itemprop="text">{items.CONTENT}</div>
								</div>
							</div>
						</article>
					# END items #
				</div>
			# ENDIF #
		</div>
	# ELSE #
		<div class="content">
			# IF NOT C_HIDE_NO_ITEM_MESSAGE #
				<div class="message-helper bgc notice">
					{@common.no.item.now}
				</div>
			# ENDIF #
		</div>
	# ENDIF #

	<footer># IF C_PAGINATION # # INCLUDE PAGINATION # # ENDIF #</footer>
</section>

<script>
	jQuery('document').ready(function(){
		listorder.init();
	});
</script>
