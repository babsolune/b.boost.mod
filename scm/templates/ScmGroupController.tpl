<section id="module-scm" class="several-items modal-container">
	# INCLUDE MENU #
    <h2># IF C_HAT_RANKING #{@scm.day} {DAY}# ELSE #{@scm.group} {GROUP}# ENDIF #</h2>
    # IF C_HAS_GAMES #
        <div class="cell-flex cell-columns-2">
            <div class="responsive-table">
                <table class="bordered-table">
                    <colgroup class="hidden-small-screens">
                        <col class="width-pc-4" />
                        <col class="width-pc-40" />
                        <col class="width-pc-8" />
                        <col class="width-pc-8" />
                        <col class="width-pc-40" />
                        # IF C_DISPLAY_PLAYGROUNDS #<col class="width-pc-10" /># ENDIF #
                        <col class="width-pc-5" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th aria-label="{@scm.th.hourly}"><i class="far fa-clock"></i></th>
                            <th>{@scm.th.home.team}</th>
                            <th colspan="2">{@scm.th.score}</th>
                            <th>{@scm.th.away.team}</th>
                            # IF C_DISPLAY_PLAYGROUNDS #<th>{@scm.th.playground}</th># ENDIF #
                            <th aria-label="{@scm.th.details}"><i class="fa fa-align-left"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                    # START matchdays #
                        # IF NOT C_HAT_RANKING #
                            <tr>
                                <td colspan="# IF C_DISPLAY_PLAYGROUNDS #7# ELSE #6# ENDIF #"># IF C_ONE_DAY #{@scm.round}# ELSE #{@scm.day}# ENDIF # {matchdays.MATCHDAY}</td>
                            </tr>
                        # ENDIF #
                        # START matchdays.dates #
                            # IF NOT C_ONE_DAY #
                                <tr>
                                    <td colspan="# IF C_DISPLAY_PLAYGROUNDS #7# ELSE #6# ENDIF #">{matchdays.dates.DATE}</td>
                                </tr>
                            # ENDIF #
                            # START matchdays.dates.games #
                                <tr# IF matchdays.dates.games.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                    <td>{matchdays.dates.games.GAME_DATE_HOUR_MINUTE}</td>
                                    <td class="align-right# IF matchdays.dates.games.C_HOME_FAV # text-strong# ENDIF #">
                                        <div class="flex-team flex-right">
                                            <span><a href="{matchdays.dates.games.U_HOME_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{matchdays.dates.games.HOME_TEAM}</a></span>
                                            # IF matchdays.dates.games.C_HAS_HOME_LOGO #<img src="{matchdays.dates.games.HOME_LOGO}" alt="{matchdays.dates.games.HOME_TEAM}"># ENDIF #
                                        </div>
                                    </td>
                                    # IF matchdays.dates.games.C_STATUS #
                                        <td colspan="2">{matchdays.dates.games.STATUS}</td>
                                    # ELSE #
                                        <td>{matchdays.dates.games.HOME_SCORE}</td>
                                        <td>{matchdays.dates.games.AWAY_SCORE}</td>
                                    # ENDIF #
                                    <td class="align-left# IF matchdays.dates.games.C_AWAY_FAV # text-strong# ENDIF #">
                                        <div class="flex-team flex-left">
                                            # IF matchdays.dates.games.C_HAS_AWAY_LOGO #<img src="{matchdays.dates.games.AWAY_LOGO}" alt="{matchdays.dates.games.AWAY_TEAM}"># ENDIF #
                                            <span><a href="{matchdays.dates.games.U_AWAY_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{matchdays.dates.games.AWAY_TEAM}</a></span>
                                        </div>
                                    </td>
                                    # IF C_DISPLAY_PLAYGROUNDS #<td>{matchdays.dates.games.PLAYGROUND}</td># ENDIF #
                                    <td>
                                        <span data-modal="" data-target="target-panel-{matchdays.dates.games.GAME_ID}">
                                            # IF matchdays.dates.games.C_HAS_DETAILS #
                                                # IF matchdays.dates.games.C_VIDEO #
                                                    <i class="far fa-circle-play"></i>
                                                # ELSE #
                                                    <i class="far fa-file-lines"></i>
                                                # ENDIF #
                                            # ENDIF #
                                        </span>
                                        <div id="target-panel-{matchdays.dates.games.GAME_ID}" class="modal modal-animation">
                                            <div class="close-modal" aria-label="{@common.close}"></div>
                                            <div class="content-panel">
                                                <div class="align-right"><a href="#" class="error big hide-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></a></div>
                                                <div class="cell-flex cell-columns-2 cell-tile">
                                                    <div class="home-team cell">
                                                        <div class="cell-header">
                                                            <div class="cell-name">
                                                                <a href="{matchdays.dates.games.U_HOME_CLUB}" class="offload">{matchdays.dates.games.HOME_TEAM}</a>
                                                            </div>
                                                            # IF matchdays.dates.games.C_HAS_HOME_LOGO #<img class="smaller width-px-25" src="{matchdays.dates.games.HOME_LOGO}" alt="{matchdays.dates.games.HOME_TEAM}"># ENDIF #
                                                        </div>
                                                        <div class="cell-score bigger align-center">
                                                            {matchdays.dates.games.HOME_SCORE}
                                                        </div>
                                                        <div class="cell-details">{@scm.event.goals}</div>
                                                        # START matchdays.dates.games.home_goals #
                                                            <div class="cell-infos">
                                                                <span>{matchdays.dates.games.home_goals.PLAYER}</span>
                                                                <span>{matchdays.dates.games.home_goals.TIME}'</span>
                                                            </div>
                                                        # END matchdays.dates.games.home_goals #
                                                        <div class="cell-details">{@scm.event.yellow.cards}</div>
                                                        # START matchdays.dates.games.home_yellow #
                                                            <div class="cell-infos">
                                                                <span>{matchdays.dates.games.home_yellow.PLAYER}</span>
                                                                <span>{matchdays.dates.games.home_yellow.TIME}'</span>
                                                            </div>
                                                        # END matchdays.dates.games.home_yellow #
                                                        <div class="cell-details">{@scm.event.red.cards}</div>
                                                        # START matchdays.dates.games.home_red #
                                                            <div class="cell-infos">
                                                                <span>{matchdays.dates.games.home_red.PLAYER}</span>
                                                                <span>{matchdays.dates.games.home_red.TIME}'</span>
                                                            </div>
                                                        # END matchdays.dates.games.home_red #
                                                    </div>
                                                    <div class="away-team cell">
                                                        <div class="cell-header">
                                                            <div class="cell-name"><a href="{matchdays.dates.games.U_AWAY_CLUB}" class="offload">{matchdays.dates.games.AWAY_TEAM}</a></div>
                                                            # IF matchdays.dates.games.C_HAS_AWAY_LOGO #<img class="smaller width-px-25" src="{matchdays.dates.games.AWAY_LOGO}" alt="{matchdays.dates.games.AWAY_TEAM}"># ENDIF #
                                                        </div>
                                                        <div class="cell-score bigger align-center">
                                                            {matchdays.dates.games.AWAY_SCORE}
                                                        </div>
                                                        <div class="cell-details">{@scm.event.goals}</div>
                                                        # START matchdays.dates.games.away_goals #
                                                            <div class="cell-infos">
                                                                <span>{matchdays.dates.games.away_goals.PLAYER}</span>
                                                                <span>{matchdays.dates.games.away_goals.TIME}'</span>
                                                            </div>
                                                        # END matchdays.dates.games.away_goals #
                                                        <div class="cell-details">{@scm.event.yellow.cards}</div>
                                                        # START matchdays.dates.games.away_yellow #
                                                            <div class="cell-infos">
                                                                <span>{matchdays.dates.games.away_yellow.PLAYER}</span>
                                                                <span>{matchdays.dates.games.away_yellow.TIME}'</span>
                                                            </div>
                                                        # END matchdays.dates.games.away_yellow #
                                                        <div class="cell-details">{@scm.event.red.cards}</div>
                                                        # START matchdays.dates.games.away_red #
                                                            <div class="cell-infos">
                                                                <span>{matchdays.dates.games.away_red.PLAYER}</span>
                                                                <span>{matchdays.dates.games.away_red.TIME}'</span>
                                                            </div>
                                                        # END matchdays.dates.games.away_red #
                                                    </div>
                                                </div>
                                                # IF matchdays.dates.games.C_VIDEO #
                                                    <a href="{matchdays.dates.games.U_VIDEO}" class="button d-block align-center" target="blank" rel="noopener noreferer">
                                                        <i class="far fa-circle-play"></i> {@scm.watch.video}
                                                    </a>
                                                # ENDIF #
                                                # IF matchdays.dates.games.SUMMARY #
                                                    {matchdays.dates.games.SUMMARY}
                                                # ENDIF #
                                                # IF matchdays.dates.games.STADIUM #
                                                    <div class="width-pc-50 m-a">{matchdays.dates.games.STADIUM}</div>
                                                # ENDIF #
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            # END matchdays.dates.games #
                        # END matchdays.dates #
                    # END matchdays #
                    </tbody>
                </table>
            </div>
            <div class="responsive-table">
                <table class="bordered-table">
                    <colgroup class="hidden-small-screens">
                        <col class="width-pc-05" />
                        <col class="width-pc-60" />
                        <col class="width-pc-05" />
                        <col class="width-pc-05" />
                        <col class="width-pc-05" />
                        <col class="width-pc-05" />
                        <col class="width-pc-05" />
                        <col class="width-pc-05" />
                        <col class="width-pc-05" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>{@scm.th.rank.short}</th>
                            <th>{@scm.th.team}</th>
                            <th>{@scm.th.points.short}</th>
                            <th>{@scm.th.played.short}</th>
                            <th>{@scm.th.win.short}</th>
                            <th>{@scm.th.draw.short}</th>
                            <th>{@scm.th.loss.short}</th>
                            <th>{@scm.th.goals.for.short}</th>
                            <th>{@scm.th.goals.against.short}</th>
                            <th>{@scm.th.goal.average.short}</th>
                        </tr>
                    </thead>
                    <tbody>
                        # START ranks #
                            <tr class="ranking-color# IF ranks.C_FAV # fav-team# ENDIF #" style="background-color: {ranks.RANK_COLOR}">
                                <td>{ranks.RANK}</td>
                                <td class="">
                                    <div class="flex-team flex-left">
                                        <img src="{ranks.TEAM_LOGO}" alt="{ranks.TEAM_NAME}">
                                        <span>{ranks.TEAM_NAME}</span>
                                    </div>
                                </td>
                                <td>{ranks.POINTS}</td>
                                <td>{ranks.PLAYED}</td>
                                <td>{ranks.WIN}</td>
                                <td>{ranks.DRAW}</td>
                                <td>{ranks.LOSS}</td>
                                <td>{ranks.GOALS_FOR}</td>
                                <td>{ranks.GOALS_AGAINST}</td>
                                <td>{ranks.GOAL_AVERAGE}</td>
                            </tr>
                        # END ranks #
                    </tbody>
                </table>
            </div>
        </div>
    # ELSE #
        <div class="message-helper bgc notice">{@scm.message.no.games}</div>
    # ENDIF #
    <footer></footer>
</section>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>