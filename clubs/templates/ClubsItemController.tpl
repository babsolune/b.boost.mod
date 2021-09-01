<section id="module-clubs" class="category-{CATEGORY_ID}">
	<header class="section-header">
		<div class="controls align-right">
			<a class="offload" href="{U_SYNDICATION}" aria-label="{@common.syndication}"><i class="fa fa-rss"></i></a>
			{@clubs.module.title}# IF NOT C_ROOT_CATEGORY # - {CATEGORY_NAME}# ENDIF # # IF IS_ADMIN #<a class="offload" href="{U_EDIT_CATEGORY}" aria-label="{@common.edit}"><i class="fa fa-edit"></i></a># ENDIF #
		</div>
		<h1>
			<span id="name" itemprop="name">{TITLE}</span>
		</h1>
	</header>
	<div class="sub-section">
		<div class="content-container">
			# IF NOT C_VISIBLE #
				# INCLUDE NOT_VISIBLE_MESSAGE #
			# ENDIF #
			<article id="article-clubs-{ID}" itemscope="itemscope" itemtype="http://schema.org/CreativeWork" class="clubs-item single-item# IF C_IS_PARTNER # content-friends# ENDIF ## IF C_IS_PRIVILEGED_PARTNER # content-privileged-friends# ENDIF ## IF C_NEW_CONTENT # new-content# ENDIF#">
				# IF C_CONTROLS #
					<div class="controls align-right">
						# IF C_EDIT #
							<a class="offload" href="{U_EDIT}" aria-label="{@common.edit}"><i class="fa fa-edit"></i></a>
						# ENDIF #
						# IF C_DELETE #
							<a class="offload" href="{U_DELETE}" aria-label="{@common.delete}" data-confirmation="delete-element"><i class="far fa-trash-alt"></i></a>
						# ENDIF #
					</div>
				# ENDIF #
				<div class="content cell-tile">
					<div class="cell cell-options">
						<div class="cell-header">{@clubs.link.infos}</div>
						# IF C_LOGO #
							<div class="cell-body">
								<div class="cell-thumbnail">
									<img src="{U_LOGO}" alt="{NAME}" itemprop="image" />
								</div>
							</div>
						# ENDIF #
						<div class="cell-list small">
							<ul>
								# IF C_VISIBLE #
									# IF C_VISIT #
										<li class="li-stretch">
											<a href="{U_VISIT}" # IF C_NEW_WINDOW #target="_blank" rel="noopener noreferrer"# ENDIF # class="button submit offload" aria-label="{@clubs.visit.website}">
												<i class="fa fa-globe" aria-hidden="true"></i> {@common.visit}
											</a>
											# IF IS_USER_CONNECTED #
												<a href="{U_DEADLINK}" data-confirmation="{@contribution.dead.link.confirmation}" class="button offload bgc-full warning" aria-label="{@contribution.report.dead.link}">
													<i class="fa fa-unlink" aria-hidden="true"></i>
												</a>
											# ENDIF #
										</li>
									# ELSE #
										<li>{@clubs.no.website}</li>
									# ENDIF #
								# ENDIF #
								<li class="li-stretch"><span class="text-strong">{@common.visits.number} : </span><span>{VIEWS_NUMBER}</span></li>
								# IF C_ENABLED_COMMENTS #
									<li class="li-stretch"># IF C_COMMENTS # {NUMBER_COMMENTS} # ENDIF # {L_COMMENTS}</li>
								# ENDIF #
							</ul>
						</div>
						# IF C_VISIBLE #
							# IF C_ENABLED_NOTATION #
								<div class="spacer"></div>
								<div class="center">{NOTATION}</div>
							# ENDIF #
						# ENDIF #
					</div>

					# IF C_CONTENT #
						<div itemprop="text">
							{CONTENT}
						</div>
					# ENDIF #

					# IF C_LOCATION #
						<aside>
							<h6>{@clubs.headquarter.address} :</h6>
								# START location #
									<p>{location.STREET_NUMBER}# IF C_STREET_NUMBER #,# ENDIF # {location.ROUTE} </p>
									<p>{location.POSTAL_CODE} {location.CITY}</p>
								# END location #
						</aside>
					# ENDIF #

					# IF C_COLORS #
						<aside>
							<h6>{@clubs.colors} :</h6>
								# START colors #
									<span class="club-colors" aria-label="{colors.NAME}" style="background-color: {colors.COLOR}"></span>
								# END colors #
						</aside>
					# ENDIF #

					# IF C_CONTACT #
						<aside>
							<h6>{@clubs.contact} :</h6>
							<div class="flex-between">
									# IF C_PHONE #
										<span>
											<i class="fa fa-fw fa-phone"></i> {PHONE}
										</span>
									# ENDIF #
									# IF C_EMAIL #
										<a href="mailto:{EMAIL}" aria-label="{@clubs.labels.email}"><i class="fa fa-fw fa-envelope fa-lg"></i></a>
									# ENDIF #
									# IF C_FACEBOOK #
										<a class="offload" href="{U_FACEBOOK}" aria-label="Facebook" # IF C_NEW_WINDOW #target="_blank" rel="noopener noreferrer"# ENDIF #><i class="fab fa-fw fa-facebook fa-lg"></i></a>
									# ENDIF #
									# IF C_TWITTER #
										<a class="offload" href="{U_TWITTER}" aria-label="Twitter" # IF C_NEW_WINDOW #target="_blank" rel="noopener noreferrer"# ENDIF #><i class="fab fa-fw fa-twitter fa-lg"></i></a>
									# ENDIF #
									# IF C_INSTAGRAM #
										<a class="offload" href="{U_INSTAGRAM}" aria-label="Instagram" # IF C_NEW_WINDOW #target="_blank" rel="noopener noreferrer"# ENDIF #><i class="fab fa-fw fa-instagram fa-lg"></i></a>
									# ENDIF #
									# IF C_YOUTUBE #
										<a class="offload" href="{U_YOUTUBE}" aria-label="Instagram" # IF C_NEW_WINDOW #target="_blank" rel="noopener noreferrer"# ENDIF #><i class="fab fa-fw fa-youtube fa-lg"></i></a>
									# ENDIF #
							</div>
						</aside>
					# ENDIF #

				</div>
				# IF C_GMAP_ENABLED #
					# IF C_DEFAULT_ADDRESS #
						# IF C_STADIUM_LOCATION #
							<div class="fixed-top">
								<div id="gmap"></div>
							</div>
							<div id="panel"></div>
							<h5>{@clubs.stadium.gps} :</h5>
							<p>
								{@clubs.stadium.lat} : {STAD_LAT} / {LATITUDE}
							<br />{@clubs.stadium.lng} : {STAD_LNG} / {LONGITUDE}
							</p>
						# ELSE #
							<div class="message-helper bgc warning">{@clubs.no.gps}</div>
						# ENDIF #
					# ELSE #
						<div class="message-helper bgc warning">{@H|clubs.no.default.address}</div>
					# ENDIF #
				# ELSE #
					<div class="message-helper bgc warning">{@clubs.no.gmap}</div>
				# ENDIF #
				<aside>
					# INCLUDE COMMENTS #
				</aside>
			</article>
		</div>
	</div>
	<footer>
		<meta itemprop="url" content="{U_ITEM}">
		<meta itemprop="description" content="${escape(DESCRIPTION)}" />
		# IF C_COMMENTS_ENABLED #
			<meta itemprop="discussionUrl" content="{U_COMMENTS}">
			<meta itemprop="interactionCount" content="{COMMENTS_NUMBER} UserComments">
		# ENDIF #
	</footer>
