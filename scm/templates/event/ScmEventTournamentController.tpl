# IF C_HAS_GAMES #
    <nav id="team-list" class="cssmenu cssmenu-horizontal">
        <ul class="level-0">
            # START team_groups #
                <li class="has-sub">
                    <a href="{team_groups.U_GROUP}" class="offload cssmenu-title"><span>{@scm.group} {team_groups.GROUP}</span></a>
                    <ul class="level-1">
                        # START team_groups.teams #
                            <li>
                                <a href="{team_groups.teams.U_CLUB}" class="offload cssmenu-title" aria-label="{@scm.club.see.infos}">
                                    <img class="cell-icon" src="{team_groups.teams.TEAM_LOGO}" alt="{team_groups.teams.TEAM_NAME}">
                                    <span>{team_groups.teams.TEAM_NAME}</span>
                                </a>
                            </li>
                        # END team_groups.teams #
                    </ul>
                </li>
            # END team_groups #
        </ul>
    </nav>
    <script>jQuery('#team-list').menumaker({title: ${escapejs(@scm.clubs.list)}, format: 'multitoggle', breakpoint: 768});</script>
    <article class="games">
        <header class="article-header flex-between flex-between-large">
            <h3>{@scm.calendar}</h3>
            # IF C_ONE_DAY #<p>{ONE_DAY_DATE}</p># ENDIF #
            <button id="next-game" class="button default"><i class="fa fa-circle-arrow-down"></i> {@scm.next.games}</button>
        </header>
        <p><h3>{@scm.games.groups.stage}</h3></p>
        # START matchdays #
            <details open>
                # IF C_HAT_RANKING #
                    <summary class="bgc-sub"><a href="{matchdays.U_MATCHDAY}">{@scm.day} {matchdays.MATCHDAY}</a></summary>
                    <div class="">
                        <div class="content"># INCLUDE matchdays.MATCHDAYS_LIST #</div>
                    </div>
                # ELSE #
                    <summary class="bgc-sub">{@scm.round} {matchdays.MATCHDAY}</summary>
                    # IF C_ROUND_RANKINGS #
                        <div class="content"># INCLUDE matchdays.ROUND_RANKINGS_LIST #</div>
                    # ELSE #
                        <div class="content"># INCLUDE matchdays.ROUNDS_LIST #</div>
                    # ENDIF #
                # ENDIF #
            </details>
        # END matchdays #
        <p><h3>{@scm.games.brackets.stage}</h3></p>
        # START matchrounds #
            <details open>
                <summary class="bgc-sub">{matchrounds.L_MATCHROUND}</summary>
                # IF C_ROUND_RANKINGS #
                    <div class="content"># INCLUDE matchrounds.ROUND_RANKINGS_LIST #</div>
                # ELSE #
                    <div class="content"># INCLUDE matchrounds.ROUNDS_LIST #</div>
                # ENDIF #
            </details>
        # END matchrounds #
    </article>
# ELSE #
    <div class="message-helper bgc notice">{@scm.message.no.games}</div>
# ENDIF #
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.event.home.js"></script>