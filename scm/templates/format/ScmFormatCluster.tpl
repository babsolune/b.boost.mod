# START blocks #
    <div class="cell-vertical">
        <h5>
            # IF blocks.C_ROUND #{@scm.round}# ENDIF #
            {blocks.TITLE}
        </h5>
        # START blocks.sub_blocks #
            # IF blocks.sub_blocks.C_SUB_ROUND ## IF blocks.sub_blocks.C_SEVERAL_DATES #<h6>{blocks.sub_blocks.SUB_TITLE}</h6># ENDIF ## ENDIF #
            # START blocks.sub_blocks.items #
                <div id="game-{blocks.sub_blocks.items.GAME_ID}" class="cell cell-game">
                    <div class="flex-between">
                        <time class="sm-width-pc-30 small cell-gap">{blocks.sub_blocks.items.GAME_DATE_HOUR_MINUTE}</time>
                        # IF blocks.sub_blocks.items.C_STATUS #
                            <div class="sm-width-pc-40 smaller text-italic align-center bgc notice">{blocks.sub_blocks.items.STATUS}</div>
                        # ENDIF #
                        <div class="sm-width-pc-30 cell-gap modal-container align-right" aria-label="{@scm.game.event.details}">
                            <a data-modal="" data-target="target-panel-{blocks.sub_blocks.items.GAME_ID}">
                                # IF blocks.sub_blocks.items.C_HAS_DETAILS #
                                    # IF blocks.sub_blocks.items.C_VIDEO #
                                        <i class="far fa-circle-play"></i>
                                    # ELSE #
                                        <i class="far fa-file-lines"></i>
                                    # ENDIF #
                                # ENDIF #
                            </a>
                            <div id="target-panel-{blocks.sub_blocks.items.GAME_ID}" class="modal modal-animation">
                                <div class="close-modal" aria-label="{@common.close}"></div>
                                <div class="content-panel">
                                    <div class="align-right"><a href="#" class="error big hide-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></a></div>
                                    <div class="cell-flex cell-columns-2 cell-tile">
                                        <div class="home-team">
                                            <div class="cell-header flex-team">
                                                <h4 class="cell-name">
                                                    <a href="{blocks.sub_blocks.items.U_HOME_CLUB}" class="offload">{blocks.sub_blocks.items.HOME_TEAM}</a>
                                                </h4>
                                                # IF blocks.sub_blocks.items.C_HAS_HOME_LOGO #<img src="{blocks.sub_blocks.items.HOME_LOGO}" alt="{blocks.sub_blocks.items.HOME_TEAM}"># ENDIF #
                                            </div>
                                            <div class="cell-score bigger align-center">
                                                {blocks.sub_blocks.items.HOME_SCORE}
                                            </div>
                                            <div class="cell-details">{@scm.game.event.goals}</div>
                                            # START blocks.sub_blocks.items.home_goals #
                                                <div class="cell-infos">
                                                    <span>{blocks.sub_blocks.items.home_goals.PLAYER}</span>
                                                    <span>{blocks.sub_blocks.items.home_goals.TIME}'</span>
                                                </div>
                                            # END blocks.sub_blocks.items.home_goals #
                                            <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                            # START blocks.sub_blocks.items.home_yellow #
                                                <div class="cell-infos">
                                                    <span>{blocks.sub_blocks.items.home_yellow.PLAYER}</span>
                                                    <span>{blocks.sub_blocks.items.home_yellow.TIME}'</span>
                                                </div>
                                            # END blocks.sub_blocks.items.home_yellow #
                                            <div class="cell-details">{@scm.game.event.cards.red}</div>
                                            # START blocks.sub_blocks.items.home_red #
                                                <div class="cell-infos">
                                                    <span>{blocks.sub_blocks.items.home_red.PLAYER}</span>
                                                    <span>{blocks.sub_blocks.items.home_red.TIME}'</span>
                                                </div>
                                            # END blocks.sub_blocks.items.home_red #
                                        </div>
                                        <div class="away-team">
                                            <div class="cell-header flex-team">
                                                <h4 class="cell-name">
                                                    <a href="{blocks.sub_blocks.items.U_AWAY_CLUB}" class="offload">{blocks.sub_blocks.items.AWAY_TEAM}</a>
                                                </h4>
                                                # IF blocks.sub_blocks.items.C_HAS_AWAY_LOGO #<img src="{blocks.sub_blocks.items.AWAY_LOGO}" alt="{blocks.sub_blocks.items.AWAY_TEAM}"># ENDIF #
                                            </div>
                                            <div class="cell-score bigger align-center">
                                                {blocks.sub_blocks.items.AWAY_SCORE}
                                            </div>
                                            <div class="cell-details">{@scm.game.event.goals}</div>
                                            # START blocks.sub_blocks.items.away_goals #
                                                <div class="cell-infos">
                                                    <span>{blocks.sub_blocks.items.away_goals.PLAYER}</span>
                                                    <span>{blocks.sub_blocks.items.away_goals.TIME}'</span>
                                                </div>
                                            # END blocks.sub_blocks.items.away_goals #
                                            <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                            # START blocks.sub_blocks.items.away_yellow #
                                                <div class="cell-infos">
                                                    <span>{blocks.sub_blocks.items.away_yellow.PLAYER}</span>
                                                    <span>{blocks.sub_blocks.items.away_yellow.TIME}'</span>
                                                </div>
                                            # END blocks.sub_blocks.items.away_yellow #
                                            <div class="cell-details">{@scm.game.event.cards.red}</div>
                                            # START blocks.sub_blocks.items.away_red #
                                                <div class="cell-infos">
                                                    <span>{blocks.sub_blocks.items.away_red.PLAYER}</span>
                                                    <span>{blocks.sub_blocks.items.away_red.TIME}'</span>
                                                </div>
                                            # END blocks.sub_blocks.items.away_red #
                                        </div>
                                    </div>
                                    # IF blocks.sub_blocks.items.C_VIDEO #
                                        <a href="{blocks.sub_blocks.items.U_VIDEO}" class="button submit" target="blank" rel="noopener noreferer">
                                            <i class="far fa-circle-play"></i> {@scm.watch.video}
                                        </a>
                                    # ENDIF #
                                    <div class="flex-between flex-between-large">
                                        # IF blocks.sub_blocks.items.STADIUM #
                                            <div class="md-width-pc-50">
                                                <h5>{@scm.game.event.stadium}</h5>
                                                {blocks.sub_blocks.items.STADIUM}
                                            </div>
                                        # ENDIF #
                                        # IF blocks.sub_blocks.items.SUMMARY #
                                            <div class="md-width-pc-50">
                                                <h5>{@scm.game.event.summary}</h5>
                                                {blocks.sub_blocks.items.SUMMARY}
                                            </div>
                                        # ENDIF #
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-between flex-between-large# IF blocks.sub_blocks.items.C_EXEMPT # bgc notice# ENDIF #">
                        <div class="team-{blocks.sub_blocks.items.HOME_ID} flex-between sm-width-pc-100 md-width-pc-50">
                            <div class="game-team home-team cell-pad flex-team flex-right sm-width-pc-80# IF blocks.sub_blocks.items.C_HOME_FAV # text-strong# ENDIF #">
                                <span>
                                    <a
                                        href="{blocks.sub_blocks.items.U_HOME_CALENDAR}"
                                        aria-label="{@scm.club.see.calendar}# IF blocks.sub_blocks.items.HOME_FORFEIT # - {@scm.game.event.forfeit}# ENDIF ## IF blocks.sub_blocks.items.HOME_GENERAL_FORFEIT # - {@scm.game.event.general.forfeit}# ENDIF #"
                                        class="offload# IF blocks.sub_blocks.items.C_HOME_FAV # text-strong# ENDIF ## IF blocks.sub_blocks.items.HOME_FORFEIT # warning# ENDIF ## IF blocks.sub_blocks.items.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #">
                                        {blocks.sub_blocks.items.HOME_TEAM}
                                    </a>
                                </span>
                                # IF blocks.sub_blocks.items.C_HAS_HOME_LOGO #<img src="{blocks.sub_blocks.items.HOME_LOGO}" alt="{blocks.sub_blocks.items.HOME_TEAM}"># ENDIF #
                            </div>
                            <div class="game-score home-score cell-pad sm-width-pc-20">{blocks.sub_blocks.items.HOME_SCORE}# IF blocks.sub_blocks.items.C_HAS_PEN # <span class="small">({blocks.sub_blocks.items.HOME_PEN})</span># ENDIF #</div>
                        </div>
                        <div class="team-{blocks.sub_blocks.items.AWAY_ID} flex-between sm-width-pc-100 md-width-pc-50 invert-team">
                            <div class="game-score away-score cell-pad sm-width-pc-20">{blocks.sub_blocks.items.AWAY_SCORE}# IF blocks.sub_blocks.items.C_HAS_PEN # <span class="small">({blocks.sub_blocks.items.AWAY_PEN})</span># ENDIF #</div>
                            <div class="game-team away-team cell-pad flex-team flex-left sm-width-pc-80# IF blocks.sub_blocks.items.C_AWAY_FAV # text-strong# ENDIF #">
                                # IF blocks.sub_blocks.items.C_HAS_AWAY_LOGO #<img src="{blocks.sub_blocks.items.AWAY_LOGO}" alt="{blocks.sub_blocks.items.AWAY_TEAM}"># ENDIF #
                                <span>
                                    <a
                                        href="{blocks.sub_blocks.items.U_AWAY_CALENDAR}"
                                        aria-label="{@scm.club.see.calendar}# IF blocks.sub_blocks.items.AWAY_FORFEIT # - {@scm.game.event.forfeit}# ENDIF ## IF blocks.sub_blocks.items.AWAY_GENERAL_FORFEIT # - {@scm.game.event.general.forfeit}# ENDIF #"
                                        class="offload# IF blocks.sub_blocks.items.C_AWAY_FAV # text-strong# ENDIF ## IF blocks.sub_blocks.items.AWAY_FORFEIT # warning# ENDIF ## IF blocks.sub_blocks.items.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #">
                                        {blocks.sub_blocks.items.AWAY_TEAM}
                                    </a>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            # END blocks.sub_blocks.items #
        # END blocks.sub_blocks #
    </div>
# END blocks #