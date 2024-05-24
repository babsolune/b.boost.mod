// local tournament
// @copyright   &copy; 2005-2023 perdant LHPBoost
// @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
// @author      Sebastien LATIGUE <babsolune@phpboost.com>
// @version     PHPBoost 6.0 - last update: 2023 10 07
// @since       PHPBoost 6.0 - 2023 10 07

jQuery(document).ready(function(){
    // Matches form
    jQuery('.LQ1 > div').append('<br />3A|4D');
    jQuery('.LQ2 > div').append('<br />3B|4C');
    jQuery('.LQ3 > div').append('<br />3C|4B');
    jQuery('.LQ4 > div').append('<br />3D|4A');

    jQuery('.LD1 > div').append(' - 13/16<br />perdant LQ1<br />perdant LQ2');
    jQuery('.LD2 > div').append(' - 13/16<br />perdant LQ3<br />perdant LQ4');
    jQuery('.LD3 > div').append(' - 9/12<br />gagnant LQ1<br />gagnant LQ2');
    jQuery('.LD4 > div').append(' - 9/12<br />gagnant LQ3<br />gagnant LQ4');

    jQuery('.LF1 > div').append(' - 15/16<br />perdant LD1<br />perdant LD2');
    jQuery('.LF2 > div').append(' - 13/14<br />gagnant LD1<br />gagnant LD2');
    jQuery('.LF3 > div').append(' - 11/12<br />perdant LD3<br />perdant LD4');
    jQuery('.LF4 > div').append(' - 9/10<br />gagnant LD3<br />gagnant LD4');

    jQuery('.WQ1 > div').append('<br />1A|2D');
    jQuery('.WQ2 > div').append('<br />1B|2C');
    jQuery('.WQ3 > div').append('<br />1C|2B');
    jQuery('.WQ4 > div').append('<br />1D|2A');

    jQuery('.WD1 > div').append(' - 5/8<br />perdant WQ1<br />perdant WQ2');
    jQuery('.WD2 > div').append(' - 5/8<br />perdant WQ3<br />perdant WQ4');
    jQuery('.WD3 > div').append(' - 1/4<br />gagnant WQ1<br />gagnant WQ2');
    jQuery('.WD4 > div').append(' - 1/4<br />gagnant WQ3<br />gagnant WQ4');

    jQuery('.WF1 > div').append(' - 7/8<br />perdant WD1<br />perdant WD2');
    jQuery('.WF2 > div').append(' - 5/6<br />gagnant WD1<br />gagnant WD2');
    jQuery('.WF3 > div').append(' - 3/4<br />perdant WD3<br />perdant WD4');
    jQuery('.WF4 > div').append(' - 1/2<br />gagnant WD3<br />gagnant WD4');

    // Matches front
    if(jQuery('.home-L31').text() == '') jQuery('.home-L31').append('3e du groupe A');
    if(jQuery('.away-L31').text() == '') jQuery('.away-L31').append('4e du groupe D');
    if(jQuery('.home-L32').text() == '') jQuery('.home-L32').append('3e du groupe B');
    if(jQuery('.away-L32').text() == '') jQuery('.away-L32').append('4e du groupe C');
    if(jQuery('.home-L33').text() == '') jQuery('.home-L33').append('3e du groupe C');
    if(jQuery('.away-L33').text() == '') jQuery('.away-L33').append('4e du groupe B');
    if(jQuery('.home-L34').text() == '') jQuery('.home-L34').append('3e du groupe D');
    if(jQuery('.away-L34').text() == '') jQuery('.away-L34').append('4e du groupe A');

    if(jQuery('.home-L21').text() == '') jQuery('.home-L21').append('perdant L31');
    if(jQuery('.away-L21').text() == '') jQuery('.away-L21').append('perdant L32');
    if(jQuery('.home-L22').text() == '') jQuery('.home-L22').append('perdant L33');
    if(jQuery('.away-L22').text() == '') jQuery('.away-L22').append('perdant L34');
    if(jQuery('.home-L23').text() == '') jQuery('.home-L23').append('gagnant L31');
    if(jQuery('.away-L23').text() == '') jQuery('.away-L23').append('gagnant L32');
    if(jQuery('.home-L24').text() == '') jQuery('.home-L24').append('gagnant L33');
    if(jQuery('.away-L24').text() == '') jQuery('.away-L24').append('gagnant L34');

    if(jQuery('.home-L11').text() == '') jQuery('.home-L11').append('perdant L21');
    if(jQuery('.away-L11').text() == '') jQuery('.away-L11').append('perdant L22');
    if(jQuery('.home-L12').text() == '') jQuery('.home-L12').append('gagnant L21');
    if(jQuery('.away-L12').text() == '') jQuery('.away-L12').append('gagnant L22');
    if(jQuery('.home-L13').text() == '') jQuery('.home-L13').append('perdant L23');
    if(jQuery('.away-L13').text() == '') jQuery('.away-L13').append('perdant L24');
    if(jQuery('.home-L14').text() == '') jQuery('.home-L14').append('gagnant L23');
    if(jQuery('.away-L14').text() == '') jQuery('.away-L14').append('gagnant L24');

    if(jQuery('.home-W31').text() == '') jQuery('.home-W31').append('1e du groupe A');
    if(jQuery('.away-W31').text() == '') jQuery('.away-W31').append('2e du groupe D');
    if(jQuery('.home-W32').text() == '') jQuery('.home-W32').append('1e du groupe B');
    if(jQuery('.away-W32').text() == '') jQuery('.away-W32').append('2e du groupe C');
    if(jQuery('.home-W33').text() == '') jQuery('.home-W33').append('1e du groupe C');
    if(jQuery('.away-W33').text() == '') jQuery('.away-W33').append('2e du groupe B');
    if(jQuery('.home-W34').text() == '') jQuery('.home-W34').append('1e du groupe D');
    if(jQuery('.away-W34').text() == '') jQuery('.away-W34').append('2e du groupe A');

    if(jQuery('.home-W21').text() == '') jQuery('.home-W21').append('perdant W31');
    if(jQuery('.away-W21').text() == '') jQuery('.away-W21').append('perdant W32');
    if(jQuery('.home-W22').text() == '') jQuery('.home-W22').append('perdant W33');
    if(jQuery('.away-W22').text() == '') jQuery('.away-W22').append('perdant W34');
    if(jQuery('.home-W23').text() == '') jQuery('.home-W23').append('gagnant W31');
    if(jQuery('.away-W23').text() == '') jQuery('.away-W23').append('gagnant W32');
    if(jQuery('.home-W24').text() == '') jQuery('.home-W24').append('gagnant W33');
    if(jQuery('.away-W24').text() == '') jQuery('.away-W24').append('gagnant W34');

    if(jQuery('.home-W11').text() == '') jQuery('.home-W11').append('perdant W21');
    if(jQuery('.away-W11').text() == '') jQuery('.away-W11').append('perdant W22');
    if(jQuery('.home-W12').text() == '') jQuery('.home-W12').append('gagnant W21');
    if(jQuery('.away-W12').text() == '') jQuery('.away-W12').append('gagnant W22');
    if(jQuery('.home-W13').text() == '') jQuery('.home-W13').append('perdant W23');
    if(jQuery('.away-W13').text() == '') jQuery('.away-W13').append('perdant W24');
    if(jQuery('.home-W14').text() == '') jQuery('.home-W14').append('gagnant W23');
    if(jQuery('.away-W14').text() == '') jQuery('.away-W14').append('gagnant W24');
});