# INCLUDE MENU #
<article>
    <header><h2>{@scm.days.results} {@scm.day} {DAY}</h2></header>
    <div class="content">
        # IF C_HAS_GAMES #
            <table class="width-pc-70 table bordered-table m-a">
                <colgroup class="hidden-small-screens">
                    # IF NOT C_ONE_DAY #
                        <col class="width-pc-10" />
                    # ENDIF #
                    <col class="width-pc-4" />
                    <col class="width-pc-# IF C_ONE_DAY #40# ELSE #35# ENDIF #" />
                    <col class="width-pc-8" />
                    <col class="width-pc-8" />
                    <col class="width-pc-# IF C_ONE_DAY #40# ELSE #35# ENDIF #" />
                </colgroup>
                <thead>
                    <tr>
                        # IF NOT C_ONE_DAY #<th>{@scm.th.date}</th># ENDIF #
                        <th aria-label="{@scm.th.hourly}"><i class="far fa-clock"></i></th>
                        <th>{@scm.th.team} 1</th>
                        <th colspan="2">{@scm.th.score}</th>
                        <th>{@scm.th.team} 2</th>
                    </tr>
                </thead>
                <tbody>
                    # START games #
                        <tr# IF games.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                            # IF NOT C_ONE_DAY #<td><span class="">{games.GAME_DATE_SHORT}</span></td># ENDIF #
                            <td>{games.GAME_DATE_HOUR_MINUTE}</td>
                            <td class="# IF games.C_HOME_FAV #text-strong# ENDIF #">
                                <div class="flex-team flex-right">
                                    <span><a href="{games.U_HOME_CALENDAR}" class="offload">{games.HOME_TEAM}</a></span>
                                    <img src="{PATH_TO_ROOT}/{games.HOME_LOGO}" alt="{games.HOME_TEAM}">
                                </div>
                            </td>
                            <td>{games.HOME_SCORE}</td>
                            <td>{games.AWAY_SCORE}</td>
                            <td class="# IF games.C_AWAY_FAV #text-strong# ENDIF #">
                                <div class="flex-team flex-left">
                                    <img src="{PATH_TO_ROOT}/{games.AWAY_LOGO}" alt="{games.AWAY_TEAM}">
                                    <span><a href="{games.U_AWAY_CALENDAR}" class="offload">{games.AWAY_TEAM}</a></span>
                                </div>
                            </td>
                        </tr>
                    # END games #
                </tbody>
            </table>
        # ELSE #
            <div class="message-helper bgc notice">{@scm.message.no.games}</div>
        # ENDIF #
    </div>
</article>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>