
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
                beforeSend: function(returnData) {
                    jQuery.each(returnData, function(index, game) {
                        jQuery('#vs-' + returnData.game_id).html('<i class="fa fa-spin fa-spinner"></i>');
                    });
                },
                success: function(returnData) {
                    jQuery.each(returnData, function(index, game) {
                        jQuery('#vs-' + returnData.game_id).html('-');
                        jQuery('#home-' + game.game_id).html(game.home_score);
                        jQuery('#away-' + game.game_id).html(game.away_score);
                    });
                },
                error: function(e) {
                    // jQuery('body').addClass('bgc-full error');
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
                    const penaltyFields = game_type === 'B'
                        ? (
                            '<label class="label-sup grouped-element" for="home_pen_' + game.game_id + '">' +
                                '<span>Penalties locaux</span>' +
                                '<input class="align-center" type="number" min="0" id="home_pen_' + game.game_id + '" name="home_pen" value="' + game.home_pen + '" placeholder="{@scm.game.event.penalties}">' +
                            '</label>' +
                            '<label class="label-sup grouped-element" for="away_pen_' + game.game_id + '">' +
                                '<span>Penalties visiteurs</span>' +
                                '<input class="align-center" type="number" min="0" id="away_pen_' + game.game_id + '" name="away_pen" value="' + game.away_pen + '" placeholder="{@scm.game.event.penalties}">' +
                            '</label>'
                        )
                        : ''
                    ;
                    const score_form = jQuery('#score-panel-' + game.game_id + ' .modal-form');
                    score_form.append(
                        '<form class="grouped-inputs inputs-with-sup" method="post" action="{REWRITED_SCRIPT}">' +
                            '<label style="min-width: 220px !important;" class="label-sup grouped-element" for="date_' + game.game_id + '">' +
                                '<span>Date/heure</span>' +
                                '<input type="datetime-local" id="date_' + game.game_id + '" name="date" value="' + game.date + '">' +
                            '</label>' +
                            '<label class="label-sup grouped-element" for="playground_' + game.game_id + '">' +
                                '<span>Terrain</span>' +
                                '<input class="align-center" type="text" id="playground_' + game.game_id + '" name="playground" value="' + game.playground + '" placeholder="terrain">' +
                            '</label>' +
                            '<label class="label-sup grouped-element" for="home_name_' + game.game_id + '">' +
                                '<span>Locaux</span>' +
                                '<select id="home_name_' + game.game_id + '" name="home_id"></select>' +
                            '</label>' +
                            '<label class="label-sup grouped-element" for="home_score_' + game.game_id + '">' +
                                '<span>Score locaux</span>' +
                                '<input class="align-center" type="number" min="0" id="home_score_' + game.game_id + '" name="home_score" value="' + game.home_score + '" placeholder="score">' +
                            '</label>' +
                            penaltyFields +
                            '<label class="label-sup grouped-element" for="away_score_' + game.game_id + '">' +
                                '<span>Score visiteurs</span>' +
                                '<input class="align-center" type="number" min="0" id="away_score_' + game.game_id + '" name="away_score" value="' + game.away_score + '" placeholder="score">' +
                            '</label>' +
                            '<label class="label-sup grouped-element" for="away_name_' + game.game_id + '">' +
                                '<span>Visiteurs</span>' +
                                '<select id="away_name_' + game.game_id + '" name="away_id"></select>' +
                            '</label>' +
                            '<input type="hidden" name="token" value="{TOKEN}" />' +
                            '<input type="hidden" name="event_id" value="{EVENT_ID}" />' +
                            '<button type="button" class="button submit" onclick="validate_score(\'' + game_type + '\', \'' + game_cluster + '\', \'' + game_round + '\', \'' + game_order + '\')">Valider</button>' +
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
    <div class="cell-vertical">
        # IF NOT C_ONE_DAY #
            <h5>
                # IF blocks.C_ROUND #{@scm.round}# ENDIF #
                {blocks.TITLE}
            </h5>
        # ENDIF #
        # START blocks.sub_blocks #
            # IF blocks.sub_blocks.C_SUB_ROUND ## IF blocks.sub_blocks.C_SEVERAL_DATES #<h6>{blocks.sub_blocks.SUB_TITLE}</h6># ENDIF ## ENDIF #
            # START blocks.sub_blocks.items #
                <div id="game-{blocks.sub_blocks.items.GAME_ID}" class="cell cell-game">
                    <div class="flex-between small">
                        <time class="sm-width-pc-30 cell-gap">{blocks.sub_blocks.items.GAME_DATE_HOUR_MINUTE}</time>
                        # IF blocks.sub_blocks.items.C_STATUS #
                            <div class="sm-width-pc-40 smaller text-italic align-center bgc notice">{blocks.sub_blocks.items.STATUS}</div>
                        # ENDIF #
                        <div class="sm-width-pc-30 cell-gap modal-container align-right" aria-label="{@scm.game.event.details}">
                            <span class="modal-button --target-panel-{blocks.sub_blocks.items.GAME_ID}">
                                # IF blocks.sub_blocks.items.C_HAS_DETAILS #
                                    # IF blocks.sub_blocks.items.C_VIDEO #
                                        <i class="far fa-circle-play"></i>
                                    # ELSE #
                                        <i class="far fa-file-lines"></i>
                                    # ENDIF #
                                # ENDIF #
                            </span>
                            <div id="target-panel-{blocks.sub_blocks.items.GAME_ID}" class="modal">
                                <div class="modal-overlay close-modal" aria-label="{@common.close}"></div>
                                <div class="modal-content">
                                    <span class="error big hide-modal close-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></span>
                                    <div class="cell-flex cell-columns-2 cell-tile">
                                        <div class="home-team">
                                            <div class="cell-header flex-team">
                                                <h4 class="cell-name">
                                                    <a href="{blocks.sub_blocks.items.U_HOME_CLUB}" class="offload">{blocks.sub_blocks.items.HOME_TEAM}</a>
                                                </h4>
                                                # IF blocks.sub_blocks.items.C_HAS_HOME_LOGO #<img src="{blocks.sub_blocks.items.HOME_LOGO}" alt="{blocks.sub_blocks.items.HOME_TEAM}"># ENDIF #
                                            </div>
                                            <div class="cell-score bigger align-center">
                                                {blocks.sub_blocks.items.HOME_SCORE}
                                            </div>
                                            <div class="cell-details">{@scm.game.event.goals}</div>
                                            # START blocks.sub_blocks.items.home_goals #
                                                <div>
                                                    <span>{blocks.sub_blocks.items.home_goals.TIME}'</span>
                                                    <span>- {blocks.sub_blocks.items.home_goals.PLAYER}</span>
                                                </div>
                                            # END blocks.sub_blocks.items.home_goals #
                                            <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                            # START blocks.sub_blocks.items.home_yellow #
                                                <div>
                                                    <span>{blocks.sub_blocks.items.home_yellow.TIME}'</span>
                                                    <span>- {blocks.sub_blocks.items.home_yellow.PLAYER}</span>
                                                </div>
                                            # END blocks.sub_blocks.items.home_yellow #
                                            <div class="cell-details">{@scm.game.event.cards.red}</div>
                                            # START blocks.sub_blocks.items.home_red #
                                                <div>
                                                    <span>{blocks.sub_blocks.items.home_red.TIME}'</span>
                                                    <span>- {blocks.sub_blocks.items.home_red.PLAYER}</span>
                                                </div>
                                            # END blocks.sub_blocks.items.home_red #
                                        </div>
                                        <div class="away-team">
                                            <div class="cell-header flex-team">
                                                <h4 class="cell-name">
                                                    <a href="{blocks.sub_blocks.items.U_AWAY_CLUB}" class="offload">{blocks.sub_blocks.items.AWAY_TEAM}</a>
                                                </h4>
                                                # IF blocks.sub_blocks.items.C_HAS_AWAY_LOGO #<img src="{blocks.sub_blocks.items.AWAY_LOGO}" alt="{blocks.sub_blocks.items.AWAY_TEAM}"># ENDIF #
                                            </div>
                                            <div class="cell-score bigger align-center">
                                                {blocks.sub_blocks.items.AWAY_SCORE}
                                            </div>
                                            <div class="cell-details">{@scm.game.event.goals}</div>
                                            # START blocks.sub_blocks.items.away_goals #
                                                <div>
                                                    <span>{blocks.sub_blocks.items.away_goals.TIME}'</span>
                                                    <span>- {blocks.sub_blocks.items.away_goals.PLAYER}</span>
                                                </div>
                                            # END blocks.sub_blocks.items.away_goals #
                                            <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                            # START blocks.sub_blocks.items.away_yellow #
                                                <div>
                                                    <span>{blocks.sub_blocks.items.away_yellow.TIME}'</span>
                                                    <span>- {blocks.sub_blocks.items.away_yellow.PLAYER}</span>
                                                </div>
                                            # END blocks.sub_blocks.items.away_yellow #
                                            <div class="cell-details">{@scm.game.event.cards.red}</div>
                                            # START blocks.sub_blocks.items.away_red #
                                                <div>
                                                    <span>{blocks.sub_blocks.items.away_red.TIME}'</span>
                                                    <span>- {blocks.sub_blocks.items.away_red.PLAYER}</span>
                                                </div>
                                            # END blocks.sub_blocks.items.away_red #
                                        </div>
                                    </div>
                                    # IF blocks.sub_blocks.items.C_VIDEO #
                                        <a href="{blocks.sub_blocks.items.U_VIDEO}" class="button submit" target="blank" rel="noopener noreferer">
                                            <i class="far fa-circle-play"></i> {@scm.watch.video}
                                        </a>
                                    # ENDIF #
                                    <div class="flex-between flex-between-large">
                                        # IF blocks.sub_blocks.items.STADIUM #
                                            <div class="md-width-pc-50">
                                                <h5>{@scm.game.event.stadium}</h5>
                                                {blocks.sub_blocks.items.STADIUM}
                                            </div>
                                        # ENDIF #
                                        # IF blocks.sub_blocks.items.SUMMARY #
                                            <div class="md-width-pc-50">
                                                <h5>{@scm.game.event.summary}</h5>
                                                {blocks.sub_blocks.items.SUMMARY}
                                            </div>
                                        # ENDIF #
                                    </div>
                                </div>
                            </div>
                        </div>
                        # IF C_DISPLAY_PLAYGROUNDS #
                            <div class="sm-width-pc-100 md-width-pc-33">{@scm.field}: {blocks.sub_blocks.items.PLAYGROUND}</div>
                        # ELSE #
                            <div></div>
                        # ENDIF #
                        # IF C_CONTROLS #
                            <div class="sm-width-pc-30 cell-gap modal-container align-right" aria-label="{@scm.game.event.details}">
                                <span class="modal-button --score-panel-{blocks.sub_blocks.items.GAME_ID}"
                                    onclick="call_score('{blocks.sub_blocks.items.GAME_TYPE}', '{blocks.sub_blocks.items.GAME_CLUSTER}', '{blocks.sub_blocks.items.GAME_ROUND}', '{blocks.sub_blocks.items.GAME_ORDER}')">
                                    <i class="fa fa-gear"></i>
                                </span>
                                <div id="score-panel-{blocks.sub_blocks.items.GAME_ID}" class="modal">
                                    <div class="modal-overlay close-modal" aria-label="{@common.close}"></div>
                                    <div class="modal-content">
                                        <span class="error big hide-modal close-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></span>
                                        <div class="modal-form"></div>
                                    </div>
                                </div>
                            </div>
                        # ENDIF #
                    </div>
                    <div class="flex-between flex-between-large# IF blocks.sub_blocks.items.C_EXEMPT # bgc notice# ENDIF #">
                        <div class="team-{blocks.sub_blocks.items.HOME_ID} flex-between sm-width-pc-100 md-width-pc-50">
                            <div class="game-team home-team cell-pad flex-team flex-right sm-width-pc-80# IF blocks.sub_blocks.items.C_HOME_FAV # text-strong# ENDIF #">
                                <span>
                                    <a
                                        href="{blocks.sub_blocks.items.U_HOME_CALENDAR}"
                                        aria-label="{@scm.club.see.calendar}# IF blocks.sub_blocks.items.HOME_FORFEIT # - {@scm.game.event.forfeit}# ENDIF ## IF blocks.sub_blocks.items.HOME_GENERAL_FORFEIT # - {@scm.game.event.general.forfeit}# ENDIF #"
                                        class="offload# IF blocks.sub_blocks.items.C_HOME_FAV # text-strong# ENDIF ## IF blocks.sub_blocks.items.HOME_FORFEIT # warning# ENDIF ## IF blocks.sub_blocks.items.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #">
                                        {blocks.sub_blocks.items.HOME_TEAM}
                                    </a>
                                </span>
                                # IF blocks.sub_blocks.items.C_HAS_HOME_LOGO #<img src="{blocks.sub_blocks.items.HOME_LOGO}" alt="{blocks.sub_blocks.items.HOME_TEAM}"># ENDIF #
                            </div>
                            <div id="home-{blocks.sub_blocks.items.GAME_ID}" class="game-score home-score cell-pad sm-width-pc-20">{blocks.sub_blocks.items.HOME_SCORE}# IF blocks.sub_blocks.items.C_HAS_PEN # <span class="small">({blocks.sub_blocks.items.HOME_PEN})</span># ENDIF #</div>
                        </div>
                        <div id="vs-{blocks.sub_blocks.items.GAME_ID}" class="hidden-small-screens">-</div>
                        <div class="team-{blocks.sub_blocks.items.AWAY_ID} flex-between sm-width-pc-100 md-width-pc-50 invert-team">
                            <div id="away-{blocks.sub_blocks.items.GAME_ID}" class="game-score away-score cell-pad sm-width-pc-20">{blocks.sub_blocks.items.AWAY_SCORE}# IF blocks.sub_blocks.items.C_HAS_PEN # <span class="small">({blocks.sub_blocks.items.AWAY_PEN})</span># ENDIF #</div>
                            <div class="game-team away-team cell-pad flex-team flex-left sm-width-pc-80# IF blocks.sub_blocks.items.C_AWAY_FAV # text-strong# ENDIF #">
                                # IF blocks.sub_blocks.items.C_HAS_AWAY_LOGO #<img src="{blocks.sub_blocks.items.AWAY_LOGO}" alt="{blocks.sub_blocks.items.AWAY_TEAM}"># ENDIF #
                                <span>
                                    <a
                                        href="{blocks.sub_blocks.items.U_AWAY_CALENDAR}"
                                        aria-label="{@scm.club.see.calendar}# IF blocks.sub_blocks.items.AWAY_FORFEIT # - {@scm.game.event.forfeit}# ENDIF ## IF blocks.sub_blocks.items.AWAY_GENERAL_FORFEIT # - {@scm.game.event.general.forfeit}# ENDIF #"
                                        class="offload# IF blocks.sub_blocks.items.C_AWAY_FAV # text-strong# ENDIF ## IF blocks.sub_blocks.items.AWAY_FORFEIT # warning# ENDIF ## IF blocks.sub_blocks.items.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #">
                                        {blocks.sub_blocks.items.AWAY_TEAM}
                                    </a>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            # END blocks.sub_blocks.items #
        # END blocks.sub_blocks #
    </div>
# END blocks #