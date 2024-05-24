<div id="{MATCH_ID}" class="finals-match">
    <div class="finals-details bgc link-color small text-italic">
        <span>{PLAYGROUND}</span>
        <span># IF C_ONE_DAY #{MATCH_DATE_HOUR_MINUTE}# ELSE #{MATCH_DATE_FULL}# ENDIF #</span>
        <span>{MATCH_ID}</span>
    </div>
    <div class="finals-team finals-home"# IF C_HOME_WIN # style="background-color: {WIN_COLOR}"# ENDIF #>
        <div class="id-{HOME_ID} home-{MATCH_ID} home-team width-70">{HOME_TEAM}</div>
        <div class="home-score width-30 align-center">{HOME_SCORE}# IF C_HAS_PEN # <span class="small">({HOME_PEN})</span># ENDIF #</div>
    </div>
    <div class="finals-team finals-away"# IF C_AWAY_WIN # style="background-color: {WIN_COLOR}"# ENDIF #>
        <div class="id-{AWAY_ID} away-{MATCH_ID} away-team width-70">{AWAY_TEAM}</div>
        <div class="away-score width-30 align-center">{AWAY_SCORE}# IF C_HAS_PEN # <span class="small">({AWAY_PEN})</span># ENDIF #</div>
    </div>
</div>