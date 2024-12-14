<section id="module-scm" class="category-{CATEGORY_ID} single-item">
	# INCLUDE MENU #
    # IF NOT C_IS_MASTER #<h2>{@scm.infos}</h2># ENDIF #
	<div class="sub-section">
		<div class="content-container">
			# IF NOT C_VISIBLE #
				<div class="content">
					# INCLUDE NOT_VISIBLE_MESSAGE #
				</div>
			# ENDIF #
            # IF IS_MODERATOR #
                <div class="flex-between"><span></span>  <span class="small message-helper bgc moderator">{L_VIEWS_NUMBER}</span></div>
            # ENDIF #
            # IF C_IS_MASTER #
                # START sub_events #
                    <h2>
                        <a href="{sub_events.U_EVENT}" class="offload">
                            {sub_events.DIVISION_NAME}</a>
                    </h2>
                    <div class="more">
                        <span>{sub_events.START_DATE} | {sub_events.END_DATE}</span>
                        # IF sub_events.C_IS_ENDED #
                            <span class="warning">{@scm.event.ended.event}</span>
                        # ENDIF #
                    </div>
                    <div class="content">
                        # IF sub_events.C_CHAMPIONSHIP #<div itemprop="text"># INCLUDE sub_events.CHAMPIONSHIP_HOME #</div># ENDIF #
                        # IF sub_events.C_CUP #<div itemprop="text"># INCLUDE sub_events.CUP_HOME #</div># ENDIF #
                        # IF sub_events.C_TOURNAMENT #<div itemprop="text"># INCLUDE sub_events.TOURNAMENT_HOME #</div># ENDIF #
                    </div>
                # END sub_events #
            # ELSE #
                # IF C_HAS_GAMES #
                        <article itemscope="itemscope" itemtype="https://schema.org/CreativeWork" id="scm-item-{ID}" class="scm-item# IF C_NEW_CONTENT # new-content# ENDIF #">
                            <div class="content">
                                # IF C_CHAMPIONSHIP #<div itemprop="text"># INCLUDE CHAMPIONSHIP_HOME #</div># ENDIF #
                                # IF C_CUP #<div itemprop="text"># INCLUDE CUP_HOME #</div># ENDIF #
                                # IF C_TOURNAMENT #<div itemprop="text"># INCLUDE TOURNAMENT_HOME #</div># ENDIF #
                            </div>

                            <aside>${ContentSharingActionsMenuService::display()}</aside>

                            # IF C_SOURCES #
                                <aside class="sources-container">
                                    <span class="text-strong"><i class="fa fa-map-signs" aria-hidden="true"></i> {@common.sources}</span> :
                                    # START sources #
                                        <a itemprop="isBasedOnUrl" href="{sources.URL}" class="pinned link-color offload" rel="nofollow">{sources.NAME}</a># IF sources.C_SEPARATOR ## ENDIF #
                                    # END sources #
                                </aside>
                            # ENDIF #
                        </article>
                # ELSE #
                    <div class="message-helper bgc notice">{@scm.message.no.games}</div>
                # ENDIF #
            # ENDIF #
		</div>
	</div>
	<footer>
		<meta itemprop="url" content="{U_EVENT}">
		<meta itemprop="description" content="" />
	</footer>
</section>
