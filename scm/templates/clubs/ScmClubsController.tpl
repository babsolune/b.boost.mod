<!-- === ScmClubsController === -->
<section id="module-scm" class="several-items">
	<header class="section-header">
		<h1 class="flex-between">
			{@scm.clubs}
		</h1>
	</header>

	# IF C_NO_ITEM #
		<div class="sub-section">
			<div class="content-container">
				<div class="content">
					<div class="message-helper bgc notice">{@common.no.item.now}</div>
				</div>
			</div>
		</div>
	# ELSE #
		<div class="sub-section">
			<div class="content-container">
                # START countries #
                    <details class="clubs-details" open>
                        <summary class="summary-title"><h3>{countries.COUNTRY_NAME}</h3></summary>
                        <div class="col-v-4">
                            # START countries.items #
                                <article class="flex-team flex-left mb">
                                    # IF countries.items.C_HAS_SHIELD #
                                        # IF countries.items.C_HAS_LOGO #
                                            <img src="{countries.items.U_LOGO}" alt="{countries.items.FULL_NAME}">
                                        # ELSE #
                                            # IF countries.items.C_HAS_FLAG #
                                                <img src="{countries.items.U_FLAG}" alt="{countries.items.FULL_NAME}">
                                            # ENDIF #
                                        # ENDIF #
                                    # ENDIF #
                                    <span><a href="{countries.items.U_CLUB}" aria-label="{countries.items.NAME}" class="offload">{countries.items.FULL_NAME}</a></span>
                                </article>
                            # END countries.items #
                        </div>
                        # START countries.leagues #
                            # IF countries.leagues.C_LEAGUE #
                                <details class="clubs-details" open>
                                    <summary class="summary-title"><h4>{countries.leagues.LEAGUE_NAME}</h4></summary>
                                    <div class="col-v-4">
                                        # START countries.leagues.items #
                                            <article class="flex-team flex-left mb">
                                                # IF countries.leagues.items.C_HAS_SHIELD #
                                                    # IF countries.leagues.items.C_HAS_LOGO #
                                                        <img src="{countries.leagues.items.U_LOGO}" alt="{countries.leagues.items.FULL_NAME}">
                                                    # ELSE #
                                                        # IF countries.leagues.items.C_HAS_FLAG #
                                                            <img src="{countries.leagues.items.U_FLAG}" alt="{countries.leagues.items.FULL_NAME}">
                                                        # ENDIF #
                                                    # ENDIF #
                                                # ENDIF #
                                                <span><a href="{countries.leagues.items.U_CLUB}" aria-label="{countries.leagues.items.NAME}" class="offload">{countries.leagues.items.FULL_NAME}</a></span>
                                            </article>
                                        # END countries.leagues.items #
                                    </div>
                                    # START countries.leagues.districts #
                                        <details class="clubs-details" open>
                                            # IF countries.leagues.districts.C_DISTRICT_NAME #<summary class="summary-title"><h5>{countries.leagues.districts.DISTRICT_NAME}</h5></summary># ENDIF #
                                            <div class="col-v-4">
                                                # START countries.leagues.districts.items #
                                                    <article class="flex-team flex-left mb">
                                                        # IF countries.leagues.districts.items.C_HAS_SHIELD #
                                                            # IF countries.leagues.districts.items.C_HAS_LOGO #
                                                                <img src="{countries.leagues.districts.items.U_LOGO}" alt="{countries.leagues.districts.items.FULL_NAME}">
                                                            # ELSE #
                                                                # IF countries.leagues.districts.items.C_HAS_FLAG #
                                                                    <img src="{countries.leagues.districts.items.U_FLAG}" alt="{countries.leagues.districts.items.FULL_NAME}">
                                                                # ENDIF #
                                                            # ENDIF #
                                                        # ENDIF #
                                                        <span><a href="{countries.leagues.districts.items.U_CLUB}" aria-label="{countries.leagues.districts.items.NAME}" class="offload">{countries.leagues.districts.items.FULL_NAME}</a></span>
                                                    </article>
                                                # END countries.leagues.districts.items #
                                            </div>
                                        </details>
                                    # END countries.leagues.districts #
                                </details>
                            # ENDIF #
                        # END countries.leagues #
                    </details>
                # END countries #
			</div>
		</div>
	# ENDIF #
	<footer></footer>
</section>
