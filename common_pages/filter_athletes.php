<?php

     require_once "../session_check.php";
    include "../config.php";
    $user= $_SESSION['user'];

    $dept=isset($_GET['dept'])? explode(',',$_GET['dept']):[];
    $events=isset($_GET['event'])? explode(',',$_GET['event']):[];
    $cat=isset($_GET['category'])? explode(',',$_GET['category']):[];

    $where=[];
    $params=[];

    if(!empty($dept[0])){
        $in=str_repeat('?',count($dept)-1).'?';
        $where="d.dept_id IN($in)";
        $params=array_merge($params,$dept);
    }
    if(!empty($event[0]))   {
        $in=str_repeat('?',count($dept)-1).'?';
        $where="e.event_id IN($in)";
        $params=array_merge($params,$event);
    }
    if(!empty($cat[0])){
        $in=str_repeat('?',count($cat)-1).'?';
        $where="c.category_id IN($in)";
        $params=array_merge($params,$cat);
    }
    $whereSql = $where ? "WHERE " . implode(' AND ', $where) : "";
    
?>