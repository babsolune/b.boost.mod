<script>
jQuery(document).ready(function() 
{
    const
        looser = ${escapejs(@scm.bracket.looser)},
        winner = ${escapejs(@scm.bracket.winner)},
        groups = ${escapejs(@scm.bracket.from.groups)},
        Htag    = ${escapejs(@scm.bracket.hour.tag)},
        hourly = (n) => {
            return '<span class="small pinned custom-author">' + Htag + ' ' + n + '</span>';
        };

    function back_fill(id, h, html)
    {
        jQuery(id + '> div').prepend(hourly(h)).append(html);
    }
    // Games form
    // Looser bracket
    # IF C_LOOSER_BRACKET #
        # IF C_16_4 #
            back_fill('.form-G11', 1, ''); back_fill('.form-G21', 2, '');
            back_fill('.form-G12', 1, ''); back_fill('.form-G22', 2, '');
            back_fill('.form-G13', 5, ''); back_fill('.form-G23', 6, '');
            back_fill('.form-G14', 5, ''); back_fill('.form-G24', 6, '');
            back_fill('.form-G15', 9, ''); back_fill('.form-G25', 10, '');
            back_fill('.form-G16', 9, ''); back_fill('.form-G26', 10, '');
            back_fill('.form-G31', 3, ''); back_fill('.form-G41', 4, '');
            back_fill('.form-G32', 3, ''); back_fill('.form-G42', 4, '');
            back_fill('.form-G33', 7, ''); back_fill('.form-G43', 9, '');
            back_fill('.form-G34', 7, ''); back_fill('.form-G44', 9, '');
            back_fill('.form-G35', 11, ''); back_fill('.form-G45', 12, '');
            back_fill('.form-G36', 11, ''); back_fill('.form-G46', 12, '');

            back_fill('.form-L31', 1, '<br />3A|4D'); back_fill('.form-L32', 1, '<br />3B|4C');
            back_fill('.form-L33', 2, '<br />4A|3D'); back_fill('.form-L34', 2, '<br />4B|3C');

            back_fill('.form-L21', 6, '9/12<br />+ L31 | L32'); back_fill('.form-L22', 6, '9/12<br />+ L33 | L34');
            back_fill('.form-L23', 5, '13/16<br />- L31 | L32'); back_fill('.form-L24', 5, '13/16<br />- L33 | L34');

            back_fill('.form-L11', 10, '9/10<br />+ L21 | L22'); back_fill('.form-L12', 10, '1/12<br />- L21 | L22');
            back_fill('.form-L13', 9, '13/14<br />+ L23 | L24'); back_fill('.form-L14', 9, '15/16<br />- L23 | L24');
        # ENDIF #
    # ENDIF #

    // Winner bracket
    # IF C_24_4 #
        back_fill('.form-W41', 2, '<br />1B|3A/D/E/F');
        back_fill('.form-W42', 1, '<br />1A|2C');
        back_fill('.form-W43', 3, '<br />1F|3A/B/C');
        back_fill('.form-W44', 3, '<br />2D|2E');
        back_fill('.form-W45', 4, '<br />1E|3A/B/C');
        back_fill('.form-W46', 4, '<br />1D|2F');
        back_fill('.form-W47', 1, '<br />1C|3D/E/F');
        back_fill('.form-W48', 2, '<br />2B|2A');

        back_fill('.form-W31', 5, '<br />+ W41|W42');
        back_fill('.form-W32', 5, '<br />+ W43|W44');
        back_fill('.form-W33', 6, '<br />+ W45|W46');
        back_fill('.form-W34', 6, '<br />+ W47|W48');

        back_fill('.form-W21', 7, '<br />+ W31 | W32');
        back_fill('.form-W22', 8, '<br />+ W33 | W34');

        back_fill('.form-W11', 9, '<br />+ W21 | W22');
    # ENDIF #
    # IF C_16_4 #
        back_fill('.form-W31', # IF C_LOOSER_BRACKET #3# ELSE #1# ENDIF #, '<br />1A|2D'); back_fill('.form-W32', # IF C_LOOSER_BRACKET #3# ELSE #1# ENDIF #, '<br />1B|2C');
        back_fill('.form-W33', # IF C_LOOSER_BRACKET #4# ELSE #2# ENDIF #, '<br />2A|1D'); back_fill('.form-W34', # IF C_LOOSER_BRACKET #4# ELSE #2# ENDIF #, '<br />1C|2B');

        # IF C_LOOSER_BRACKET #
            back_fill('.form-W21', 8, '1/4<br />- W31 | W32'); back_fill('.form-W22', 8, '1/4<br />- W33 | W34');
            back_fill('.form-W23', 7, '1/4<br />+ W31 | W32'); back_fill('.form-W24', 7, '1/4<br />+ W33 | W34');
        # ELSE #
            back_fill('.form-W21', 3, '<br />+ W31 | W32'); back_fill('.form-W22', 3, '<br />+ W33 | W34');
        # ENDIF #

        # IF C_LOOSER_BRACKET #
            back_fill('.form-W11', 12, '1/2<br />+ W21 | W22'); back_fill('.form-W12', 12, '3/4<br />- W21 | W22');
            back_fill('.form-W13', 11, '5/6<br />+ W21 | W22'); back_fill('.form-W14', 11, '7/8<br />- W21 | W22');
        # ELSE #
            back_fill('.form-W11', # IF C_THIRD_PLACE #5# ELSE #4# ENDIF #, '# IF C_THIRD_PLACE #1/2# ENDIF #<br />+ W21 | W22');
            # IF C_THIRD_PLACE #back_fill('.form-W12', 4, '3/4<br />- W21 | W22');# ENDIF #
        # ENDIF #
    # ENDIF #

    // Games front
    function front_fill(id, text)
    {
        if (jQuery(id).children().length == 0) {
            jQuery(id).append('<span>' + text + '</span>');
        }
        if (jQuery(id).children('.bt-content').length) {
            if (jQuery(id).children('.bt-content').children().length == 0) {
                jQuery(id).find('.bt-content').append('<span>' + text + '</span>');
            }
        }
    }
    // looser bracket
    # IF C_LOOSER_BRACKET #
        # IF C_16_4 #
            front_fill('.home-L31', '3A'); front_fill('.away-L31', '4D');
            front_fill('.home-L32', '3B'); front_fill('.away-L32', '4C');
            front_fill('.home-L33', '3D'); front_fill('.away-L33', '4A');
            front_fill('.home-L34', '3C'); front_fill('.away-L34', '4B');

            front_fill('.home-L21', winner + ' L31'); front_fill('.away-L21', winner + ' L32');
            front_fill('.home-L22', winner + ' L33'); front_fill('.away-L22', winner + ' L34');
            front_fill('.home-L23', looser + ' L31'); front_fill('.away-L23', looser + ' L32');
            front_fill('.home-L24', looser + ' L33'); front_fill('.away-L24', looser + ' L34');

            jQuery('#L11').find('.bracket-details span').first().prepend('9|10 - ');
            front_fill('.home-L11', winner + ' L21'); front_fill('.away-L11', winner + ' L22');
            jQuery('#L12').find('.bracket-details span').first().prepend('11|12 - ');
            front_fill('.home-L12', looser + ' L23'); front_fill('.away-L12', looser + ' L24');
            jQuery('#L13').find('.bracket-details span').first().prepend('13|14 - ');
            front_fill('.home-L13', winner + ' L21'); front_fill('.away-L13', winner + ' L22');
            jQuery('#L14').find('.bracket-details span').first().prepend('15|16 - ');
            front_fill('.home-L14', looser + ' L23'); front_fill('.away-L14', looser + ' L24');
        # ENDIF #
    # ENDIF #

    // Winner bracket
    # IF C_24_4 #
        front_fill('.home-W41', '1B'); front_fill('.away-W41', '3A/D/E/F');
        front_fill('.home-W42', '1A'); front_fill('.away-W42', '2C');
        front_fill('.home-W43', '1F'); front_fill('.away-W43', '3A/B/C');
        front_fill('.home-W44', '2D'); front_fill('.away-W44', '2E');
        front_fill('.home-W45', '1E'); front_fill('.away-W45', '3A/B/C/D');
        front_fill('.home-W46', '1D'); front_fill('.away-W46', '2F');
        front_fill('.home-W47', '1C'); front_fill('.away-W47', '3D/E/F');
        front_fill('.home-W48', '2B'); front_fill('.away-W48', '2A');
        jQuery('.home-team, .away-team').each(function(){
            front_fill(jQuery(this), '&nbsp;');
        })
    # ENDIF #
    # IF C_16_4 #
        front_fill('.home-W31', '1A'); front_fill('.away-W31', '2D');
        front_fill('.home-W32', '1B'); front_fill('.away-W32', '2C');
        front_fill('.home-W33', '1D'); front_fill('.away-W33', '2A');
        front_fill('.home-W34', '1C'); front_fill('.away-W34', '2B');

        front_fill('.home-W21', winner + ' W31'); front_fill('.away-W21', winner + ' W32');
        front_fill('.home-W22', winner + ' W33'); front_fill('.away-W22', winner + ' W34');
        # IF C_LOOSER_BRACKET #
            front_fill('.home-W23', looser + ' W31'); front_fill('.away-W23', looser + ' W32');
            front_fill('.home-W24', looser + ' W33'); front_fill('.away-W24', looser + ' W34');
        # ENDIF #

        jQuery('#W11').find('.bracket-details span').first().prepend('1|2 - ');
        front_fill('.home-W11', winner + ' W21'); front_fill('.away-W11', winner + ' W22');
        # IF C_THIRD_PLACE #
            jQuery('#W12').find('.bracket-details span').first().prepend('3|4 - ');
            front_fill('.home-W12', looser + ' W21'); front_fill('.away-W12', looser + ' W22');
        # ENDIF #
        # IF C_LOOSER_BRACKET #
            jQuery('#W12').find('.bracket-details span').first().prepend('3|4 - ');
            front_fill('.home-W12', looser + ' W21'); front_fill('.away-W12', looser + ' W22');
            jQuery('#W13').find('.bracket-details span').first().prepend('5|6 - ');
            front_fill('.home-W13', winner + ' W23'); front_fill('.away-W13', winner + ' W24');
            jQuery('#W14').find('.bracket-details span').first().prepend('7|8 - ');
            front_fill('.home-W14', looser + ' W23'); front_fill('.away-W14', looser + ' W24');
        # ENDIF #
    # ENDIF #
});
</script>