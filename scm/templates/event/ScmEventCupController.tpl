# IF C_HAS_GAMES #
    <article class="groups">
        <header class="article-header"><h3>{@scm.teams}</h3></header>
        <div class="content">
            # START team_groups #
                <div class="flex-group">
                    <div class="flex-group-container">
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
            <button id="next-game" class="button default"><i class="fa fa-circle-arrow-down"></i> {@scm.next.games}</button>
        </header>
        # IF C_ONE_DAY #<p>{ONE_DAY_DATE}</p># ENDIF #
        <p><h4>{@scm.games.brackets.stage}</h4></p>
        # START matchrounds #
            <h5>{matchrounds.L_MATCHROUND}</h5>
            # INCLUDE matchrounds.ROUNDS_LIST #
        # END matchrounds #
    </article>
# ELSE #
    <div class="message-helper bgc notice">{@scm.message.no.games}</div>
# ENDIF #
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.event.home.js"></script>