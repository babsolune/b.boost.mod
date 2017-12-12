$('[ida=""]').remove();

$('[is_cat=1] a').click(function(event){
    event.preventDefault();
});

$('[is_cat=0]').addClass('articles');

$('[is_cat=1]').each(function(){
    $(this).clone().appendTo('#construct_nav').removeAttr('cats_article_id','cats_id_parent','cats_id').attr('is_cat','0').addClass('desc_articles');
    $(this).addClass('folder');
});

$('#construct_nav li').each(function(){
    var a=$(this).attr('cats_article_id');
    var b=$(this).attr('cats_id_parent');
    var c=$(this).attr('cats_id');
    var d=$(this).siblings('[cats_id="'+b+'"]').map(function(index){
        return $(this).attr('cats_article_id');
    }).get();

    if(b>0){
        $(this).attr('real_cats_id',d);
    }
});

$('[is_cat="1"]').each(function(){
    var e=$(this).attr('cats_id');
    var f=$(this).attr('cats_article_id');
    var g=$(this).attr('real_cats_id');
    $(this).append('<ul ul_cats_id="'+e+'" ul_real_cats_id="'+g+'" ul_cats_article_id="'+f+'"></ul>');
});

$('[cats_article_id]').each(function(){
    var h=$(this).attr('real_cats_id');
    $(this).prependTo($('[ul_cats_article_id="'+h+'"]'));
});

$('[is_cat=0]').each(function(){
    var i=$(this).attr('id_cat');
    $(this).prependTo($('[ul_cats_id="'+i+'"]'));
});

$('ul:empty').remove();
