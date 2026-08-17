<script>
    function call_score(game_type, game_cluster, game_round, game_order)
    {
        jQuery.ajax({
            url: '${relative_url(ScmUrlBuilder::ajax_game_form())}',
            type: "post",
            dataType: "json",
            data: {
                'action': 'call',
                'token' : '{TOKEN}',
                'event_id' : '{EVENT_ID}',
                'game_type' : game_type,
                'game_cluster' : game_cluster,
                'game_round' : game_round,
                'game_order' : game_order,
            },
            success: function(returnData) {
                jQuery.each(returnData, function(index, game) {
                    const homePenaltyFields = game_type === 'B'
                        ? (
                            '<label class="label-sup grouped-element" for="home_pen_' + game.game_id + '">' +
                                '<span>Penalties</span>' +
                                '<input class="align-center" type="number" min="0" id="home_pen_' + game.game_id + '" name="home_pen" value="' + game.home_pen + '" placeholder="{@scm.game.event.penalties}">' +
                            '</label>'
                        )
                        : ''
                    ;
                    const awayPenaltyFields = game_type === 'B'
                        ? (
                            '<label class="label-sup grouped-element" for="away_pen_' + game.game_id + '">' +
                                '<span>Penalties</span>' +
                                '<input class="align-center" type="number" min="0" id="away_pen_' + game.game_id + '" name="away_pen" value="' + game.away_pen + '" placeholder="{@scm.game.event.penalties}">' +
                            '</label>'
                        )
                        : ''
                    ;
                    const score_form = jQuery('#score-panel-' + game.game_id + ' .modal-form');
                    score_form.append(
                        '<form method="post" action="{REWRITED_SCRIPT}">' +
                            '<div class="grouped-inputs stretch-inputs inputs-with-sup">' +
                                '<label style="min-width: 220px !important;" class="label-sup grouped-element" for="date_' + game.game_id + '">' +
                                    '<span>Date/heure</span>' +
                                    '<input type="datetime-local" id="date_' + game.game_id + '" name="date" value="' + game.date + '">' +
                                '</label>' +
                                # IF C_DISPLAY_PLAYGROUNDS #
                                    '<label class="label-sup grouped-element" for="playground_' + game.game_id + '">' +
                                        '<span>Terrain</span>' +
                                        '<input class="align-center" type="text" id="playground_' + game.game_id + '" name="playground" value="' + game.playground + '" placeholder="terrain">' +
                                    '</label>' +
                                # ENDIF #
                            '</div>' +
                            '<div class="grouped-inputs stretch-inputs inputs-with-sup">' +
                                '<label class="label-sup grouped-element" for="home_name_' + game.game_id + '">' +
                                    '<span>Locaux</span>' +
                                    '<select id="home_name_' + game.game_id + '" name="home_id"></select>' +
                                '</label>' +
                                '<label class="label-sup grouped-element" for="home_score_' + game.game_id + '">' +
                                    '<span>Score</span>' +
                                    '<input class="align-center" type="number" min="0" id="home_score_' + game.game_id + '" name="home_score" value="' + game.home_score + '" placeholder="score">' +
                                '</label>' +
                                homePenaltyFields +
                            '</div>' +
                            '<div class="grouped-inputs stretch-inputs inputs-with-sup">' +
                                '<label class="label-sup grouped-element" for="away_name_' + game.game_id + '">' +
                                    '<span>Visiteurs</span>' +
                                    '<select id="away_name_' + game.game_id + '" name="away_id"></select>' +
                                '</label>' +
                                '<label class="label-sup grouped-element" for="away_score_' + game.game_id + '">' +
                                    '<span>Score</span>' +
                                    '<input class="align-center" type="number" min="0" id="away_score_' + game.game_id + '" name="away_score" value="' + game.away_score + '" placeholder="score">' +
                                '</label>' +
                                awayPenaltyFields +
                            '</div>' +
                            '<input type="hidden" name="token" value="{TOKEN}" />' +
                            '<input type="hidden" name="event_id" value="{EVENT_ID}" />' +
                            '<div class="align-center">' +
                                '<button type="button" class="button submit" onclick="validate_score(\'' + game_type + '\', \'' + game_cluster + '\', \'' + game_round + '\', \'' + game_order + '\')">Valider</button>' +
                            '</div>' +
                        '</form>'
                    );
                    const home_select = jQuery("#home_name_" + game.game_id);
                    const away_select = jQuery("#away_name_" + game.game_id);
                    game.team_list.forEach((team) => {
                        const home_option = jQuery("<option></option>").attr("value", team.id).text(team.name);
                        if (team.name === game.home_name) {
                            home_option.attr("selected", "selected");
                        }
                        home_select.append(home_option);

                        const away_option = jQuery("<option></option>").attr("value", team.id).text(team.name);
                        if (team.name === game.away_name) {
                            away_option.attr("selected", "selected");
                        }
                        away_select.append(away_option);
                    });
                    jQuery(".close-modal").on('click', function() {
                        score_form.empty();
                    });
                });
            },
            error: function(e) {
                jQuery('body').addClass('bgc-full error');
            }
        });
    }

    function validate_score(game_type, game_cluster, game_round, game_order)
    {
        const form = jQuery('#score-panel-' + game_type + game_cluster + game_round + game_order).find('form');
        jQuery.ajax({
            url: '${relative_url(ScmUrlBuilder::ajax_game_form())}',
            type: "post",
            dataType: "json",
            data: 
                form.serialize() +
                '&action=validate' +
                '&game_type=' + encodeURIComponent(game_type) +
                '&game_cluster=' + encodeURIComponent(game_cluster) +
                '&game_round=' + encodeURIComponent(game_round) +
                '&game_order=' + encodeURIComponent(game_order)
            ,
            success: function(returnData) {
                window.location.reload();
            },
            error: function(returnData) {
                jQuery('body').addClass('bgc-full warning');
            }
        });
    }
