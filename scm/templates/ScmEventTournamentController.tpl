# IF C_HAS_GAMES #
    <article class="groups">
        <header class="article-header"><h3>{@scm.groups}</h3></header>
        <div class="content">
            # START team_groups #
                <div class="flex-team">
                    <span class="flex-team-group"><a href="{team_groups.U_GROUP}" class="offload text-strong">{@scm.group} {team_groups.GROUP}</a></span>
                    <div class="flex-team-container">
                        # START team_groups.teams #
                            <span class="pinned link-color">
                                <img src="{team_groups.teams.TEAM_LOGO}" alt="{team_groups.teams.TEAM_NAME}">
                                <span><a href="{team_groups.teams.U_CLUB}" class="offload" aria-label="{@scm.club.see.infos}">{team_groups.teams.TEAM_NAME}</a></span>
                            </span>
                        # END team_groups.teams #
                        </div>
                </div>
            # END team_groups #
        </div>
    </article>
    <article class="games">
        <header class="article-header flex-between">
            <h3>{@scm.calendar}</h3>
            <button id="next-game" class="button default"><i class="fa fa-circle-arrow-down"></i> {@scm.next.games}</button>
            # IF C_ONE_DAY #<p>{ONE_DAY_DATE}</p># ENDIF #
        </header>
        <div class="">
            <p><h3>{@scm.games.groups.stage}</h3></p>
            <div class="">
                # START matchdays #
                    # IF C_HAT_RANKING #
                        <h4><a href="{matchdays.U_MATCHDAY}">{@scm.day} {matchdays.MATCHDAY}</a></h4>
                        <div class="">
                            # INCLUDE matchdays.MATCHDAYS_LIST #
                        </div>
                    # ELSE #
                        <h4>{@scm.round} {matchdays.MATCHDAY}</h4>
                        # IF C_ROUND_RANKINGS #
                            # INCLUDE matchdays.ROUND_RANKINGS_LIST #
                        # ELSE #
                            # INCLUDE matchdays.ROUNDS_LIST #
                        # ENDIF #
                    # ENDIF #
                # END matchdays #
            </div>
            <div class="">
                <p><h3>{@scm.games.brackets.stage}</h3></p>
                <div class="">
                    # START matchrounds #
                        <h4>{matchrounds.L_MATCHROUND}</h4>
                        # IF C_ROUND_RANKINGS #
                            # INCLUDE matchrounds.ROUND_RANKINGS_LIST #
                        # ELSE #
                            # INCLUDE matchrounds.ROUNDS_LIST #
                        # ENDIF #
                    # END matchrounds #
                </div>
            </div>
        </div>
    </article>
# ELSE #
    <div class="message-helper bgc notice">{@scm.message.no.games}</div>
# ENDIF #
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.event.home# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>