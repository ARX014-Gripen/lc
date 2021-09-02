<?php
return [
    'nextActive' => '<a class="pagination-next has-text-weight-bold" href="{{url}}">{{text}}</a>',
    'nextDisabled' => '<a class="pagination-next has-text-weight-bold" href="{{url}}">{{text}}</a>',
    'prevActive' => '<a class="pagination-previous has-text-weight-bold" href="{{url}}">{{text}}</a>',
    'prevDisabled' => '<a class="pagination-previous has-text-weight-bold" href="{{url}}">{{text}}</a>',
    'first' => '<a class="pagination-previous has-text-weight-bold" href="{{url}}">{{text}}</a>',
    'last' => '<a class="pagination-previous has-text-weight-bold" href="{{url}}">{{text}}</a>',
    'number' => '<a class="pagination-link has-text-weight-bold" href="{{url}}">{{text}}</a>',
    'current' => '<a class="pagination-link is-current has-text-weight-bold" href="{{url}}">{{text}}</a>',
];
?>

<!-- 

nextActive next() によって生成されたリンクの有効な状態。

nextDisabled next() の無効な状態。

prevActive prev() によって生成されたリンクの有効な状態。

prevDisabled prev() の無効な状態。

counterRange counter() で format == range の場合のテンプレート。

counterPages counter() で format == pages の場合のテンプレート。

first first() によって生成されたリンクに使用されるテンプレート。

last last() によって生成されたリンクに使用されるテンプレート。

number numbers() によって生成されたリンクに使用されるテンプレート。

current 現在のページで使用されているテンプレート。

ellipsis numbers() によって生成された省略記号に使用されるテンプレート。

sort 方向のないソートリンクのテンプレート。

sortAsc 昇順のソートリンクのテンプレート。

sortDesc 降順のソートリンクのテンプレート。

 -->