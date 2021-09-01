<section id="module-clubs">
	<header class="section-header">
		<div class="controls align-right">
			<a href="${relative_url(SyndicationUrlBuilder::rss('clubs', ID_CAT))}" aria-label="{@common.syndication}"><i class="fa fa-rss warning"></i></a>
			# IF NOT C_ROOT_CATEGORY #{@clubs.module.title}# ENDIF #
			# IF IS_ADMIN #<a href="{U_EDIT_CATEGORY}" aria-label="{@common.edit}"><i class="fa fa-edit"></i></a># ENDIF #
		</div>
		<h1>
			# IF C_PENDING #
				{@clubs.pending.items}
			# ELSE #
				# IF C_MEMBER_ITEMS #
					# IF C_MY_ITEMS #{@clubs.my.items}# ELSE #{@clubs.member.items} {MEMBER_NAME}# ENDIF #
				# ELSE #
					# IF C_ROOT_CATEGORY #{@clubs.module.title}# ELSE #{CATEGORY_NAME}# ENDIF #
				# ENDIF #
			# ENDIF #
		</h1>
	</header>

	# IF C_CATEGORY_DESCRIPTION #
		<div class="sub-section">
			<div class="content-container">
				<div class="cat-description">
					{CATEGORY_DESCRIPTION}
				</div>
			</div>
		</div>
	# ENDIF #

	# IF C_SUB_CATEGORIES #
		<div class="sub-section">
			<div class="content-container">
				<div class="cell-flex cell-tile cell-columns-{CATEGORIES_PER_ROW}">
					# START sub_categories_list #
						<div class="cell category-{sub_categories_list.CATEGORY_ID}" itemscope>
							<div class="cell-header">
								<h5 class="cell-name" itemprop="about"><a href="{sub_categories_list.U_CATEGORY}">{sub_categories_list.CATEGORY_NAME}</a></h5>
								<span class="small pinned notice" role="contentinfo" aria-label="{@clubs.items.number}">
									{sub_categories_list.ITEMS_NUMBER}
								</span>
							</div>
							# IF sub_categories_list.C_CATEGORY_THUMBNAIL #
								<div class="cell-body">
									<div class="cell-thumbnail cell-landscape cell-center">
										<img itemprop="thumbnailUrl" src="{sub_categories_list.U_CATEGORY_THUMBNAIL}" alt="{sub_categories_list.CATEGORY_NAME}" />
										<a class="cell-thumbnail-caption" itemprop="about" href="{sub_categories_list.U_CATEGORY}">
											{@common.see.category}
										</a>
									</div>
								</div>
							# ENDIF #
						</div>
					# END sub_categories_list #
				</div>
				# IF C_SUBCATEGORIES_PAGINATION #<div class="align-center"># INCLUDE SUBCATEGORIES_PAGINATION #</div># ENDIF #
			</div>
		</div>
	# ENDIF #

	# IF C_GMAP_ENABLED #
		<div id="gmap"></div>
	# ELSE #
		<p>{@clubs.no.gmap}</p>
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
							<th class="col-small">{@clubs.logo}</th>
							<th>{@common.name}</th>
							<th class="coll-small">{@common.website}</th>
							<th class="col-small">{@common.visits.number}</th>
							# IF C_CONTROLS #<th class="col-small"></th># ENDIF #
						</tr>
					</thead>
					<tbody>
						# START items #
							<tr>
								<td>
									# IF items.C_LOGO_MINI #
										<img src="{items.U_LOGO_MINI}" alt="{items.TITLE}" />
									# ELSE #
										<img src="{PATH_TO_ROOT}/clubs/clubs.png" alt="{items.TITLE}" />
									# ENDIF #
								</td>
								<td>
									<a href="{items.U_ITEM}"><span itemprop="name" aria-label="{@common.see.details}">{items.TITLE}</span></a>
								</td>
								<td>
									# IF items.C_VISIT #
										<a class="basic-button" aria-label="{@clubs.visit.website}" # IF items.C_NEW_WINDOW #target="_blank" rel="noopener noreferrer"# ENDIF # href="{items.U_VISIT}">{@common.visit}</a>
									# ELSE #
										{@clubs.no.website}
									# ENDIF #
								</td>
								<td>
									{items.VIEWS_NUMBER}
								</td>
								# IF C_CONTROLS #
									<td>
										# IF items.C_EDIT #
											<a href="{items.U_EDIT}" aria-label="{@common.edit}"><i class="fa fa-edit"></i></a>
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
						<article id="article-clubs-{items.ID}" class="clubs-item several-items cell# IF items.C_IS_PARTNER # content-friends# ENDIF ## IF items.C_IS_PRIVILEGED_PARTNER # content-privileged-friends# ENDIF ## IF items.C_NEW_CONTENT # new-content# ENDIF#" itemscope="itemscope" itemtype="http://schema.org/CreativeWork">
							<header>
								<h2>
									<a href="{items.U_ITEM}" itemprop="name">{items.TITLE}</a>
								</h2>
							</header>
							<div class="cell-body">
								<div class="cell-infos">
									<div class="more">

									</div>
									# IF items.C_CONTROLS #
										<div class="controls align-right">
											# IF items.C_EDIT #<a href="{items.U_EDIT}" aria-label="{@common.edit}"><i class="far fa-edit"></i></a># ENDIF #
											# IF items.C_DELETE #<a href="{items.U_DELETE}" aria-label="{@common.delete}" data-confirmation="delete-element"><i class="far fa-trash-alt"></i></a># ENDIF #
										</div>
									# ENDIF #
								</div>
								# IF items.C_LOGO #
									<div class="cell-thumbnail cell-landscape cell-center">
										<img src="{items.U_LOGO}" alt="{items.TITLE}" itemprop="image" />
										<a href="{items.U_ITEM}" class="cell-thumbnail-caption offload">
											{@common.see.details}
										</a>
									</div>
								# ENDIF #
								<div class="cell-content">
									<div class="options infos">
										<div class="center">
											# IF items.C_VISIBLE #
												# IF items.C_LOGO #
													<p class="clubs-logo">
														<img src="{items.U_LOGO}" alt="{items.NAME}" itemprop="image" />
													</p>
												# ENDIF #
												# IF items.C_VISIT #
													<a href="{items.U_VISIT}" # IF items.C_NEW_WINDOW #target="_blank" rel="noopener noreferrer"# ENDIF # class="basic-button">
														<i class="fa fa-globe"></i> {@common.visit}
													</a>
													# IF IS_USER_CONNECTED #
														<a href="{items.U_DEADLINK}" class="basic-button alt" aria-label="{@contribution.report.dead.link}">
															<i class="fa fa-unlink"></i>
														</a>
													# ENDIF #
												# ELSE #
													{@clubs.no.website}
												# ENDIF #
											# ENDIF #
										</div>
										<h6>{@clubs.link.infos}</h6>
										<span class="text-strong">{@common.visits.number} : </span><span>{items.VIEWS_NUMBER}</span><br/>
										# IF NOT C_CATEGORY #<span class="text-strong">{@common.category} : </span><span><a itemprop="about" class="small" href="{items.U_CATEGORY}">{items.CATEGORY_NAME}</a></span><br/># ENDIF #
									</div>
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
				<div class="center">
					{@common.no.item.now}
				</div>
			# ENDIF #
		</div>
	# ENDIF #

	<footer># IF C_PAGINATION # # INCLUDE PAGINATION # # ENDIF #</footer>
