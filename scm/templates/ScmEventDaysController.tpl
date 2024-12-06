<div>
    <h3>{@scm.clubs.list}</h3>
    <div class="columns-6">
        # START clubs_list #
            <a href="{clubs_list.U_CLUB}" class="offload align-center pinned link-color d-block" aria-label="{@scm.see.club}">{clubs_list.CLUB_SHORT_NAME}</a>
        # END clubs_list #
    </div>
</div>

<div class="cell-flex cell-columns-2">
    <div class="days-calendar">
        # IF C_EVENT_STARTING #
            <div class="message-helper bgc notice m-t">{L_STARTING_DATE}</div>
        # ELSE #
            <div class="responsive-table">
                <table class="bordered-table">
                    <caption>{@scm.day} {LAST_DAY}</caption>
                    <colgroup class="hidden-small-screens">
                        <col class="width-pc-6" />
                        <col class="width-pc-39" />
                        <col class="width-pc-8" />
                        <col class="width-pc-8" />
                        <col class="width-pc-39" />
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
                            <tr><td colspan="5">{prev_dates.DATE}</td></tr>
                            # START prev_dates.prev_days #
                                <tr class="# IF prev_dates.prev_days.C_HAS_SCORE #has-score-color# ENDIF ## IF prev_dates.prev_days.C_EXEMPT #bgc notice# ENDIF #">
                                    <td class="small">{prev_dates.prev_days.GAME_DATE_HOUR_MINUTE}</td>
                                    <td class="align-right home-{prev_dates.prev_days.ID}# IF prev_dates.prev_days.C_HOME_FAV # text-strong# ENDIF #">
                                        <div class="flex-team flex-right">
                                            <span>
                                                <a
                                                    href="{prev_dates.prev_days.U_HOME_CALENDAR}"
                                                    aria-label="{@scm.see.club.calendar}"
                                                    class="offload# IF prev_dates.prev_days.HOME_FORFEIT # warning# ENDIF ## IF prev_dates.prev_days.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #"
                                                >{prev_dates.prev_days.HOME_TEAM}</a>
                                            </span>
                                            # IF prev_dates.prev_days.C_HAS_HOME_LOGO #<img src="{prev_dates.prev_days.HOME_LOGO}" alt="{prev_dates.prev_days.HOME_TEAM}"># ENDIF #
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
                                            # IF prev_dates.prev_days.C_HAS_AWAY_LOGO #<img src="{prev_dates.prev_days.AWAY_LOGO}" alt="{prev_dates.prev_days.AWAY_TEAM}"># ENDIF #
                                            <span>
                                                <a
                                                    href="{prev_dates.prev_days.U_AWAY_CALENDAR}"
                                                    aria-label="{@scm.see.club.calendar}"
                                                    class="offload# IF prev_dates.prev_days.AWAY_FORFEIT # warning# ENDIF ## IF prev_dates.prev_days.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #"
                                                >{prev_dates.prev_days.AWAY_TEAM}</a>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            # END prev_dates.prev_days #
                        # END prev_dates #
                    </tbody>
                </table>
            </div>
        # ENDIF #
        # IF C_EVENT_ENDING #
            <div class="message-helper bgc notice">{@scm.event.ended.event}</div>
        # ELSE #
            <div class="responsive-table">
                <table class="bordered-table">
                    <caption>{@scm.day} {NEXT_DAY}</caption>
                    <colgroup class="hidden-small-screens">
                        <col class="width-pc-6" />
                        <col class="width-pc-39" />
                        <col class="width-pc-08" />
                        <col class="width-pc-08" />
                        <col class="width-pc-39" />
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
                            <tr><td colspan="5">{next_dates.DATE}</td></tr>
                            # START next_dates.next_days #
                                <tr class="# IF next_dates.next_days.C_HAS_SCORE #has-score-color# ENDIF ## IF next_dates.next_days.C_EXEMPT #bgc notice# ENDIF #">
                                    <td class="small">{next_dates.next_days.GAME_DATE_HOUR_MINUTE}</td>
                                    <td class="align-right home-{next_dates.next_days.ID}# IF next_dates.next_days.C_HOME_FAV # text-strong# ENDIF #">
                                        <div class="flex-team flex-right">
                                            <span>
                                                <a
                                                    href="{next_dates.next_days.U_HOME_CALENDAR}"
                                                    aria-label="{@scm.see.club.calendar}"
                                                    class="offload# IF next_dates.next_days.HOME_FORFEIT # warning# ENDIF ## IF next_dates.next_days.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #"
                                                >{next_dates.next_days.HOME_TEAM}</a>
                                            </span>
                                            # IF next_dates.next_days.C_HAS_HOME_LOGO #<img src="{next_dates.next_days.HOME_LOGO}" alt="{next_dates.next_days.HOME_TEAM}"># ENDIF #
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
                                            # IF next_dates.next_days.C_HAS_AWAY_LOGO #<img src="{next_dates.next_days.AWAY_LOGO}" alt="{next_dates.next_days.AWAY_TEAM}"># ENDIF #
                                            <span>
                                                <a
                                                    href="{next_dates.next_days.U_AWAY_CALENDAR}"
                                                    aria-label="{@scm.see.club.calendar}"
                                                    class="offload# IF next_dates.next_days.AWAY_FORFEIT # warning# ENDIF ## IF next_dates.next_days.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #"
                                                >{next_dates.next_days.AWAY_TEAM}</a>
                                            </span>
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
    <div class="days-ranking">
        <div class="scm-table">
            <header class="m-t">
                <h3>{@scm.ranking}</h3>
            </header>
            <div class="scm-line scm-head">
                <div class="scm-line-group width-pc-70">
                    <div class="scm-cell width-pc-20" aria-label="{@scm.th.rank}">{@scm.th.rank.short}</div>
                    <div class="scm-cell cell-left width-pc-80">{@scm.th.team}</div>
                </div>
                <div class="scm-line-group width-pc-30">
                    <div class="scm-cell width-pc-30" aria-label="{@scm.th.points}">{@scm.th.points.short}</div>
                    <div class="scm-cell width-pc-30" aria-label="{@scm.th.played}">{@scm.th.played.short}</div>
                    <div class="scm-cell width-pc-30" aria-label="{@scm.th.goal.average}">{@scm.th.goal.average.short}</div>
                </div>
            </div>
            <div class="scm-group">
                # START ranks #
                    <div class="scm-line ranking-color team-{ranks.TEAM_ID}# IF ranks.C_FAV # fav-team# ENDIF #" style="background-color: {ranks.RANK_COLOR}">
                        <div class="scm-line-group width-pc-70">
                            <div class="scm-cell width-pc-20">{ranks.RANK}</div>
                            <div class="scm-cell scm-name cell-left width-pc-80">
                                # IF ranks.C_HAS_TEAM_LOGO #<img src="{ranks.TEAM_LOGO}" alt="{ranks.TEAM_NAME}"># ENDIF #
                                <span>
                                    <a href="{ranks.U_TEAM_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{ranks.TEAM_NAME}</a>
                                    # IF ranks.C_FORFEIT #<span class="smaller text-italic warning">({@scm.params.status.forfeit})</span># ENDIF #
                                </span>
                            </div>
                        </div>
                        <div class="scm-line-group width-pc-30">
                            <div class="scm-cell width-pc-30">{ranks.POINTS}</div>
                            <div class="scm-cell width-pc-30">{ranks.PLAYED}</div>
                            <div class="scm-cell width-pc-30">{ranks.GOAL_AVERAGE}</div>
                        </div>
                    </div>
                # END ranks #
            </div>
        </div>
    </div>
</div>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>