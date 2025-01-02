<section id="module-scm" class="category-{CATEGORY_ID} single-item">
    <header class="section-header"><h1>{@scm.games.late.list}</h1></header>
	<div class="sub-section">
		<div class="content-container">
            # IF C_ITEMS #
                # START dates #
                    <div class="cell-list">
                        <span class="pinned bgc moderator">{dates.DATE}</span>
                        <ul>
                            # START dates.items #
                                <li class="li-stretch# IF dates.items.C_OVERTIME # bgc warning# ENDIF #">
                                    <div>
                                        <span>
                                            # IF dates.items.C_IS_SUB #{dates.items.MASTER_EVENT} : # ENDIF #
                                            {dates.items.GAME_DIVISION} | 
                                        </span>
                                        <span class="text-italic">{dates.items.GAME_CATEGORY}</span>
                                    </div>
                                    <span>{dates.items.HOME_TEAM} <strong>vs</strong> {dates.items.AWAY_TEAM}</span>
                                    <a href="{dates.items.U_EDIT_DAY}" class="offload">{@scm.day} {dates.items.CLUSTER}</a>
                                </li>
                            # END dates.items #
                        </ul>
                    </div>
                # END dates #
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