</script>

<section id="module-scm" class="several-items">
    # INCLUDE MENU #
    <article>
        <header><h2>{@scm.games.brackets.stage}</h2></header>
        <div class="content">
            # IF C_HAS_GAMES #
                # IF C_FINALS_RANKING_TYPE #
                    # START groups #
                        <h3>{@scm.group} {groups.GROUP}</h3>
                        <div class="cell-flex cell-columns-2">
                            <div class="responsive-table">
                                <table class="bordered-table">
                                    <colgroup class="hidden-small-screens">
                                        <col class="md-width-pc-4" />
                                        <col class="md-width-pc-40" />
                                        <col class="md-width-pc-8" />
                                        <col class="md-width-pc-8" />
                                        <col class="md-width-pc-40" />
                                        # IF C_DISPLAY_PLAYGROUNDS #<col class="md-width-pc-10" /># ENDIF #
                                        <col class="md-width-pc-5" />
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th aria-label="{@scm.th.hourly}"><i class="far fa-clock"></i></th>
                                            <th>{@scm.th.home.team}</th>
                                            <th colspan="2">{@scm.th.score}</th>
                                            <th>{@scm.th.away.team}</th>
                                            # IF C_DISPLAY_PLAYGROUNDS #<th>{@scm.th.playground}</th># ENDIF #
                                            <th aria-label="{@scm.th.details}"><i class="fa fa-align-left"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        # START groups.rounds #
                                            <tr>
                                                <td colspan="# IF C_DISPLAY_PLAYGROUNDS #7# ELSE #6# ENDIF #">{@scm.round} {groups.rounds.ROUND}</td>
                                            </tr>
                                            # START groups.rounds.games #
                                                <tr# IF groups.rounds.games.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                                    <td>{groups.rounds.games.GAME_DATE_HOUR_MINUTE}</td>
                                                    <td class="align-right# IF groups.rounds.games.C_HOME_FAV # text-strong# ENDIF #">
                                                        <div class="flex-team flex-right">
                                                            <span>{groups.rounds.games.HOME_TEAM}</span>
                                                            # IF groups.rounds.games.C_HAS_HOME_LOGO #<img src="{groups.rounds.games.HOME_LOGO}" alt="{groups.rounds.games.HOME_TEAM}"># ENDIF #
                                                        </div>
                                                    </td>
                                                    # IF groups.rounds.games.C_STATUS #
                                                        <td colspan="2">{groups.rounds.games.STATUS}</td>
                                                    # ELSE #
                                                        <td>{groups.rounds.games.HOME_SCORE}</td>
                                                        <td>{groups.rounds.games.AWAY_SCORE}</td>
                                                    # ENDIF #
                                                    <td class="align-left# IF groups.rounds.games.C_AWAY_FAV # text-strong# ENDIF #">
                                                        <div class="flex-team flex-left">
                                                            # IF groups.rounds.games.C_HAS_AWAY_LOGO #<img src="{groups.rounds.games.AWAY_LOGO}" alt="{groups.rounds.games.AWAY_TEAM}"># ENDIF #
                                                            <span>{groups.rounds.games.AWAY_TEAM}</span>
                                                        </div>
                                                    </td>
                                                    # IF C_DISPLAY_PLAYGROUNDS #<td>{groups.rounds.games.PLAYGROUND}</td># ENDIF #
                                                    <td>
                                                        <span class="modal-button --target-panel-{groups.rounds.games.GAME_ID}">
                                                            # IF groups.rounds.games.C_HAS_SCORE #
                                                                # IF groups.rounds.games.C_VIDEO #
                                                                    <i class="far fa-circle-play"></i>
                                                                # ELSE #
                                                                    <i class="far fa-file-lines"></i>
                                                                # ENDIF #
                                                            # ENDIF #
                                                        </span>
                                                        <div id="target-panel-{groups.rounds.games.GAME_ID}" class="modal">
                                                            <div class="modal-overlay close-modal" aria-label="{@common.close}"></div>
                                                            <div class="modal-content">
                                                                <span class="error big hide-modal close-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></span>
                                                                <div class="cell-flex cell-columns-2">
                                                                    <div class="home-team cell">
                                                                        <div class="cell-header">
                                                                            <div class="cell-name">{groups.rounds.games.HOME_TEAM}</div>
                                                                            # IF groups.rounds.games.C_HAS_HOME_LOGO #<img class="smaller md-width-px-25" src="{groups.rounds.games.HOME_LOGO}" alt="{groups.rounds.games.HOME_TEAM}"># ENDIF #
                                                                        </div>
                                                                        <div class="cell-score bigger align-center">
                                                                            {groups.rounds.games.HOME_SCORE}
                                                                        </div>
                                                                        <div class="cell-details">{@scm.game.event.goals}</div>
                                                                        # START groups.rounds.games.home_goals #
                                                                            <div>
                                                                                <span>{groups.rounds.games.home_goals.TIME}'</span>
                                                                                <span>- {groups.rounds.games.home_goals.PLAYER}</span>
                                                                            </div>
                                                                        # END groups.rounds.games.home_goals #
                                                                        <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                                        # START groups.rounds.games.home_yellow #
                                                                            <div>
                                                                                <span>{groups.rounds.games.home_yellow.TIME}'</span>
                                                                                <span>- {groups.rounds.games.home_yellow.PLAYER}</span>
                                                                            </div>
                                                                        # END groups.rounds.games.home_yellow #
                                                                        <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                                        # START groups.rounds.games.home_red #
                                                                            <div>
                                                                                <span>{groups.rounds.games.home_red.TIME}'</span>
                                                                                <span>- {groups.rounds.games.home_red.PLAYER}</span>
                                                                            </div>
                                                                        # END groups.rounds.games.home_red #
                                                                    </div>
                                                                    <div class="away-team cell">
                                                                        <div class="cell-header">
                                                                            <div class="cell-name">{groups.rounds.games.AWAY_TEAM}</div>
                                                                            # IF groups.rounds.games.C_HAS_AWAY_LOGO #<img class="smaller md-width-px-25" src="{groups.rounds.games.AWAY_LOGO}" alt="{groups.rounds.games.AWAY_TEAM}"># ENDIF #
                                                                        </div>
                                                                        <div class="cell-score bigger align-center">
                                                                            {groups.rounds.games.AWAY_SCORE}
                                                                        </div>
                                                                        <div class="cell-details">{@scm.game.event.goals}</div>
                                                                        # START groups.rounds.games.away_goals #
                                                                            <div>
                                                                                <span>{groups.rounds.games.away_goals.TIME}'</span>
                                                                                <span>- {groups.rounds.games.away_goals.PLAYER}</span>
                                                                            </div>
                                                                        # END groups.rounds.games.away_goals #
                                                                        <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                                        # START groups.rounds.games.away_yellow #
                                                                            <div>
                                                                                <span>{groups.rounds.games.away_yellow.TIME}'</span>
                                                                                <span>- {groups.rounds.games.away_yellow.PLAYER}</span>
                                                                            </div>
                                                                        # END groups.rounds.games.away_yellow #
                                                                        <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                                        # START groups.rounds.games.away_red #
                                                                            <div>
                                                                                <span>{groups.rounds.games.away_red.TIME}'</span>
                                                                                <span>- {groups.rounds.games.away_red.PLAYER}</span>
                                                                            </div>
                                                                        # END groups.rounds.games.away_red #
                                                                    </div>
                                                                </div>
                                                                # IF groups.rounds.games.C_VIDEO #
                                                                    <a href="{groups.rounds.games.U_VIDEO}" class="button d-block align-center" target="blank" rel="noopener noreferer">
                                                                        <i class="far fa-circle-play"></i> {@scm.watch.video}
                                                                    </a>
                                                                # ENDIF #
                                                                # IF groups.rounds.games.SUMMARY #
                                                                    {groups.rounds.games.SUMMARY}
                                                                # ENDIF #
                                                                # IF groups.rounds.games.STADIUM #
                                                                    <div class="md-width-pc-50 m-a">{groups.rounds.games.STADIUM}</div>
                                                                # ENDIF #
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            # END groups.rounds.games #
                                        # END groups.rounds #
                                    </tbody>
                                </table>
                            </div>
                            <div class="responsive-table">
                                <table class="bordered-table">
                                    <colgroup class="hidden-small-screens">
                                        <col class="md-width-pc-05" />
                                        <col class="md-width-pc-60" />
                                        <col class="md-width-pc-05" />
                                        <col class="md-width-pc-05" />
                                        <col class="md-width-pc-05" />
                                        <col class="md-width-pc-05" />
                                        <col class="md-width-pc-05" />
                                        <col class="md-width-pc-05" />
                                        <col class="md-width-pc-05" />
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>{@scm.th.rank.short}</th>
                                            <th>{@scm.th.team}</th>
                                            <th>{@scm.th.points.short}</th>
                                            <th>{@scm.th.played.short}</th>
                                            <th>{@scm.th.win.short}</th>
                                            <th>{@scm.th.draw.short}</th>
                                            <th>{@scm.th.loss.short}</th>
                                            <th>{@scm.th.goals.for.short}</th>
                                            <th>{@scm.th.goals.against.short}</th>
                                            <th>{@scm.th.goal.average.short}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        # START groups.ranks #
                                            <tr class="ranking-color# IF groups.ranks.C_FAV # fav-team# ENDIF #" style="background-color: {groups.ranks.RANK_COLOR}">
                                                <td>{groups.ranks.RANK}</td>
                                                <td class="">
                                                    <div class="flex-team flex-left">
                                                        <img src="{groups.ranks.TEAM_LOGO}" alt="{groups.ranks.TEAM_NAME}">
                                                        <span>{groups.ranks.TEAM_NAME}</span>
                                                    </div>
                                                </td>
                                                <td>{groups.ranks.POINTS}</td>
                                                <td>{groups.ranks.PLAYED}</td>
                                                <td>{groups.ranks.WIN}</td>
                                                <td>{groups.ranks.DRAW}</td>
                                                <td>{groups.ranks.LOSS}</td>
                                                <td>{groups.ranks.GOALS_FOR}</td>
                                                <td>{groups.ranks.GOALS_AGAINST}</td>
                                                <td>{groups.ranks.GOAL_AVERAGE}</td>
                                            </tr>
                                        # END groups.ranks #
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    # END groups #
                # ELSE #
                    # IF C_RETURN_GAMES #
                        <div class="round-trip-bracket">
                            <div class="cell-bracket">
                                # START rounds #
                                    <div# IF rounds.C_ALL_PLACES # id="round-trip-main-round-{rounds.ROUND_ID}"# ENDIF # class="bracket-round# IF rounds.C_ALL_PLACES # all-places# ENDIF ## IF rounds.C_HAT_PLAYOFF # first-round# ENDIF #">
                                        <h5 class="bracket-round-title">{rounds.L_TITLE}</h5>
                                        <div class="bracket-round-games">
                                            # IF rounds.C_DRAW_GAMES #<div># ENDIF #
                                            # START rounds.games #
                                                <div id="{rounds.games.GAME_ID}" class="game-container">
                                                    <div class="game-details small text-italic">
                                                        <span>{rounds.games.GAME_ID}</span>
                                                        <span>{rounds.games.PLAYGROUND}</span>
                                                        # IF NOT C_ONE_DAY #
                                                            # IF NOT rounds.C_FINAL #
                                                                <span>{rounds.games.GAME_DATE_B_YEAR}</span>
                                                            # ENDIF #
                                                        # ENDIF #
                                                        <span>
                                                            # IF C_ONE_DAY #
                                                                {rounds.games.GAME_DATE_A_HOUR_MINUTE} - {rounds.games.GAME_DATE_B_HOUR_MINUTE}
                                                            # ELSEIF rounds.C_FINAL #
                                                                {rounds.games.GAME_DATE_SHORT}
                                                            # ELSE #
                                                                {rounds.games.GAME_DATE_A_DAY_MONTH} - {rounds.games.GAME_DATE_B_DAY_MONTH}
                                                            # ENDIF #
                                                        </span>
                                                    </div>
                                                    <div class="id-{rounds.games.HOME_ID} game-team game-home# IF rounds.games.C_HOME_FAV # text-strong# ENDIF #"
                                                            # IF rounds.games.C_HOME_WIN # style="background-color: {rounds.games.WIN_COLOR}"# ENDIF #>
                                                        <div class="home-{rounds.games.GAME_ID} home-team">
                                                            <div class="flex-team flex-left">
                                                                # IF rounds.games.C_HOME_EMPTY #
                                                                    <span>{rounds.games.HOME_EMPTY}</span>
                                                                # ELSE #
                                                                    <img src="{rounds.games.HOME_LOGO}" alt="{rounds.games.HOME_TEAM}">
                                                                    <span><a href="{rounds.games.U_HOME_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload# IF rounds.games.HOME_FORFEIT # warning# ENDIF #">{rounds.games.HOME_TEAM}</a></span>
                                                                # ENDIF #
                                                            </div>
                                                        </div>
                                                        <div class="game-team# IF NOT rounds.C_HAT_PLAYOFF ## IF NOT rounds.C_FINAL # md-width-px-100# ENDIF ## ENDIF #">
                                                            <div class="game-score home-score# IF NOT rounds.C_HAT_PLAYOFF ## IF NOT rounds.C_FINAL # md-width-pc-50# ELSE # md-width-px-50# ENDIF ## ELSE # md-width-px-50# ENDIF #">{rounds.games.HOME_SCORE}</div>
                                                            # IF NOT rounds.C_FINAL #
                                                                <div class="game-score home-score md-width-pc-50">{rounds.games.HOME_SCORE_B}# IF rounds.games.C_HAS_PEN # <span class="small">({rounds.games.HOME_PEN})</span># ENDIF #</div>
                                                            # ENDIF #
                                                        </div>
                                                    </div>
                                                    <div class="id-{rounds.games.AWAY_ID} game-team game-away# IF rounds.games.C_AWAY_FAV # text-strong# ENDIF #"
                                                            # IF rounds.games.C_AWAY_WIN # style="background-color: {rounds.games.WIN_COLOR}"# ENDIF #>
                                                        <div class="away-{rounds.games.GAME_ID} away-team">
                                                            <div class="flex-team flex-left">
                                                                # IF rounds.games.C_AWAY_EMPTY #
                                                                    <span>{rounds.games.AWAY_EMPTY}</span>
                                                                # ELSE #
                                                                    <img src="{rounds.games.AWAY_LOGO}" alt="{rounds.games.AWAY_TEAM}">
                                                                    <span><a href="{rounds.games.U_AWAY_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload# IF rounds.games.AWAY_FORFEIT # warning# ENDIF #">{rounds.games.AWAY_TEAM}</a></span>
                                                                # ENDIF #
                                                            </div>
                                                        </div>
                                                        <div class="game-team# IF NOT rounds.C_HAT_PLAYOFF ## IF NOT rounds.C_FINAL # md-width-px-100# ENDIF ## ENDIF #">
                                                            <div class="game-score away-score# IF NOT rounds.C_HAT_PLAYOFF ## IF NOT rounds.C_FINAL # md-width-pc-50# ELSE # md-width-px-50# ENDIF ## ELSE # md-width-px-50# ENDIF #">{rounds.games.AWAY_SCORE}</div>
                                                            # IF NOT rounds.C_FINAL #
                                                                <div class="game-score away-score md-width-pc-50">{rounds.games.AWAY_SCORE_B}# IF rounds.games.C_HAS_PEN # <span class="small">({rounds.games.AWAY_PEN})</span># ENDIF #</div>
                                                            # ENDIF #
                                                        </div>
                                                    </div>
                                                </div>
                                            # END rounds.games #
                                            # IF rounds.C_DRAW_GAMES #</div># ENDIF #
                                        </div>
                                    </div>
                                # END rounds #
                            </div>
                        </div>
                    # ELSE #
                        # START brackets #
                            <h3>{brackets.BRACKET_NAME}</h3>
                            <div class="winner-bracket">
                                <div class="cell-bracket">
                                    # START brackets.rounds #
                                        <div# IF brackets.rounds.C_ALL_PLACES # id="bracket-{brackets.BRACKET_ID}-main-round-{brackets.rounds.ROUND_ID}"# ENDIF # class="bracket-round# IF brackets.rounds.C_ALL_PLACES # all-places# ENDIF #">
                                            <h5 class="bracket-round-title">{brackets.rounds.L_TITLE}</h5>
                                            <div class="bracket-round-games">
                                                # IF brackets.C_DRAW_GAMES #<div># ENDIF #
                                                # START brackets.rounds.games #
                                                    <div id="{brackets.rounds.games.GAME_ID}" class="game-container">
                                                        <div class="game-details small" aria-label="{brackets.rounds.games.GAME_ID}">
                                                            # IF brackets.rounds.games.C_THIRD_PLACE #<div>{@scm.round.looser.final}</div># ENDIF #
                                                            <div>
                                                                # IF C_ONE_DAY #{brackets.rounds.games.GAME_DATE_HOUR_MINUTE}
                                                                # ELSE #{brackets.rounds.games.GAME_DATE_FULL}
                                                                # ENDIF #
                                                            </div>
                                                            <div class="cell-gap align-right">
                                                                # IF C_DISPLAY_PLAYGROUNDS #<span aria-label="{@scm.field}">{brackets.rounds.games.PLAYGROUND}</span># ENDIF #
                                                                # IF brackets.rounds.games.C_HAS_DETAILS #
                                                                    <a class="modal-button --target-panel-{brackets.rounds.games.GAME_ID}" aria-label="{@scm.game.event.details} {brackets.rounds.games.GAME_ID}">
                                                                        <i class="far fa-file-lines"></i>
                                                                    </a>
                                                                    <div id="target-panel-{brackets.rounds.games.GAME_ID}" class="modal modal-half">
                                                                        <div class="modal-overlay close-modal" aria-label="{@common.close}"></div>
                                                                        <div class="modal-content">
                                                                            <span class="error big hide-modal close-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></span>
                                                                            <div class="cell-flex cell-columns-2">
                                                                                <div class="home-team cell">
                                                                                    <div class="cell-header">
                                                                                        <div class="cell-name">
                                                                                            # IF brackets.rounds.games.C_HAS_HOME_LOGO #<img class="smaller md-width-px-25" src="{brackets.rounds.games.HOME_LOGO}" alt="{brackets.rounds.games.HOME_TEAM}"># ENDIF #
                                                                                            <a href="{brackets.rounds.games.U_HOME_CLUB}" class="offload d-inline-block">{brackets.rounds.games.HOME_TEAM}</a>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="cell-score bigger align-center">
                                                                                        {brackets.rounds.games.HOME_SCORE}
                                                                                    </div>
                                                                                    # IF brackets.rounds.games.C_HAS_PEN #
                                                                                        <div class="cell-infos">
                                                                                            <span class="text-strong">{@scm.game.event.penalties} :</span>
                                                                                            <span>{brackets.rounds.games.HOME_PEN}</span>
                                                                                        </div>
                                                                                    # ENDIF #
                                                                                    <div class="cell-details">{@scm.game.event.goals}</div>
                                                                                    # START brackets.rounds.games.home_goals #
                                                                                        <div>
                                                                                            <span>{brackets.rounds.games.home_goals.TIME}'</span>
                                                                                            <span>- {brackets.rounds.games.home_goals.PLAYER}</span>
                                                                                        </div>
                                                                                    # END brackets.rounds.games.home_goals #
                                                                                    <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                                                    # START brackets.rounds.games.home_yellow #
                                                                                        <div>
                                                                                            <span>{brackets.rounds.games.home_yellow.TIME}'</span>
                                                                                            <span>- {brackets.rounds.games.home_yellow.PLAYER}</span>
                                                                                        </div>
                                                                                    # END brackets.rounds.games.home_yellow #
                                                                                    <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                                                    # START brackets.rounds.games.home_red #
                                                                                        <div>
                                                                                            <span>{brackets.rounds.games.home_red.TIME}'</span>
                                                                                            <span>- {brackets.rounds.games.home_red.PLAYER}</span>
                                                                                        </div>
                                                                                    # END brackets.rounds.games.home_red #
                                                                                </div>
                                                                                <div class="away-team cell">
                                                                                    <div class="cell-header">
                                                                                        <div class="cell-name">
                                                                                            # IF brackets.rounds.games.C_HAS_AWAY_LOGO #<img class="smaller md-width-px-25" src="{brackets.rounds.games.AWAY_LOGO}" alt="{brackets.rounds.games.AWAY_TEAM}"># ENDIF #
                                                                                            <a href="{brackets.rounds.games.U_AWAY_CLUB}" class="offload d-inline-block">{brackets.rounds.games.AWAY_TEAM}</a>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="cell-score bigger align-center">
                                                                                        {brackets.rounds.games.AWAY_SCORE}
                                                                                    </div>
                                                                                    # IF brackets.rounds.games.C_HAS_PEN #
                                                                                        <div class="cell-infos">
                                                                                            <span class="text-strong">{@scm.game.event.penalties} :</span>
                                                                                            <span>{brackets.rounds.games.AWAY_PEN}</span>
                                                                                        </div>
                                                                                    # ENDIF #
                                                                                    <div class="cell-details">{@scm.game.event.goals}</div>
                                                                                    # START brackets.rounds.games.away_goals #
                                                                                        <div>
                                                                                            <span>{brackets.rounds.games.away_goals.TIME}'</span>
                                                                                            <span>- {brackets.rounds.games.away_goals.PLAYER}</span>
                                                                                        </div>
                                                                                    # END brackets.rounds.games.away_goals #
                                                                                    <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                                                    # START brackets.rounds.games.away_yellow #
                                                                                        <div>
                                                                                            <span>{brackets.rounds.games.away_yellow.TIME}'</span>
                                                                                            <span>- {brackets.rounds.games.away_yellow.PLAYER}</span>
                                                                                        </div>
                                                                                    # END brackets.rounds.games.away_yellow #
                                                                                    <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                                                    # START brackets.rounds.games.away_red #
                                                                                        <div>
                                                                                            <span>{brackets.rounds.games.away_red.TIME}'</span>
                                                                                            <span>- {brackets.rounds.games.away_red.PLAYER}</span>
                                                                                        </div>
                                                                                    # END brackets.rounds.games.away_red #
                                                                                </div>
                                                                            </div>
                                                                            # IF brackets.rounds.games.C_VIDEO #
                                                                                <a href="{brackets.rounds.games.U_VIDEO}" class="button d-block align-center" target="blank" rel="noopener noreferer">
                                                                                    <i class="far fa-circle-play"></i> {@scm.watch.video}
                                                                                </a>
                                                                            # ENDIF #
                                                                            # IF brackets.rounds.games.SUMMARY #
                                                                                {brackets.rounds.games.SUMMARY}
                                                                            # ENDIF #
                                                                            # IF brackets.rounds.games.STADIUM #
                                                                                <div class="md-width-pc-50 m-a">{brackets.rounds.games.STADIUM}</div>
                                                                            # ENDIF #
                                                                        </div>
                                                                    </div>
                                                                # ENDIF #
                                                                # IF C_CONTROLS #
                                                                    <a class="modal-button --score-panel-{brackets.rounds.games.GAME_ID}"
                                                                        aria-label="{@scm.game.event.details}"
                                                                        onclick="call_score('{brackets.rounds.games.GAME_TYPE}', '{brackets.rounds.games.GAME_CLUSTER}', '{brackets.rounds.games.GAME_ROUND}', '{brackets.rounds.games.GAME_ORDER}')">
                                                                        <i class="fa fa-gear"></i>
                                                                    </a>
                                                                    <div id="score-panel-{brackets.rounds.games.GAME_ID}" class="modal modal-half">
                                                                        <div class="modal-overlay close-modal" aria-label="{@common.close}"></div>
                                                                        <div class="modal-content">
                                                                            <span class="error big hide-modal close-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></span>
                                                                            <div class="modal-form"></div>
                                                                        </div>
                                                                    </div>
                                                                # ENDIF #
                                                            </div>
                                                        </div>
                                                        <div class="id-{brackets.rounds.games.HOME_ID} game-team game-home# IF brackets.rounds.games.C_HOME_FAV # text-strong# ENDIF #"
                                                                # IF brackets.rounds.games.C_HOME_WIN # style="background-color: {brackets.rounds.games.WIN_COLOR}"# ENDIF #>
                                                            <div class="home-{brackets.rounds.games.GAME_ID} home-team">
                                                                <div class="flex-team flex-left">
                                                                    # IF brackets.rounds.games.C_HOME_EMPTY #
                                                                        <span>{brackets.rounds.games.HOME_EMPTY}</span>
                                                                    # ELSE #
                                                                        # IF brackets.rounds.games.C_HAS_HOME_LOGO #<img src="{brackets.rounds.games.HOME_LOGO}" alt="{brackets.rounds.games.HOME_TEAM}"># ENDIF #
                                                                        <span><a href="{brackets.rounds.games.U_HOME_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload">{brackets.rounds.games.HOME_TEAM}</a></span>
                                                                    # ENDIF #
                                                                </div>
                                                            </div>
                                                            <div class="game-score home-score md-width-px-50 align-center">{brackets.rounds.games.HOME_SCORE}# IF brackets.rounds.games.C_HAS_PEN # <span class="small">({brackets.rounds.games.HOME_PEN})</span># ENDIF #</div>
                                                        </div>
                                                        <div class="id-{brackets.rounds.games.AWAY_ID} game-team game-away# IF brackets.rounds.games.C_AWAY_FAV # text-strong# ENDIF #"
                                                                # IF brackets.rounds.games.C_AWAY_WIN # style="background-color: {brackets.rounds.games.WIN_COLOR}"# ENDIF #>
                                                            <div class="away-{brackets.rounds.games.GAME_ID} away-team">
                                                                <div class="flex-team flex-left">
                                                                    # IF brackets.rounds.games.C_AWAY_EMPTY #
                                                                        <span>{brackets.rounds.games.AWAY_EMPTY}</span>
                                                                    # ELSE #
                                                                        # IF brackets.rounds.games.C_HAS_AWAY_LOGO #<img src="{brackets.rounds.games.AWAY_LOGO}" alt="{brackets.rounds.games.AWAY_TEAM}"># ENDIF #
                                                                        <span><a href="{brackets.rounds.games.U_AWAY_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload">{brackets.rounds.games.AWAY_TEAM}</a></span>
                                                                    # ENDIF #
                                                                </div>
                                                            </div>
                                                            <div class="game-score away-score md-width-px-50 align-center">{brackets.rounds.games.AWAY_SCORE}# IF brackets.rounds.games.C_HAS_PEN # <span class="small">({brackets.rounds.games.AWAY_PEN})</span># ENDIF #</div>
                                                        </div>
                                                    </div>
                                                # END brackets.rounds.games #
                                                # IF brackets.C_DRAW_GAMES #</div># ENDIF #
                                            </div>
                                        </div>
                                    # END brackets.rounds #
                                </div>
                                # IF C_LOOSER_BRACKET #
                                    <div class="cell-bracket">
                                        # START brackets.rounds #
                                            <div id="bracket-{brackets.BRACKET_ID}-sub-round-{brackets.rounds.ROUND_ID}" class="sub-bracket">
                                                <div class="bracket-round-games"></div>
                                            </div>
                                        # END brackets.rounds #
                                    </div>
                                # ENDIF #
                            </div>
                        # END brackets #
                    # ENDIF #
                # ENDIF #
            # ELSE #
                <div class="message-helper bgc notice">{@scm.message.no.games}</div>
            # ENDIF #
        </div>
    </article>
    <footer></footer>
</section>

# IF C_LOOSER_BRACKET #
    <script>
        function move_games(target) {
            let elements = document.querySelectorAll('[id*="bracket-' + target + '-main-round-"]');

            elements.forEach(element => {
                let idName = element.getAttribute('id');
                let split = idName.split('-');
                let id = split[split.length - 1];
                let mainRound = document.querySelector('#bracket-' + target + '-main-round-' + id + '');
                let subRound = document.querySelector('#bracket-' + target + '-sub-round-' + id + '');
                let gameCount = mainRound.querySelectorAll('.game-container').length;

                if (gameCount >= 2) {
                    let lastTwoGames = Array.from(mainRound.querySelectorAll('.game-container')).slice(gameCount - (gameCount / 2), gameCount);
                    lastTwoGames.forEach(game => subRound.appendChild(game));
                }
            });
        }
        # START brackets #
            move_games({brackets.BRACKET_ID});
        # END brackets #
    </script>
# ENDIF #