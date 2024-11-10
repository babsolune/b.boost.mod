<section id="module-scm" class="several-items">
    # INCLUDE MENU #
    <article>
        <header>
            <h2>{@scm.days.ranking}</h2>
            <div class="flex-between">
                <nav class="roundmenu">
                    <ul>
                        <li><a href="{U_GENERAL}" class="roundmenu-title">{@scm.days.ranking.general}</a></li>
                        <li><a href="{U_HOME}" class="roundmenu-title">{@scm.days.ranking.home}</a></li>
                        <li><a href="{U_AWAY}" class="roundmenu-title">{@scm.days.ranking.away}</a></li>
                        <li><a href="{U_ATTACK}" class="roundmenu-title">{@scm.days.ranking.attack}</a></li>
                        <li><a href="{U_DEFENSE}" class="roundmenu-title">{@scm.days.ranking.defense}</a></li>
                    </ul>
                </nav>
                <nav class="roundmenu">
                    <ul>
                        <li><a href="{U_GENERAL_DAYS}" class="roundmenu-title">{@scm.days.ranking.days}</a> </li>
                    </ul>
                </nav>
            </div>
            # IF C_GENERAL_DAYS #
                <div>
                    <nav class="roundmenu general-days">
                        <ul>
                            # START days #
                                <li><a href="{days.U_DAY}" class="roundmenu-title# IF NOT days.C_DAY_PLAYED # not-played bgc error# ENDIF #">{days.DAY}</a></li>
                            # END days #
                        </ul>
                    </nav>
                </div>
            # ENDIF #
        </header>
        <div class="content">
            # IF C_HAS_GAMES #
                <div class="responsive-table">
                    <table class="bordered-table width-pc-70 m-a">
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
                            # IF C_BONUS #
                                <col class="width-pc-05" />
                                <col class="width-pc-05" />
                            # ENDIF #
                        </colgroup>
                        <thead>
                            <tr>
                                <th aria-label="{@scm.th.rank.short}">{@scm.th.rank.short}</th>
                                <th>{@scm.th.team}</th>
                                <th aria-label="{@scm.th.points}">{@scm.th.points.short}</th>
                                <th aria-label="{@scm.th.played}">{@scm.th.played.short}</th>
                                <th aria-label="{@scm.th.win}">{@scm.th.win.short}</th>
                                <th aria-label="{@scm.th.draw}">{@scm.th.draw.short}</th>
                                <th aria-label="{@scm.th.loss}">{@scm.th.loss.short}</th>
                                <th aria-label="{@scm.th.goals.for}">{@scm.th.goals.for.short}</th>
                                <th aria-label="{@scm.th.goals.against}">{@scm.th.goals.against.short}</th>
                                <th aria-label="{@scm.th.goal.average}">{@scm.th.goal.average.short}</th>
                                # IF C_BONUS_SINGLE #
                                    <th aria-label="{@scm.th.bonus}">{@scm.th.off.bonus.short}</th>
                                # ENDIF #
                                # IF C_BONUS_DOUBLE #
                                    <th aria-label="{@scm.th.off.bonus}">{@scm.th.off.bonus.short}</th>
                                    <th aria-label="{@scm.th.def.bonus}">{@scm.th.def.bonus.short}</th>
                                # ENDIF #
                            </tr>
                        </thead>
                        <tbody>
                            # START ranks #
                                <tr class="ranking-color# IF ranks.C_FAV # fav-team# ENDIF #" style="background-color: {ranks.RANK_COLOR}">
                                    <td>{ranks.RANK}</td>
                                    <td class="align-left">
                                        <div class="flex-team flex-left">
                                            <img src="{ranks.TEAM_LOGO}" alt="{ranks.TEAM_NAME}">
                                            <span>
                                                <a href="{ranks.U_TEAM_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{ranks.TEAM_NAME}</a>
                                                # IF ranks.C_FORFEIT #<span class="smaller text-italic warning">({@scm.params.status.forfeit})</span># ENDIF #
                                            </span>
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
                                    # IF C_BONUS_SINGLE #
                                        <td>{ranks.OFF_BONUS}</td>
                                    # ENDIF #
                                    # IF C_BONUS_DOUBLE #
                                        <td>{ranks.OFF_BONUS}</td>
                                        <td>{ranks.DEF_BONUS}</td>
                                    # ENDIF #
                                </tr>
                            # END ranks #
                        </tbody>
                    </table>
                </div>

                <div class="cell-flex cell-columns-2">
                    <div class="responsive-table">
                        <table class="bordered-table">
                            <caption>{@scm.day} {LAST_DAY}</caption>
                            <colgroup class="hidden-small-screens">
                                <col class="width-pc-05" />
                                <col class="width-pc-# IF C_ONE_DAY #40# ELSE #35# ENDIF #" />
                                <col class="width-pc-8" />
                                <col class="width-pc-8" />
                                <col class="width-pc-# IF C_ONE_DAY #40# ELSE #35# ENDIF #" />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th aria-label="{@scm.th.hourly}"><i class="far fa-clock"></i></th>
                                    <th>{@scm.th.home.team}</th>
                                    <th colspan="2">{@scm.th.score}</th>
                                    <th>{@scm.th.away.team}</th>
                                </tr>
                            </thead>
                            <tbody>
                                # START prev_dates #
                                    <tr>
                                        <td colspan="5">{prev_dates.DATE}</td>
                                    </tr>
                                    # START prev_dates.prev_days #
                                        <tr# IF prev_dates.prev_days.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                            <td class="small">{prev_dates.prev_days.GAME_DATE_HOUR_MINUTE}</td>
                                            <td class="align-right home-{prev_dates.prev_days.ID}# IF prev_dates.prev_days.C_HOME_FAV # text-strong# ENDIF #">
                                                <div class="flex-team flex-right">
                                                    <span><a href="{prev_dates.prev_days.U_HOME_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{prev_dates.prev_days.HOME_TEAM}</a></span>
                                                    <img src="{prev_dates.prev_days.HOME_LOGO}" alt="{prev_dates.prev_days.HOME_TEAM}">
                                                </div>
                                            </td>
                                            # IF prev_dates.prev_days.C_STATUS #
                                                <td colspan="2">{prev_dates.prev_days.STATUS}</td>
                                            # ELSE #
                                                <td>{prev_dates.prev_days.HOME_SCORE}</td>
                                                <td>{prev_dates.prev_days.AWAY_SCORE}</td>
                                            # ENDIF #
                                            <td class="align-left away-{prev_dates.prev_days.ID}# IF prev_dates.prev_days.C_AWAY_FAV # text-strong# ENDIF #">
                                                <div class="flex-team flex-left">
                                                    <img src="{prev_dates.prev_days.AWAY_LOGO}" alt="{prev_dates.prev_days.AWAY_TEAM}">
                                                    <span><a href="{prev_dates.prev_days.U_AWAY_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{prev_dates.prev_days.AWAY_TEAM}</a></span>
                                                </div>
                                            </td>
                                        </tr>
                                    # END prev_dates.prev_days #
                                # END prev_dates #
                            </tbody>
                        </table>
                    </div>
                    # IF C_EVENT_ENDING #
                        <div></div>
                    # ELSE #
                        <div class="responsive-table">
                            <table class="bordered-table">
                                <caption>{@scm.day} {NEXT_DAY}</caption>
                                <colgroup class="hidden-small-screens">
                                    <col class="width-pc-05" />
                                    <col class="width-pc-# IF C_ONE_DAY #40# ELSE #35# ENDIF #" />
                                    <col class="width-pc-08" />
                                    <col class="width-pc-08" />
                                    <col class="width-pc-# IF C_ONE_DAY #40# ELSE #35# ENDIF #" />
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th aria-label="{@scm.th.hourly}"><i class="far fa-clock"></i></th>
                                        <th>{@scm.th.home.team}</th>
                                        <th colspan="2">{@scm.th.score}</th>
                                        <th>{@scm.th.away.team}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    # START next_dates #
                                        <tr>
                                            <td colspan="5">{next_dates.DATE}</td>
                                        </tr>
                                        # START next_dates.next_days #
                                            <tr# IF next_dates.next_days.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                                <td class="small">{next_dates.next_days.GAME_DATE_HOUR_MINUTE}</td>
                                                <td class="align-right home-{next_dates.next_days.ID}# IF next_dates.next_days.C_HOME_FAV # text-strong# ENDIF #">
                                                    <div class="flex-team flex-right">
                                                        <span><a href="{next_dates.next_days.U_HOME_CALENDAR}" class="offload">{next_dates.next_days.HOME_TEAM}</a></span>
                                                        <img src="{next_dates.next_days.HOME_LOGO}" alt="{next_dates.next_days.HOME_TEAM}">
                                                    </div>
                                                </td>
                                                # IF next_dates.next_days.C_STATUS #
                                                    <td colspan="2">{next_dates.next_days.STATUS}</td>
                                                # ELSE #
                                                    <td>{next_dates.next_days.HOME_SCORE}</td>
                                                    <td>{next_dates.next_days.AWAY_SCORE}</td>
                                                # ENDIF #
                                                <td class="align-left away-{next_dates.next_days.ID}# IF next_dates.next_days.C_AWAY_FAV # text-strong# ENDIF #">
                                                    <div class="flex-team flex-LEFT">
                                                        <img src="{next_dates.next_days.AWAY_LOGO}" alt="{next_dates.next_days.AWAY_TEAM}">
                                                        <span><a href="{next_dates.next_days.U_AWAY_CALENDAR}" class="offload">{next_dates.next_days.AWAY_TEAM}</a></span>
                                                    </div>
                                                </td>
                                            </tr>
                                        # END next_dates.next_days #
                                    # END next_dates #
                                </tbody>
                            </table>
                        </div>
                    # ENDIF #
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