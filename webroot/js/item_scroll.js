$(function(){
    $('.item-list').infinitescroll({
        navSelector     : '.pagination',
        nextSelector    : '.pagination a',
        itemSelector    : '.item',
        loading: {
            msgText: "読み込み中",
            finishedMsg: 'これ以上商品はありません。'       
    }
    },function(newElements){
        $(newElements).hide().delay(100).fadeIn(600);
        $(".pagination").css('margin-top','10px').addClass("columns").addClass("is-centered").appendTo(".item-list").delay(300).fadeIn(600);
    });
     
    $('.item-list').infinitescroll('unbind');
    $(".pagination a").click(function(){
        $('.item-list').infinitescroll('retrieve');
        return false;
    });
});