</section>
# IF C_GMAP_ENABLED #
	<script src="https://maps.googleapis.com/maps/api/js?key={GMAP_API_KEY}"></script>
	<script>
		var stadiums = Array()
		# START items #
			# IF items.C_LOGO_MINI #
				var logoMini = ${escapejs(items.U_LOGO_MINI)};
			# ELSE #
				var logoMini = "{PATH_TO_ROOT}/clubs/clubs.png";
			# ENDIF #
			# IF items.C_LOGO #
				var logo = ${escapejs(items.U_LOGO)};
			# ELSE #
				var logo = "{PATH_TO_ROOT}/clubs/clubs.png";
			# ENDIF #

			# IF items.C_STADIUM_LOCATION #
				var address = ${escapejs(items.STADIUM_ADDRESS)},
					verticalAddress = address.replace(/\,/g, '<br />');
				stadiums.push([
					${escapejs(items.LATITUDE)},
					${escapejs(items.LONGITUDE)},
					${escapejs(items.TITLE)},
					logoMini,
					${escapejs(items.U_ITEM)},
					${escapejs(items.STAD_LAT)},
					${escapejs(items.STAD_LNG)},
					verticalAddress,
					logo
				]);
				console.log(${escapejs(items.U_LOGO)});
			# ENDIF #
		# END items #

		var map = new google.maps.Map(document.getElementById('gmap'), {
			zoom: 10,
			center: new google.maps.LatLng({DEFAULT_LAT}, {DEFAULT_LNG}),
			mapTypeId: 'roadmap'
		});

		var infowindow = new google.maps.InfoWindow();

		var marker, i;
		var markers = new Array();

		for (i = 0; i < stadiums.length; i++) {
			marker = new google.maps.Marker({
				position: new google.maps.LatLng(stadiums[i][0], stadiums[i][1]),
				map: map,
				icon: stadiums[i][3]
			});

			markers.push(marker);
			google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					infowindow.setContent('<h6><a href="' + stadiums[i][4] + '" aria-label="{@common.see.details}">' + stadiums[i][2] + '</a></h6>'
					+ '<div class="cell-flex cell-columns-2">'
						+ '<div class="cell cell-1-4">'
							+ '<div class="align-center marker-logo"><img src="' + stadiums[i][8] + '" alt="' + stadiums[i][2] + '" /></div>'
						+ '</div>'
						+ '<div class="cell cell-list gm-stadium-location">'
							+ '<ul>'
								+ '<li class="spacer"><span class="text-strong">{@clubs.stadium.address}:</span> <span class="d-block">' + stadiums[i][7] + '</span></li>'
								+ '<li class="li-stretch"><span class="text-strong">{@clubs.stadium.lat}:</span> <span class="pinned visitor">' + stadiums[i][5] + '</span></li>'
								+ '<li class="li-stretch"><span class="text-strong">{@clubs.stadium.lng}:</span> <span class="pinned visitor">' + stadiums[i][6]) + '</span></li>'
							+ '</ul>'
						+ '</div>'
					+ '</div>';
					infowindow.open(map, marker);
				}
			})(marker, i));

			function AutoCenter() {
				var bounds = new google.maps.LatLngBounds();
				jQuery.each(markers, function (index, marker) {
					bounds.extend(marker.position);
				});
				# IF C_SEVERAL_ITEMS #map.fitBounds(bounds);# ELSE #map.setCenter(bounds.getCenter());# ENDIF #
			}
			AutoCenter();
		}
	</script>
# ENDIF #
