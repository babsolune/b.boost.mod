<script>
jQuery(document).ready(function() 
{
    const
        looser = ${escapejs(@football.bracket.looser)},
        winner = ${escapejs(@football.bracket.winner)},
        groups = ${escapejs(@football.bracket.from.groups)},
        Htag    = ${escapejs(@football.bracket.hour.tag)},
        hourly = (n) => {
            return '<span class="small pinned custom-author">' + Htag + ' ' + n + '</span>';
        };

    // Matches form
    // Looser bracket
    # IF C_LOOSER_BRACKET #
        jQuery('.form-L31 > div').prepend(hourly(1)).append('<br />3|4 ' + groups);
        jQuery('.form-L32 > div').prepend(hourly(1)).append('<br />3|4 ' + groups);
        jQuery('.form-L33 > div').prepend(hourly(2)).append('<br />3|4 ' + groups);
        jQuery('.form-L34 > div').prepend(hourly(2)).append('<br />3|4 ' + groups);

        jQuery('.form-L21 > div').prepend(hourly(6)).append('9/12<br />+ L31 | L32');
        jQuery('.form-L22 > div').prepend(hourly(6)).append('9/12<br />+ L33 | L34');
        jQuery('.form-L23 > div').prepend(hourly(5)).append('13/16<br />- L31 | L32');
        jQuery('.form-L24 > div').prepend(hourly(5)).append('13/16<br />- L33 | L34');

        jQuery('.form-L11 > div').prepend(hourly(10)).append('9/10<br />+ L21 | L22');
        jQuery('.form-L12 > div').prepend(hourly(10)).append('11/12<br />- L21 | L22');
        jQuery('.form-L13 > div').prepend(hourly(9)).append('13/14<br />+ L23 | L24');
        jQuery('.form-L14 > div').prepend(hourly(9)).append('15/16<br />- L23 | L24');
    # ENDIF #

    // Winner bracket
    jQuery('.form-W31 > div').prepend(hourly(# IF C_LOOSER_BRACKET #3# ELSE #1# ENDIF #)).append('<br />1|2 ' + groups);
    jQuery('.form-W32 > div').prepend(hourly(# IF C_LOOSER_BRACKET #3# ELSE #1# ENDIF #)).append('<br />1|2 ' + groups);
    jQuery('.form-W33 > div').prepend(hourly(# IF C_LOOSER_BRACKET #4# ELSE #2# ENDIF #)).append('<br />1|2 ' + groups);
    jQuery('.form-W34 > div').prepend(hourly(# IF C_LOOSER_BRACKET #4# ELSE #2# ENDIF #)).append('<br />1|2 ' + groups);

    # IF C_LOOSER_BRACKET #
        jQuery('.form-W21 > div').prepend(hourly(8)).append('1/4<br />- W31 | W32');
        jQuery('.form-W22 > div').prepend(hourly(8)).append('1/4<br />- W33 | W34');
        jQuery('.form-W23 > div').prepend(hourly(7)).append('5/8<br />+ W31 | W32');
        jQuery('.form-W24 > div').prepend(hourly(7)).append('5/8<br />+ W33 | W34');
    # ELSE #
        jQuery('.form-W21 > div').prepend(hourly(3)).append('<br />+ W31 | W32');
        jQuery('.form-W22 > div').prepend(hourly(3)).append('<br />+ W33 | W34');
    # ENDIF #

    # IF C_LOOSER_BRACKET #
        jQuery('.form-W11 > div').prepend(hourly(12)).append('1/2<br />+ W21 | W22');
        jQuery('.form-W12 > div').prepend(hourly(12)).append('3/4<br />- W21 | W22');
        jQuery('.form-W13 > div').prepend(hourly(11)).append('5/6<br />+ W23 | W24');
        jQuery('.form-W14 > div').prepend(hourly(11)).append('7/8<br />- W23 | W24');
    # ELSE #
        jQuery('.form-W11 > div').prepend(hourly(# IF C_THIRD_PLACE #5# ELSE #4# ENDIF #)).append('# IF C_THIRD_PLACE #1/2# ENDIF #<br />+ W21 | W22');
        # IF C_THIRD_PLACE #jQuery('.form-W12 > div').prepend(hourly(4)).append('3/4<br />- W21 | W22');# ENDIF #
    # ENDIF #

    // Matches front
    // looser bracket
    # IF C_LOOSER_BRACKET #
        if(jQuery('.home-L31').text() == '') jQuery('.home-L31').append('3|4 ' + groups);
        if(jQuery('.away-L31').text() == '') jQuery('.away-L31').append('3|4 ' + groups);
        if(jQuery('.home-L32').text() == '') jQuery('.home-L32').append('3|4 ' + groups);
        if(jQuery('.away-L32').text() == '') jQuery('.away-L32').append('3|4 ' + groups);
        if(jQuery('.home-L33').text() == '') jQuery('.home-L33').append('3|4 ' + groups);
        if(jQuery('.away-L33').text() == '') jQuery('.away-L33').append('3|4 ' + groups);
        if(jQuery('.home-L34').text() == '') jQuery('.home-L34').append('3|4 ' + groups);
        if(jQuery('.away-L34').text() == '') jQuery('.away-L34').append('3|4 ' + groups);

        if(jQuery('.home-L21').text() == '') jQuery('.home-L21').append(winner + ' L31');
        if(jQuery('.away-L21').text() == '') jQuery('.away-L21').append(winner + ' L32');
        if(jQuery('.home-L22').text() == '') jQuery('.home-L22').append(winner + ' L33');
        if(jQuery('.away-L22').text() == '') jQuery('.away-L22').append(winner + ' L34');
        if(jQuery('.home-L23').text() == '') jQuery('.home-L23').append(looser + ' L31');
        if(jQuery('.away-L23').text() == '') jQuery('.away-L23').append(looser + ' L32');
        if(jQuery('.home-L24').text() == '') jQuery('.home-L24').append(looser + ' L33');
        if(jQuery('.away-L24').text() == '') jQuery('.away-L24').append(looser + ' L34');

        jQuery('#L11').find('.bracket-details span').first().prepend('9|10 - ');
        if(jQuery('.home-L11').text() == '') jQuery('.home-L11').append(winner + ' L21');
        if(jQuery('.away-L11').text() == '') jQuery('.away-L11').append(winner + ' L22');
        jQuery('#L12').find('.bracket-details span').first().prepend('11|12 - ');
        if(jQuery('.home-L12').text() == '') jQuery('.home-L12').append(looser + ' L21');
        if(jQuery('.away-L12').text() == '') jQuery('.away-L12').append(looser + ' L22');
        jQuery('#L13').find('.bracket-details span').first().prepend('13|14 - ');
        if(jQuery('.home-L13').text() == '') jQuery('.home-L13').append(winner + ' L23');
        if(jQuery('.away-L13').text() == '') jQuery('.away-L13').append(winner + ' L24');
        jQuery('#L14').find('.bracket-details span').first().prepend('15|16 - ');
        if(jQuery('.home-L14').text() == '') jQuery('.home-L14').append(looser + ' L23');
        if(jQuery('.away-L14').text() == '') jQuery('.away-L14').append(looser + ' L24');
    # ENDIF #

    // Winner bracket
    if(jQuery('.home-W31').text() == '') jQuery('.home-W31').append('1|2 ' + groups);
    if(jQuery('.away-W31').text() == '') jQuery('.away-W31').append('1|2 ' + groups);
    if(jQuery('.home-W32').text() == '') jQuery('.home-W32').append('1|2 ' + groups);
    if(jQuery('.away-W32').text() == '') jQuery('.away-W32').append('1|2 ' + groups);
    if(jQuery('.home-W33').text() == '') jQuery('.home-W33').append('1|2 ' + groups);
    if(jQuery('.away-W33').text() == '') jQuery('.away-W33').append('1|2 ' + groups);
    if(jQuery('.home-W34').text() == '') jQuery('.home-W34').append('1|2 ' + groups);
    if(jQuery('.away-W34').text() == '') jQuery('.away-W34').append('1|2 ' + groups);

    if(jQuery('.home-W21').text() == '') jQuery('.home-W21').append(winner + ' W31');
    if(jQuery('.away-W21').text() == '') jQuery('.away-W21').append(winner + ' W32');
    if(jQuery('.home-W22').text() == '') jQuery('.home-W22').append(winner + ' W33');
    if(jQuery('.away-W22').text() == '') jQuery('.away-W22').append(winner + ' W34');
    # IF C_LOOSER_BRACKET #
        if(jQuery('.home-W23').text() == '') jQuery('.home-W23').append(looser + ' W31');
        if(jQuery('.away-W23').text() == '') jQuery('.away-W23').append(looser + ' W32');
        if(jQuery('.home-W24').text() == '') jQuery('.home-W24').append(looser + ' W33');
        if(jQuery('.away-W24').text() == '') jQuery('.away-W24').append(looser + ' W34');
    # ENDIF #

    jQuery('#W11').find('.bracket-details span').first().prepend('1|2 - ');
    if(jQuery('.home-W11').text() == '') jQuery('.home-W11').append(winner + ' W21');
    if(jQuery('.away-W11').text() == '') jQuery('.away-W11').append(winner + ' W22');
    # IF C_THIRD_PLACE #
        jQuery('#W12').find('.bracket-details span').first().prepend('3|4 - ');
        if(jQuery('.home-W12').text() == '') jQuery('.home-W12').append(looser + ' W21');
        if(jQuery('.away-W12').text() == '') jQuery('.away-W12').append(looser + ' W22');
    # ENDIF #
    # IF C_LOOSER_BRACKET #
        jQuery('#W12').find('.bracket-details span').first().prepend('3|4 - ');
        if(jQuery('.home-W12').text() == '') jQuery('.home-W12').append(looser + ' W21');
        if(jQuery('.away-W12').text() == '') jQuery('.away-W12').append(looser + ' W22');
        jQuery('#W13').find('.bracket-details span').first().prepend('5|6 - ');
        if(jQuery('.home-W13').text() == '') jQuery('.home-W13').append(winner + ' W23');
        if(jQuery('.away-W13').text() == '') jQuery('.away-W13').append(winner + ' W24');
        jQuery('#W14').find('.bracket-details span').first().prepend('7|8 - ');
        if(jQuery('.home-W14').text() == '') jQuery('.home-W14').append(looser + ' W23');
        if(jQuery('.away-W14').text() == '') jQuery('.away-W14').append(looser + ' W24');
    # ENDIF #
});
</script>