<script>
    # IF C_REFRESH_TIME #
        setInterval(refresh_scores, 60000);
        function refresh_scores()
        {
            jQuery.ajax({
                url: '${relative_url(ScmUrlBuilder::ajax_game_format())}',
                type: "post",
                dataType: "json",
                data: {
                    'token' : '{TOKEN}',
                    'event_id' : '{EVENT_ID}'
                },
                beforeSend: function(returnData){
                    jQuery.each(returnData, function(index, game) {
                        jQuery('#vs-' + returnData.game_id).html('<i class="fa fa-spin fa-spinner"></i>');
                    });
                },
                success: function(returnData){
                    jQuery.each(returnData, function(index, game) {
                        jQuery('#vs-' + returnData.game_id).html('-');
                        jQuery('#home-' + game.game_id).html(game.home_score);
                        jQuery('#away-' + game.game_id).html(game.away_score);
                    });
                },
                error: function(e){
                }
            });
        }
    # ENDIF #

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

# START blocks #
    # IF blocks.C_SEVERAL_DATES #<h5 class="cell-content">{blocks.TITLE}</h5># ENDIF #
    <div class="cell-flex cell-columns-2">
        # START blocks.items #
            <div id="game-{blocks.items.GAME_ID}" class="cell cell-game" data-date="{blocks.items.GAME_DATE_TIMESTAMP}" aria-label="{blocks.items.GAME_ID}">
                <div class="flex-between flex-between-large small">
                    <div class="sm-width-pc-100 md-width-pc-50 cell-gap">
                        <time class="sm-width-pc-# IF blocks.items.C_STATUS #50# ELSE #70# ENDIF # cell-gap align-left">{blocks.items.GAME_DATE_HOUR_MINUTE}</time>
                        # IF blocks.items.C_LINK #
                            <a href="{blocks.items.U_GROUP}" aria-label="{@scm.group.results}" class="offload cell-gap">
                                {blocks.items.CLUSTER_NAME}
                            </a>
                        # ENDIF #
                        # IF blocks.items.C_STATUS #
                            <span class="bgc notice">{blocks.items.STATUS}</span>
                        # ENDIF #
                    </div>
                    <div class="sm-width-pc-30 cell-gap align-right">
                        # IF C_DISPLAY_PLAYGROUNDS #
                            <span class="sm-width-pc-100 md-width-pc-33">{@scm.field}: {blocks.items.PLAYGROUND}</span>
                        # ENDIF #
                        # IF blocks.items.C_HAS_DETAILS #
                            <a class="modal-button --target-panel-{blocks.items.GAME_ID}">
                                    # IF blocks.items.C_VIDEO #
                                        <i class="far fa-circle-play"></i>
                                    # ELSE #
                                        <i class="far fa-file-lines"></i>
                                    # ENDIF #
                            </a>
                            <div id="target-panel-{blocks.items.GAME_ID}" class="modal">
                                <div class="modal-overlay close-modal" aria-label="{@common.close}"></div>
                                <div class="modal-content">
                                    <span class="error big hide-modal close-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></span>
                                    <div class="cell-flex cell-columns-2 cell-tile">
                                        <div class="home-team">
                                            <div class="cell-header flex-team">
                                                <h4 class="cell-name">
                                                    <a href="{blocks.items.U_HOME_CLUB}" class="offload">{blocks.items.HOME_TEAM}</a>
                                                </h4>
                                                # IF blocks.items.C_HAS_HOME_LOGO #<img src="{blocks.items.HOME_LOGO}" alt="{blocks.items.HOME_TEAM}"># ENDIF #
                                            </div>
                                            <div class="cell-score bigger align-center">
                                                {blocks.items.HOME_SCORE}
                                            </div>
                                            <div class="cell-details">{@scm.game.event.goals}</div>
                                            # START blocks.items.home_goals #
                                                <div>
                                                    <span>{blocks.items.home_goals.TIME}'</span>
                                                    <span>- {blocks.items.home_goals.PLAYER}</span>
                                                </div>
                                            # END blocks.items.home_goals #
                                            <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                            # START blocks.items.home_yellow #
                                                <div>
                                                    <span>{blocks.items.home_yellow.TIME}'</span>
                                                    <span>- {blocks.items.home_yellow.PLAYER}</span>
                                                </div>
                                            # END blocks.items.home_yellow #
                                            <div class="cell-details">{@scm.game.event.cards.red}</div>
                                            # START blocks.items.home_red #
                                                <div>
                                                    <span>{blocks.items.home_red.TIME}'</span>
                                                    <span>- {blocks.items.home_red.PLAYER}</span>
                                                </div>
                                            # END blocks.items.home_red #
                                        </div>
                                        <div class="away-team">
                                            <div class="cell-header flex-team">
                                                <h4 class="cell-name">
                                                    <a href="{blocks.items.U_AWAY_CLUB}" class="offload">{blocks.items.AWAY_TEAM}</a>
                                                </h4>
                                                # IF blocks.items.C_HAS_AWAY_LOGO #<img src="{blocks.items.AWAY_LOGO}" alt="{blocks.items.AWAY_TEAM}"># ENDIF #
                                            </div>
                                            <div class="cell-score bigger align-center">
                                                {blocks.items.AWAY_SCORE}
                                            </div>
                                            <div class="cell-details">{@scm.game.event.goals}</div>
                                            # START blocks.items.away_goals #
                                                <div>
                                                    <span>{blocks.items.away_goals.TIME}'</span>
                                                    <span>- {blocks.items.away_goals.PLAYER}</span>
                                                </div>
                                            # END blocks.items.away_goals #
                                            <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                            # START blocks.items.away_yellow #
                                                <div>
                                                    <span>{blocks.items.away_yellow.TIME}'</span>
                                                    <span>- {blocks.items.away_yellow.PLAYER}</span>
                                                </div>
                                            # END blocks.items.away_yellow #
                                            <div class="cell-details">{@scm.game.event.cards.red}</div>
                                            # START blocks.items.away_red #
                                                <div>
                                                    <span>{blocks.items.away_red.TIME}'</span>
                                                    <span>- {blocks.items.away_red.PLAYER}</span>
                                                </div>
                                            # END blocks.items.away_red #
                                        </div>
                                    </div>
                                    # IF blocks.items.C_VIDEO #
                                        <a href="{blocks.items.U_VIDEO}" class="button submit" target="blank" rel="noopener noreferer">
                                            <i class="far fa-circle-play"></i> {@scm.watch.video}
                                        </a>
                                    # ENDIF #
                                    <div class="flex-between flex-between-large">
                                        # IF blocks.items.STADIUM #
                                            <div class="md-width-pc-50">
                                                <h5>{@scm.game.event.stadium}</h5>
                                                {blocks.items.STADIUM}
                                            </div>
                                        # ENDIF #
                                        # IF blocks.items.SUMMARY #
                                            <div class="md-width-pc-50">
                                                <h5>{@scm.game.event.summary}</h5>
                                                {blocks.items.SUMMARY}
                                            </div>
                                        # ENDIF #
                                    </div>
                                </div>
                            </div>
                        # ENDIF #
                        # IF C_CONTROLS #
                            <a class="modal-button --score-panel-{blocks.items.GAME_ID}"
                                onclick="call_score('{blocks.items.GAME_TYPE}', '{blocks.items.GAME_CLUSTER}', '{blocks.items.GAME_ROUND}', '{blocks.items.GAME_ORDER}')">
                                <i class="fa fa-gear"></i>
                            </a>
                            <div id="score-panel-{blocks.items.GAME_ID}" class="modal modal-half">
                                <div class="modal-overlay close-modal" aria-label="{@common.close}"></div>
                                <div class="modal-content">
                                    <span class="error big hide-modal close-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></span>
                                    <div class="modal-form"></div>
                                </div>
                            </div>
                        # ENDIF #
                    </div>
                </div>
                <div class="flex-between flex-between-large# IF blocks.items.C_EXEMPT # bgc notice# ENDIF #">
                    <div class="team-{blocks.items.HOME_ID} flex-between sm-width-pc-100 md-width-pc-50">
                        <div class="game-team home-team cell-pad flex-team flex-right sm-width-pc-80# IF blocks.items.C_HOME_FAV # text-strong# ENDIF #">
                            <span><a href="{blocks.items.U_HOME_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload">{blocks.items.HOME_TEAM}</a></span>
                            # IF blocks.items.C_HAS_HOME_LOGO #<img src="{blocks.items.HOME_LOGO}" alt="{blocks.items.HOME_TEAM}"># ENDIF #
                        </div>
                        <div id="home-{blocks.items.GAME_ID}" class="game-score home-score cell-pad sm-width-pc-20"># IF blocks.items.C_HAS_PEN #<span class="small">({blocks.items.HOME_PEN}) </span># ENDIF #{blocks.items.HOME_SCORE}</div>
                    </div>
                    <div id="vs-{blocks.items.GAME_ID}" class="hidden-small-screens">-</div>
                    <div class="team-{blocks.items.AWAY_ID} flex-between sm-width-pc-100 md-width-pc-50 invert-team">
                        <div id="away-{blocks.items.GAME_ID}" class="game-score away-score cell-pad sm-width-pc-20">{blocks.items.AWAY_SCORE}# IF blocks.items.C_HAS_PEN # <span class="small">({blocks.items.AWAY_PEN})</span># ENDIF #</div>
                        <div class="game-team away-team cell-pad flex-team flex-left sm-width-pc-80# IF blocks.items.C_AWAY_FAV # text-strong# ENDIF #">
                            # IF blocks.items.C_HAS_AWAY_LOGO #<img src="{blocks.items.AWAY_LOGO}" alt="{blocks.items.AWAY_TEAM}"># ENDIF #
                            <span><a href="{blocks.items.U_AWAY_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload">{blocks.items.AWAY_TEAM}</a></span>
                        </div>
                    </div>
                </div>
            </div>
        # END blocks.items #
    </div>
# END blocks #