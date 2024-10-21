<section id="module-scm" class="single-item">
    # INCLUDE MENU #
    <article>
        <header><h2><span class="small">{@scm.team.results} :</span> {TEAM_NAME}</h2></header>
        <div class="content">
            # IF C_HAS_GAMES #
                <div class="responsive-table">
                    <table class="bordered-table width-pc-70 m-a">
                        <colgroup class="hidden-small-screens">
                            <col class="width-pc-5" />
                            # IF NOT C_ONE_DAY #<col class="width-pc-10" /># ENDIF #
                            <col class="width-pc-5" />
                            <col class="width-pc-# IF C_ONE_DAY #37# ELSE #32# ENDIF #" />
                            <col class="width-pc-8" />
                            <col class="width-pc-8" />
                            <col class="width-pc-# IF C_ONE_DAY #37# ELSE #32# ENDIF #" />
                        </colgroup>
                        <thead>
                            <tr>
                                <th aria-label="{@scm.th.day}">{@scm.th.day.short}</th>
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
                                    <td>{games.DAY}</td>
                                    # IF NOT C_ONE_DAY #<td>{games.GAME_DATE_SHORT}</td># ENDIF #
                                    <td>{games.GAME_DATE_HOUR_MINUTE}</td>
                                    <td class="# IF games.C_HOME_WIN # text-strong# ENDIF #">
                                        <div class="flex-team flex-right">
                                            <span><a href="{games.U_HOME_CALENDAR}" class="offload">{games.HOME_TEAM}</a></span>
                                            <img src="{games.HOME_LOGO}" alt="{games.HOME_TEAM}">
                                        </div>
                                    </td>
                                    # IF games.C_STATUS #
                                        <td colspan="2">{games.STATUS}</td>
                                    # ELSE #
                                        <td>{games.HOME_SCORE}</td>
                                        <td>{games.AWAY_SCORE}</td>
                                    # ENDIF #
                                    <td class="# IF games.C_AWAY_WIN # text-strong# ENDIF #">
                                        <div class="flex-team flex-left">
                                            <img src="{games.AWAY_LOGO}" alt="{games.AWAY_TEAM}">
                                            <span><a href="{games.U_AWAY_CALENDAR}" class="offload">{games.AWAY_TEAM}</a></span>
                                        </div>
                                    </td>
                                </tr>
                            # END games #
                        </tbody>
                    </table>
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