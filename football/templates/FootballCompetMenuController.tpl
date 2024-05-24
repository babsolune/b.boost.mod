<header class="section-header"><h1>{HEADER}</h1></header>
<div class="compet-menu flex-between controls">
    <nav class="cssmenu cssmenu-horizontal">
        <ul>
            <li><a href="{U_COMPET}" class="offload cssmenu-title" aria-label="{@football.menu.compet.calendar}"><i class="far fa-fw fa-calendar-days"></i></a></li>
            # IF C_TOURNEY #
                <li><a href="{U_GROUPS_STAGE}" class="offload cssmenu-title" aria-label="{@football.menu.compet.group}"><i class="fa fa-fw fa-list" aria-hidden="true"></i></a></li>
                <li><a href="{U_FINALS_STAGE}" class="offload cssmenu-title" aria-label="{@football.menu.compet.brackets}"><i class="fa fa-fw fa-sitemap fa-rotate-270" aria-hidden="true"></i></a></li>
            # ELSE #
                # IF C_CHAMPIONSHIP #
                    <li><a href="{U_COMPET_CALENDAR}" class="offload cssmenu-title" aria-label="{@football.menu.compet.calendar}"><i class="fa fa-fw fa-" aria-hidden="true"></i></a></li>
                    <li><a href="{U_COMPET_RESULTS}" class="offload cssmenu-title" aria-label="{@football.menu.compet.results}"><i class="fa fa-fw fa-" aria-hidden="true"></i></a></li>
                    <li><a href="{U_COMPET_RANKING}" class="offload cssmenu-title" aria-label="{@football.menu.compet.ranking}"><i class="fa fa-fw fa-" aria-hidden="true"></i></a></li>
                # ENDIF #
            # ENDIF #
        </ul>
    </nav>
    <nav class="cssmenu cssmenu-horizontal">
        <ul>
            <li><a href="{U_TEAMS}" class="offload cssmenu-title" aria-label="{@football.menu.teams}"><i class="fa fa-fw fa-people-group" aria-hidden="true"></i></a></li>
            # IF C_HAS_TEAMS #<li><a href="{U_PARAMS}" class="offload cssmenu-title" aria-label="{@football.menu.params}"><i class="fa fa-fw fa-cogs" aria-hidden="true"></i></a></li># ENDIF #

            # IF C_TOURNEY #
                # IF C_HAS_GROUP_PARAMS #
                    <li><a href="{U_GROUPS}" class="offload cssmenu-title" aria-label="{@football.menu.groups}"><i class="fa fa-fw fa-people-roof" aria-hidden="true"></i></a></li>
                    # IF C_HAS_MATCHES #
                        <li><a href="{U_MATCHES}" class="offload cssmenu-title" aria-label="{@football.menu.matches}"><i class="fa fa-fw fa-people-arrows" aria-hidden="true"></i></a></li>
                    # ENDIF #
                # ENDIF #
            # ELSE #
                # IF C_HAS_TEAMS #
                    <li><a href="{U_MATCHES}" class="offload cssmenu-title" aria-label="{@football.menu.matches}"><i class="fa fa-fw fa-people-arrows" aria-hidden="true"></i></a></li>
                # ENDIF #
            # ENDIF #
        </ul>
    </nav>
</div>
