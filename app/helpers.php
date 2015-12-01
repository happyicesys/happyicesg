<?php

 function sort_person($column, $body)
 {
    $direction = (Request::get('direction') == 'asc') ? 'desc' : 'asc';

    return link_to_route('transaction.index', $body, ['sortBy' => $column, 'direction' => $direction]);
 }