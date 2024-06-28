# INCLUDE MENU #
<article class="days-checker">
    <header><h2>{@scm.check.days}</h2></header>
    <div class="cell-flex cell-inline cell-tile">
        # START teams #
            <div class="cell">
                <div class="cell-header# IF teams.C_CHECK_ERROR # bgc-full error# ENDIF #">
                    <h5 class="cell-name">{teams.TEAM_NAME}</h5>
                </div>
                <div class="cell-list">
                    <ul>
                        <li>{@scm.matchdays.number}</li>
                        <li class="li-stretch"><span>{@scm.total}</span> <span>{teams.GAMES_NUMBER}</span></li>
                        <li class="li-stretch"><span>{@scm.days.ranking.home}</span> <span>{teams.GAMES_HOME_NUMBER}</span></li>
                        <li class="li-stretch"><span>{@scm.days.ranking.away}</span> <span>{teams.GAMES_AWAY_NUMBER}</span></li>
                    </ul>
                </div>
                # IF teams.C_CHECK_ERROR #
                    <div class="cell-content">
                        <p>{@scm.duplicate.matches}</p>
                        # START teams.games #
                            <div><span class="pinned warning">{teams.games.GAME_DAY}</span> {teams.games.TEAM_HOME_NAME} vs {teams.games.TEAM_AWAY_NAME}</div>
                        # END teams.games #
                    </div>
                    <div class="cell-content">
                        <p>{@scm.matchdays.missing}</p>
                        # START teams.missing_days #
                            <a href="{teams.missing_days.U_EDIT_DAYS_GAMES}" class="offload pinned warning">{teams.missing_days.MISSING_DAY}</a>
                        # END teams.missing_days #
                    </div>
                # ENDIF #
            </div>
        # END teams #
    </div>
</article>
