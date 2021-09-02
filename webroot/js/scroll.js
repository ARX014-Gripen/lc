$(function(){
    $('.order_list').infinitescroll({
        navSelector     : '.pagination',
        nextSelector    : '.pagination a',
        itemSelector    : '.order',
        loading: {
            msgText: "読み込み中",
            finishedMsg: 'これ以上注文はありません。'       
    }
    },function(newElements){
        $(newElements).hide().delay(100).fadeIn(600);
        $(".pagination").css('margin-top','10px').addClass("columns").addClass("is-centered").appendTo(".order_list").delay(300).fadeIn(600);
    });
     
    $('.order_list').infinitescroll('unbind');
    $(".pagination a").click(function(){
        $('.order_list').infinitescroll('retrieve');
        return false;
    });
});