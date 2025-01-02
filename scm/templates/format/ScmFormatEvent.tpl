# START blocks #
    # IF blocks.C_SEVERAL_DATES #<h5 class="cell-content">{blocks.TITLE}</h5># ENDIF #
    <div class="cell-flex cell-columns-2">
        # START blocks.items #
            <div id="game-{blocks.items.GAME_ID}" class="cell cell-game">
                <div class="flex-between flex-between-large">
                    <div class="flex-between sm-width-pc-100 md-width-pc-33 small">
                        <time class="sm-width-pc-70 small cell-gap align-center">{blocks.items.GAME_DATE_HOUR_MINUTE}</time>
                        <div class="sm-width-pc-30 cell-gap align-center game-details modal-container" aria-label="{@scm.game.event.details}">
                            <a data-modal="" data-target="target-panel-{blocks.items.GAME_ID}">
                                # IF blocks.items.C_HAS_DETAILS #
                                    # IF blocks.items.C_VIDEO #
                                        <i class="far fa-circle-play"></i>
                                    # ELSE #
                                        <i class="far fa-file-lines"></i>
                                    # ENDIF #
                                # ENDIF #
                            </a>
                            <div id="target-panel-{blocks.items.GAME_ID}" class="modal modal-animation">
                                <div class="close-modal" aria-label="{@common.close}"></div>
                                <div class="content-panel">
                                    <div class="align-right"><a href="#" class="error big hide-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></a></div>
                                    <div class="cell-flex cell-columns-2 cell-tile">
                                        <div class="home-team">
                                            <div class="cell-header flex-team">
                                                <h4 class="cell-name">
                                                    <a href="{blocks.items.U_HOME_CLUB}" class="offload">{blocks.items.HOME_TEAM}</a>
                                                </h4>
                                                # IF blocks.items.C_HAS_HOME_LOGO #<img src="{blocks.items.HOME_LOGO}" alt="{blocks.items.HOME_TEAM}"># ENDIF #
                                            </div>
                                            <div class="cell-score bigger align-center">
                                                {blocks.items.HOME_SCORE}
                                            </div>
                                            <div class="cell-details">{@scm.game.event.goals}</div>
                                            # START blocks.items.home_goals #
                                                <div class="cell-infos">
                                                    <span>{blocks.items.home_goals.PLAYER}</span>
                                                    <span>{blocks.items.home_goals.TIME}'</span>
                                                </div>
                                            # END blocks.items.home_goals #
                                            <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                            # START blocks.items.home_yellow #
                                                <div class="cell-infos">
                                                    <span>{blocks.items.home_yellow.PLAYER}</span>
                                                    <span>{blocks.items.home_yellow.TIME}'</span>
                                                </div>
                                            # END blocks.items.home_yellow #
                                            <div class="cell-details">{@scm.game.event.cards.red}</div>
                                            # START blocks.items.home_red #
                                                <div class="cell-infos">
                                                    <span>{blocks.items.home_red.PLAYER}</span>
                                                    <span>{blocks.items.home_red.TIME}'</span>
                                                </div>
                                            # END blocks.items.home_red #
                                        </div>
                                        <div class="away-team cell">
                                            <div class="cell-header flex-team">
                                                <h4 class="cell-name">
                                                    <a href="{blocks.items.U_AWAY_CLUB}" class="offload">{blocks.items.AWAY_TEAM}</a>
                                                </h4>
                                                # IF blocks.items.C_HAS_AWAY_LOGO #<img src="{blocks.items.AWAY_LOGO}" alt="{blocks.items.AWAY_TEAM}"># ENDIF #
                                            </div>
                                            <div class="cell-score bigger align-center">
                                                {blocks.items.AWAY_SCORE}
                                            </div>
                                            <div class="cell-details">{@scm.game.event.goals}</div>
                                            # START blocks.items.away_goals #
                                                <div class="cell-infos">
                                                    <span>{blocks.items.away_goals.PLAYER}</span>
                                                    <span>{blocks.items.away_goals.TIME}'</span>
                                                </div>
                                            # END blocks.items.away_goals #
                                            <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                            # START blocks.items.away_yellow #
                                                <div class="cell-infos">
                                                    <span>{blocks.items.away_yellow.PLAYER}</span>
                                                    <span>{blocks.items.away_yellow.TIME}'</span>
                                                </div>
                                            # END blocks.items.away_yellow #
                                            <div class="cell-details">{@scm.game.event.cards.red}</div>
                                            # START blocks.items.away_red #
                                                <div class="cell-infos">
                                                    <span>{blocks.items.away_red.PLAYER}</span>
                                                    <span>{blocks.items.away_red.TIME}'</span>
                                                </div>
                                            # END blocks.items.away_red #
                                        </div>
                                    </div>
                                    # IF blocks.items.C_VIDEO #
                                        <a href="{blocks.items.U_VIDEO}" class="button submit" target="blank" rel="noopener noreferer">
                                            <i class="far fa-circle-play"></i> {@scm.watch.video}
                                        </a>
                                    # ENDIF #
                                    <div class="flex-between flex-between-large">
                                        # IF blocks.items.STADIUM #
                                            <div class="md-width-pc-50">
                                                <h5>{@scm.game.event.stadium}</h5>
                                                {blocks.items.STADIUM}
                                            </div>
                                        # ENDIF #
                                        # IF blocks.items.SUMMARY #
                                            <div class="md-width-pc-50">
                                                <h5>{@scm.game.event.summary}</h5>
                                                {blocks.items.SUMMARY}
                                            </div>
                                        # ENDIF #
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    # IF C_DISPLAY_PLAYGROUNDS #
                        <div class="sm-width-pc-100 md-width-pc-33">{@scm.field}: {blocks.items.PLAYGROUND}</div>
                    # ELSE #
                        <div></div>
                    # ENDIF #
                    # IF blocks.items.C_LINK #
                        <div class="sm-width-pc-100 md-width-pc-33 small text-italic align-center">
                            <a href="{blocks.items.U_GROUP}" aria-label="{@scm.group.results}" class="offload cell-gap">
                                {blocks.items.CLUSTER_NAME}
                            </a>
                        </div>
                    # ENDIF #
                </div>
                <div class="flex-between flex-between-large# IF blocks.items.C_EXEMPT # bgc notice# ENDIF #">
                    <div class="team-{blocks.items.HOME_ID} flex-between sm-width-pc-100 md-width-pc-50">
                        <div class="game-team home-team cell-pad flex-team flex-right sm-width-pc-80# IF blocks.items.C_HOME_FAV # text-strong# ENDIF #">
                            <span><a href="{blocks.items.U_HOME_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload">{blocks.items.HOME_TEAM}</a></span>
                            # IF blocks.items.C_HAS_HOME_LOGO #<img src="{blocks.items.HOME_LOGO}" alt="{blocks.items.HOME_TEAM}"># ENDIF #
                        </div>
                        <div class="game-score home-score cell-pad sm-width-pc-20">{blocks.items.HOME_SCORE}# IF blocks.items.C_HAS_PEN # <span class="small">({blocks.items.HOME_PEN})</span># ENDIF #</div>
                    </div>
                    <div class="team-{blocks.items.AWAY_ID} flex-between sm-width-pc-100 md-width-pc-50 invert-team">
                        <div class="game-score away-score cell-pad sm-width-pc-20">{blocks.items.AWAY_SCORE}# IF blocks.items.C_HAS_PEN # <span class="small">({blocks.items.AWAY_PEN})</span># ENDIF #</div>
                        <div class="game-team away-team cell-pad flex-team flex-left sm-width-pc-80# IF blocks.items.C_AWAY_FAV # text-strong# ENDIF #">
                            # IF blocks.items.C_HAS_AWAY_LOGO #<img src="{blocks.items.AWAY_LOGO}" alt="{blocks.items.AWAY_TEAM}"># ENDIF #
                            <span><a href="{blocks.items.U_AWAY_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload">{blocks.items.AWAY_TEAM}</a></span>
                        </div>
                    </div>
                </div>
            </div>
        # END blocks.items #
    </div>
# END blocks #