<div>
    <h3>{@scm.clubs.list}</h3>
    # START clubs_list #
        <a href="{clubs_list.U_CLUB}" class="offload pinned link-color" aria-label="{@scm.see.club}">{clubs_list.CLUB_SHORT_NAME}</a>
    # END clubs_list #
</div>

<div class="cell-flex cell-columns-2">
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
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>