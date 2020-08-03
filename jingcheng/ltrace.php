<?php
 $y = '1380';
 $arr = array();
  for($i = 0; $i < 2000; $i ++){
	     $arr[] = "{$i}"; //故意用引号包起来设成字符串
		  }
  for($i = 0; $i < 2000; $i ++){
	     if(!in_array($y, $arr)) continue;
		  }

