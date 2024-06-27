
<div class="cell-flex cell-columns-2">
    # IF C_EVENT_STARTING #
        <div></div>
    # ELSE #
    <div>
        <table class="table bordered-table">
            <caption>{@scm.prev.day} ({LAST_DAY})</caption>
            <colgroup class="hidden-small-screens">
                <col class="width-pc-05" />
                # IF NOT C_ONE_DAY #<col class="width-pc-10" /># ENDIF #
                <col class="width-pc-05" />
                <col class="width-pc-# IF C_ONE_DAY #37# ELSE #32# ENDIF #" />
                <col class="width-pc-8" />
                <col class="width-pc-8" />
                <col class="width-pc-# IF C_ONE_DAY #37# ELSE #32# ENDIF #" />
            </colgroup>
            <thead>
                <tr>
                    <th>id</th>
                    # IF NOT C_ONE_DAY #<th>{@scm.th.date}</th># ENDIF #
                    <th aria-label="{@scm.th.hourly}"><i class="far fa-clock"></i></th>
                    <th>{@scm.th.team} 1</th>
                    <th colspan="2">{@scm.th.score}</th>
                    <th>{@scm.th.team} 2</th>
                </tr>
            </thead>
            <tbody>
                # START prev_days #
                    <tr# IF prev_days.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                        <td class="small">{prev_days.GAME_ID}</td>
                        # IF NOT C_ONE_DAY #<td class="small">{prev_days.GAME_DATE_SHORT}</td># ENDIF #
                        <td class="small">{prev_days.GAME_DATE_HOUR_MINUTE}</td>
                        <td class="align-right home-{prev_days.ID}# IF prev_days.C_HOME_FAV # text-strong# ENDIF #">
                            <div class="flex-team flex-right">
                                <span><a href="{prev_days.U_HOME_CALENDAR}" class="offload">{prev_days.HOME_TEAM}</a></span>
                                <img src="{PATH_TO_ROOT}/{prev_days.HOME_LOGO}" alt="{prev_days.HOME_TEAM}">
                            </div>
                        </td>
                        <td>{prev_days.HOME_SCORE}</td>
                        <td>{prev_days.AWAY_SCORE}</td>
                        <td class="align-left away-{prev_days.ID}# IF prev_days.C_AWAY_FAV # text-strong# ENDIF #">
                            <div class="flex-team flex-left">
                                <img src="{PATH_TO_ROOT}/{prev_days.AWAY_LOGO}" alt="{prev_days.AWAY_TEAM}">
                                <span><a href="{prev_days.U_AWAY_CALENDAR}" class="offload">{prev_days.AWAY_TEAM}</a></span>
                            </div>
                        </td>
                    </tr>
                # END prev_days #
            </tbody>
        </table>
    </div>
    # ENDIF #
    # IF C_EVENT_ENDING #
        <div></div>
    # ELSE #
        <div>
            <table class="table bordered-table">
                <caption>{@scm.next.day} ({NEXT_DAY})</caption>
                <colgroup class="hidden-small-screens">
                    <col class="width-pc-05" />
                    # IF NOT C_ONE_DAY #<col class="width-pc-10" /># ENDIF #
                    <col class="width-pc-05" />
                    <col class="width-pc-# IF C_ONE_DAY #37# ELSE #32# ENDIF #" />
                    <col class="width-pc-08" />
                    <col class="width-pc-08" />
                    <col class="width-pc-# IF C_ONE_DAY #37# ELSE #32# ENDIF #" />
                </colgroup>
                <thead>
                    <tr>
                        <th>id</th>
                        # IF NOT C_ONE_DAY #<th>{@scm.th.date}</th># ENDIF #
                        <th aria-label="{@scm.th.hourly}"><i class="far fa-clock"></i></th>
                        <th>{@scm.th.team} 1</th>
                        <th colspan="2">{@scm.th.score}</th>
                        <th>{@scm.th.team} 2</th>
                    </tr>
                </thead>
                <tbody>
                    # START next_days #
                        <tr# IF next_days.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                            <td class="small">{next_days.GAME_ID}</td>
                            # IF NOT C_ONE_DAY #<td class="small">{next_days.GAME_DATE_SHORT}</td># ENDIF #
                            <td class="small">{next_days.GAME_DATE_HOUR_MINUTE}</td>
                            <td class="align-right home-{next_days.ID}# IF next_days.C_HOME_FAV # text-strong# ENDIF #">
                                <div class="flex-team flex-right">
                                    <span><a href="{next_days.U_HOME_CALENDAR}" class="offload">{next_days.HOME_TEAM}</a></span>
                                    <img src="{PATH_TO_ROOT}/{next_days.HOME_LOGO}" alt="{next_days.HOME_TEAM}">
                                </div>
                            </td>
                            <td>{next_days.HOME_SCORE}</td>
                            <td>{next_days.AWAY_SCORE}</td>
                            <td class="align-left away-{next_days.ID}# IF next_days.C_AWAY_FAV # text-strong# ENDIF #">
                                <div class="flex-team flex-LEFT">
                                    <img src="{PATH_TO_ROOT}/{next_days.AWAY_LOGO}" alt="{next_days.AWAY_TEAM}">
                                    <span><a href="{next_days.U_AWAY_CALENDAR}" class="offload">{next_days.AWAY_TEAM}</a></span>
                                </div>
                            </td>
                        </tr>
                    # END next_days #
                </tbody>
            </table>
        </div>
    # ENDIF #
</div>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>