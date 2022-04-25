<?php
require_once('op_lib.php');
function  pagination($table,$limit,$offset,$order_by = 'id ASC', $where = null)
{
		try {
			if($where != null){
				$whereNew = array();
				foreach($where as $key=>$value) 
				{
					$whereNew[] = "$key = '$value'";
				}
				$impArr = implode('and ', $whereNew);
			}
			// Find out how many items are in the table
			if($where == null){
		        $sql = "SELECT * FROM $table WHERE status='ACTIVE'";
		    }else{
			  $sql = "SELECT * FROM $table WHERE $impArr";
			}
			  $total = direct_sql($sql)['count'];
			// How many items to list per page
			// $limit = 20;
		
			// How many pages will there be
			$pages = ceil($total / $limit);
		
			// What page are we currently on?
			$page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
				'options' => array(
					'default'   => 1,
					'min_range' => 1,
				),
			)));
		
			// Calculate the offset for the query
			$offset = ($page - 1)  * $limit;
		
			// Some information to display to the user
			$start = $offset + 1;
			$end = min(($offset + $limit), $total);
		
			// The "back" link
			//  $prevlink = ($page > 1) ? '<a href="?page=1" title="First page">&laquo;</a> <a href="?page=' . ($page - 1) . '" title="Previous page">&lsaquo;</a>' : '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';
			 $res['pre'] = ($page > 1) ? '<a class="page-link" href="?page='. ($page - 1) .'" tabindex="-1">Prev</a>' : '<span class="page-link invisible">Prev</span>';
			// The "forward" link
			//  $nextlink = ($page < $pages) ? '<a href="?page=' . ($page + 1) . '" title="Next page">&rsaquo;</a> <a href="?page=' . $pages . '" title="Last page">&raquo;</a>' : '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';
			 $res['next'] = ($page < $pages) ?  '<a class="page-link" href="?page=' . ($page + 1) . '">Next</a>' : '<span class="page-link invisible">Next</span>';
			// Display the paging information
			$res['pages'] = '';
			if($page+3<=$pages)
			{
				$lpage = $page+3;
			}
			else{
				$lpage = $pages;
			}
			for($i = $page-3; $i<= $lpage;  $i++ ){
				if($i >= 1){
				$active ='';
				if($i == $page){
					$active = 'active';
				}
				$res['pages'] .=  "<li class='page-item $active'><a class='page-link' href='?page=$i'>$i</a></li>";
			  }
			}
			// echo '<div id="paging"><p>', $prevlink, ' Page ', $page, ' of ', $pages, ' pages, displaying ', $start, '-', $end, ' of ', $total, ' results ', $nextlink, ' </p></div>';
		      
			// Prepare the paged query
			if($where == null){
		        $sql = "SELECT * FROM $table WHERE status='ACTIVE' ORDER BY $order_by LIMIT $limit OFFSET $offset";
		    }else{
			    $sql = "SELECT * FROM $table WHERE $impArr ORDER BY $order_by LIMIT $limit OFFSET $offset";
			}
			$tblData = direct_sql($sql)['data'];
		    $res['data'] = $tblData;
		    $res['count'] = $total;
			// 	// Display the results
				// foreach ($tblData as $row) {
				// 	echo '<p>', $row['state_name'], '</p>';
				// }
		    return $res;
		} catch (Exception $e) {
			echo '<p>', $e->getMessage(), '</p>';
		}
	}
function number_format_short( $n, $precision = 1 ) {
    if ($n < 900) {
        // 0 - 900
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        // 0.9k-850k
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        // 0.9m-850m
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        // 0.9b-850b
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        // 0.9t+
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
    }
	return $n_format. $suffix;
}
?>

