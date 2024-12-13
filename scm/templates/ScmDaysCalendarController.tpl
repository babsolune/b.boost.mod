<section id="module-scm" class="several-items modal-container">
    # INCLUDE MENU #
    <article>
        <header><h2>{@scm.days.results} {@scm.day} {DAY}</h2></header>
        <div class="content">
            # IF C_HAS_GAMES #
                <table class="md-width-pc-70 bordered-table m-a">
                    <colgroup class="hidden-small-screens">
                        <col class="md-width-pc-4" />
                        <col class="md-width-pc-40" />
                        <col class="md-width-pc-8" />
                        <col class="md-width-pc-8" />
                        <col class="md-width-pc-40" />
                        <col class="md-width-pc-5" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th aria-label="{@scm.th.hourly}"><i class="far fa-clock"></i></th>
                            <th>{@scm.th.home.team}</th>
                            <th colspan="2">{@scm.th.score}</th>
                            <th>{@scm.th.away.team}</th>
                            <th aria-label="{@scm.th.details}"><i class="fa fa-align-left"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        # START dates #
                            # IF NOT C_ONE_DAY #
                                <tr>
                                    <td colspan="6">{dates.DATE}</td>
                                </tr>
                            # ENDIF #
                            # START dates.games #
                                <tr class="# IF dates.games.C_HAS_SCORE #has-score-color# ENDIF ## IF dates.games.C_EXEMPT #bgc notice# ENDIF #">
                                    <td>{dates.games.GAME_DATE_HOUR_MINUTE}</td>
                                    <td class="# IF dates.games.C_HOME_FAV #text-strong# ENDIF #">
                                        <div class="flex-team flex-right">
                                            <span>
                                                <a
                                                    href="{dates.games.U_HOME_CALENDAR}"
                                                    aria-label="{@scm.see.club.calendar}# IF dates.games.HOME_FORFEIT # - {@scm.game.event.forfeit}# ENDIF ## IF dates.games.HOME_GENERAL_FORFEIT # - {@scm.game.event.general.forfeit}# ENDIF #"
                                                    # IF dates.games.HOME_FORFEIT #data-tooltip-class="warning"# ENDIF #
                                                    # IF dates.games.HOME_GENERAL_FORFEIT #data-tooltip-class="warning"# ENDIF #
                                                    class="offload# IF dates.games.HOME_FORFEIT # warning# ENDIF ## IF dates.games.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #"
                                                >{dates.games.HOME_TEAM}</a>
                                            </span>
                                            # IF dates.games.C_HAS_HOME_LOGO #<img src="{dates.games.HOME_LOGO}" alt="{dates.games.HOME_TEAM}"># ENDIF #
                                        </div>
                                    </td>
                                    # IF dates.games.C_STATUS #
                                        <td colspan="2">{dates.games.STATUS}</td>
                                    # ELSE #
                                        <td>{dates.games.HOME_SCORE}</td>
                                        <td>{dates.games.AWAY_SCORE}</td>
                                    # ENDIF #
                                    <td class="# IF dates.games.C_AWAY_FAV #text-strong# ENDIF #">
                                        <div class="flex-team flex-left">
                                            # IF dates.games.C_HAS_AWAY_LOGO #<img src="{dates.games.AWAY_LOGO}" alt="{dates.games.AWAY_TEAM}"># ENDIF #
                                            <span>
                                                <a
                                                    href="{dates.games.U_AWAY_CALENDAR}"
                                                    aria-label="{@scm.see.club.calendar}# IF dates.games.AWAY_FORFEIT # - {@scm.game.event.forfeit}# ENDIF ## IF dates.games.AWAY_GENERAL_FORFEIT # - {@scm.game.event.general.forfeit}# ENDIF #"
                                                    # IF dates.games.AWAY_FORFEIT #data-tooltip-class="warning"# ENDIF #
                                                    # IF dates.games.AWAY_GENERAL_FORFEIT #data-tooltip-class="warning"# ENDIF #
                                                    class="offload# IF dates.games.AWAY_FORFEIT # warning# ENDIF ## IF dates.games.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #"
                                                >{dates.games.AWAY_TEAM}</a>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span data-modal="" data-target="target-panel-{dates.games.GAME_ID}">
                                            # IF dates.games.C_HAS_DETAILS #
                                                # IF dates.games.C_VIDEO #
                                                    <i class="far fa-circle-play"></i>
                                                # ELSE #
                                                    <i class="far fa-file-lines"></i>
                                                # ENDIF #
                                            # ENDIF #
                                        </span>
                                        <div id="target-panel-{dates.games.GAME_ID}" class="modal modal-animation">
                                            <div class="close-modal" aria-label="{@common.close}"></div>
                                            <div class="content-panel">
                                                <div class="align-right"><a href="#" class="error big hide-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></a></div>
                                                <div class="cell-flex cell-columns-2 cell-tile">
                                                    <div class="home-team cell">
                                                        <div class="cell-header">
                                                            <div class="cell-name">
                                                                <a href="{dates.games.U_HOME_CLUB}" class="offload">{dates.games.HOME_TEAM}</a>
                                                            </div>
                                                            # IF dates.games.C_HAS_HOME_LOGO #<img class="smaller md-width-px-25" src="{dates.games.HOME_LOGO}" alt="{dates.games.HOME_TEAM}"># ENDIF #
                                                        </div>
                                                        <div class="cell-score bigger align-center">
                                                            {dates.games.HOME_SCORE}
                                                        </div>
                                                        <div class="cell-details">{@scm.game.event.goals}</div>
                                                        # START dates.games.home_goals #
                                                            <div class="cell-infos">
                                                                <span>{dates.games.home_goals.PLAYER}</span>
                                                                <span>{dates.games.home_goals.TIME}'</span>
                                                            </div>
                                                        # END dates.games.home_goals #
                                                        <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                        # START dates.games.home_yellow #
                                                            <div class="cell-infos">
                                                                <span>{dates.games.home_yellow.PLAYER}</span>
                                                                <span>{dates.games.home_yellow.TIME}'</span>
                                                            </div>
                                                        # END dates.games.home_yellow #
                                                        <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                        # START dates.games.home_red #
                                                            <div class="cell-infos">
                                                                <span>{dates.games.home_red.PLAYER}</span>
                                                                <span>{dates.games.home_red.TIME}'</span>
                                                            </div>
                                                        # END dates.games.home_red #
                                                    </div>
                                                    <div class="away-team cell">
                                                        <div class="cell-header">
                                                            <div class="cell-name">
                                                                <a href="{dates.games.U_AWAY_CLUB}" class="offload">{dates.games.AWAY_TEAM}</a>
                                                            </div>
                                                            # IF dates.games.C_HAS_AWAY_LOGO #<img class="smaller md-width-px-25" src="{dates.games.AWAY_LOGO}" alt="{dates.games.AWAY_TEAM}"># ENDIF #
                                                        </div>
                                                        <div class="cell-score bigger align-center">
                                                            {dates.games.AWAY_SCORE}
                                                        </div>
                                                        <div class="cell-details">{@scm.game.event.goals}</div>
                                                        # START dates.games.away_goals #
                                                            <div class="cell-infos">
                                                                <span>{dates.games.away_goals.PLAYER}</span>
                                                                <span>{dates.games.away_goals.TIME}'</span>
                                                            </div>
                                                        # END dates.games.away_goals #
                                                        <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                        # START dates.games.away_yellow #
                                                            <div class="cell-infos">
                                                                <span>{dates.games.away_yellow.PLAYER}</span>
                                                                <span>{dates.games.away_yellow.TIME}'</span>
                                                            </div>
                                                        # END dates.games.away_yellow #
                                                        <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                        # START dates.games.away_red #
                                                            <div class="cell-infos">
                                                                <span>{dates.games.away_red.PLAYER}</span>
                                                                <span>{dates.games.away_red.TIME}'</span>
                                                            </div>
                                                        # END dates.games.away_red #
                                                    </div>
                                                </div>
                                                # IF dates.games.C_VIDEO #
                                                    <a href="{dates.games.U_VIDEO}" class="button d-block align-center" target="blank" rel="noopener noreferer">
                                                        <i class="far fa-circle-play"></i> {@scm.watch.video}
                                                    </a>
                                                # ENDIF #
                                                # IF dates.games.SUMMARY #
                                                    {dates.games.SUMMARY}
                                                # ENDIF #
                                                # IF dates.games.STADIUM #
                                                    <div class="md-width-pc-50 m-a">{dates.games.STADIUM}</div>
                                                # ENDIF #
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            # END dates.games #
                        # END dates #
                    </tbody>
                </table>
            # ELSE #
                <div class="message-helper bgc notice">{@scm.message.no.games}</div>
            # ENDIF #
        </div>
    </article>
    <footer></footer>
</section>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>