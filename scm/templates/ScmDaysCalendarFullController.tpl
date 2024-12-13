<section id="module-scm" class="several-items modal-container">
    # INCLUDE MENU #
    <article>
        <header><h2>{@scm.calendar}</h2></header>
        <div class="content">
            # IF C_HAS_GAMES #
                <div class="cell-flex cell-columns-2">
                    # START days #
                        <div>
                            <h3>{@scm.day} {days.DAY}</h3>
                            <div class="responsive-table">
                                <table class="bordered-table">
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
                                        # START days.dates #
                                            # IF NOT C_ONE_DAY #
                                                <tr>
                                                    <td colspan="6">{days.dates.DATE}</td>
                                                </tr>
                                            # ENDIF #
                                            # START days.dates.items #
                                                <tr class="# IF days.dates.items.C_HAS_SCORE #has-score-color# ENDIF ## IF days.dates.items.C_EXEMPT #bgc notice# ENDIF #">
                                                    <td>{days.dates.items.GAME_DATE_HOUR_MINUTE}</td>
                                                    <td class="# IF days.dates.items.C_HOME_FAV #text-strong# ENDIF #">
                                                        <div class="flex-team flex-right">
                                                            <span>
                                                                <a
                                                                    href="{days.dates.items.U_HOME_CALENDAR}"
                                                                    aria-label="{@scm.see.club.calendar}"
                                                                    class="offload# IF days.dates.items.HOME_FORFEIT # warning# ENDIF ## IF days.dates.items.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #"
                                                                >{days.dates.items.HOME_TEAM}</a>
                                                            </span>
                                                            # IF days.dates.items.C_HAS_HOME_LOGO #<img src="{days.dates.items.HOME_LOGO}" alt="{days.dates.items.HOME_TEAM}"># ENDIF #
                                                        </div>
                                                    </td>
                                                    # IF days.dates.items.C_STATUS #
                                                        <td colspan="2">{days.dates.items.STATUS}</td>
                                                    # ELSE #
                                                        <td>{days.dates.items.HOME_SCORE}</td>
                                                        <td>{days.dates.items.AWAY_SCORE}</td>
                                                    # ENDIF #
                                                    <td class="# IF days.dates.items.C_AWAY_FAV #text-strong# ENDIF #">
                                                        <div class="flex-team flex-left">
                                                            # IF days.dates.items.C_HAS_AWAY_LOGO #<img src="{days.dates.items.AWAY_LOGO}" alt="{days.dates.items.AWAY_TEAM}"># ENDIF #
                                                            <span>
                                                                <a
                                                                    href="{days.dates.items.U_AWAY_CALENDAR}"
                                                                    aria-label="{@scm.see.club.calendar}"
                                                                    class="offload# IF days.dates.items.AWAY_FORFEIT # warning# ENDIF ## IF days.dates.items.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #"
                                                                >{days.dates.items.AWAY_TEAM}</a>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span data-modal="" data-target="target-panel-{days.dates.items.GAME_ID}">
                                                            # IF days.dates.items.C_HAS_DETAILS #
                                                                # IF days.dates.items.C_VIDEO #
                                                                    <i class="far fa-circle-play"></i>
                                                                # ELSE #
                                                                    <i class="far fa-file-lines"></i>
                                                                # ENDIF #
                                                            # ENDIF #
                                                        </span>
                                                        <div id="target-panel-{days.dates.items.GAME_ID}" class="modal modal-animation">
                                                            <div class="close-modal" aria-label="{@common.close}"></div>
                                                            <div class="content-panel">
                                                                <div class="align-right"><a href="#" class="error big hide-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></a></div>
                                                                <div class="cell-flex cell-columns-2 cell-tile">
                                                                    <div class="home-team cell">
                                                                        <div class="cell-header">
                                                                            <div class="cell-name">
                                                                                <a href="{days.dates.items.U_HOME_CLUB}" class="offload">{days.dates.items.HOME_TEAM}</a>
                                                                            </div>
                                                                            # IF days.dates.items.C_HAS_HOME_LOGO #<img class="smaller md-width-px-25" src="{days.dates.items.HOME_LOGO}" alt="{days.dates.items.HOME_TEAM}"># ENDIF #
                                                                        </div>
                                                                        <div class="cell-score bigger align-center">
                                                                            {days.dates.items.HOME_SCORE}
                                                                        </div>
                                                                        <div class="cell-details">{@scm.game.event.goals}</div>
                                                                        # START days.dates.items.home_goals #
                                                                            <div class="cell-infos">
                                                                                <span>{days.dates.items.home_goals.PLAYER}</span>
                                                                                <span>{days.dates.items.home_goals.TIME}'</span>
                                                                            </div>
                                                                        # END days.dates.items.home_goals #
                                                                        <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                                        # START days.dates.items.home_yellow #
                                                                            <div class="cell-infos">
                                                                                <span>{days.dates.items.home_yellow.PLAYER}</span>
                                                                                <span>{days.dates.items.home_yellow.TIME}'</span>
                                                                            </div>
                                                                        # END days.dates.items.home_yellow #
                                                                        <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                                        # START days.dates.items.home_red #
                                                                            <div class="cell-infos">
                                                                                <span>{days.dates.items.home_red.PLAYER}</span>
                                                                                <span>{days.dates.items.home_red.TIME}'</span>
                                                                            </div>
                                                                        # END days.dates.items.home_red #
                                                                    </div>
                                                                    <div class="away-team cell">
                                                                        <div class="cell-header">
                                                                            <div class="cell-name">
                                                                                <a href="{days.dates.items.U_AWAY_CLUB}" class="offload">{days.dates.items.AWAY_TEAM}</a>
                                                                            </div>
                                                                            # IF days.dates.items.C_HAS_AWAY_LOGO #<img class="smaller md-width-px-25" src="{days.dates.items.AWAY_LOGO}" alt="{days.dates.items.AWAY_TEAM}"># ENDIF #
                                                                        </div>
                                                                        <div class="cell-score bigger align-center">
                                                                            {days.dates.items.AWAY_SCORE}
                                                                        </div>
                                                                        <div class="cell-details">{@scm.game.event.goals}</div>
                                                                        # START days.dates.items.away_goals #
                                                                            <div class="cell-infos">
                                                                                <span>{days.dates.items.away_goals.PLAYER}</span>
                                                                                <span>{days.dates.items.away_goals.TIME}'</span>
                                                                            </div>
                                                                        # END days.dates.items.away_goals #
                                                                        <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                                        # START days.dates.items.away_yellow #
                                                                            <div class="cell-infos">
                                                                                <span>{days.dates.items.away_yellow.PLAYER}</span>
                                                                                <span>{days.dates.items.away_yellow.TIME}'</span>
                                                                            </div>
                                                                        # END days.dates.items.away_yellow #
                                                                        <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                                        # START days.dates.items.away_red #
                                                                            <div class="cell-infos">
                                                                                <span>{days.dates.items.away_red.PLAYER}</span>
                                                                                <span>{days.dates.items.away_red.TIME}'</span>
                                                                            </div>
                                                                        # END days.dates.items.away_red #
                                                                    </div>
                                                                </div>
                                                                # IF days.dates.items.C_VIDEO #
                                                                    <a href="{days.dates.items.U_VIDEO}" class="button d-block align-center" target="blank" rel="noopener noreferer">
                                                                        <i class="far fa-circle-play"></i> {@scm.watch.video}
                                                                    </a>
                                                                # ENDIF #
                                                                # IF days.dates.items.SUMMARY #
                                                                    {days.dates.items.SUMMARY}
                                                                # ENDIF #
                                                                # IF days.dates.items.STADIUM #
                                                                    <div class="md-width-pc-50 m-a">{days.dates.items.STADIUM}</div>
                                                                # ENDIF #
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            # END days.dates.items #
                                        # END days.dates #
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    # END days #
                </div>
            # ELSE #
                <div class="message-helper bgc notice">{@scm.message.no.games}</div>
            # ENDIF #
        </div>
    </article>
    <footer></footer>
</section>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>