</section>

# IF C_GMAP_ENABLED #

	<script src="https://maps.googleapis.com/maps/api/js?key={GMAP_API_KEY}"></script>
	<script src="{PATH_TO_ROOT}/clubs/templates/js/sticky.js"></script>
	<script>
		jQuery(function(){
			jQuery('.fixed-top').sticky();
		});
	</script>

	<script>
		# IF C_STADIUM_LOCATION #
			var club = {lat: {LATITUDE}, lng: {LONGITUDE}};
		# ELSE #
			var club = {lat: {DEFAULT_LAT}, lng: {DEFAULT_LNG}};
		# ENDIF #

			var map = new google.maps.Map(document.getElementById('gmap'), {
			  	zoom: 10,
			  	center: club,
				mapTypeId: 'roadmap'
			});

		var panel = document.getElementById('panel'),
			origin = {lat: {DEFAULT_LAT}, lng: {DEFAULT_LNG}}

		calculate = function(){
			origin      = origin
			destination = club; // The point of arrival
			if(origin && destination){
				var request = {
					origin      : origin,
					destination : destination,
					provideRouteAlternatives: true,
					// avoidTolls: true,
					travelMode  : google.maps.DirectionsTravelMode.DRIVING, // Type of travel
				}
				direction = new google.maps.DirectionsRenderer({
					draggable: true,
					map: map,
					panel: panel
				});
				var directionsService = new google.maps.DirectionsService(); // Route planning service
				directionsService.route(request, function(response, status){ // Sends the request to calculate the route
					if(status == google.maps.DirectionsStatus.OK){
						direction.setDirections(response); // Trace the route on the map and the different stages of the route
					}
				});
			}
		};

		calculate();
	</script>
# ENDIF #
