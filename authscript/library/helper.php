<?php 
/**
 * Helper page
 *
 * @author Azfar Ahmed
 * @version 1.0
 * @date November 02, 2015
 * @EasyPhp MVC Framework
 * @website www.tutbuzz.com
 */
 
 class helper extends DBconfig {
	 
	 public function __construct()
	{
		$connection = new DBconfig();
		$this->connection = $connection->connectToDatabase();
	}
       function getUriSegments() {
           return explode("/", $_GET['params']);
       }
 
       function getUriSegment($n) {
          $segs = $this->getUriSegments();
          return count($segs)>0&&count($segs)>=($n-1)?$segs[$n]:'';
       }
	public function db_insert($array, $tbname) {
		$array_keys = array_keys($array);
		$array_keys = implode(", ", $array_keys);
		$array_values = implode("','", $array);
		$array_values = "'".$array_values."'";
		$query = "INSERT INTO $tbname ($array_keys) VALUES ($array_values)";
		if (mysqli_query($this->connection, $query)) {
			return mysqli_insert_id($this->connection);
		} else {
			return false;
		}
	}
	
	public function db_select($select, $tbname, $filter="") {
		$query = "SELECT $select FROM $tbname $filter";
		$result = $this->connection->query($query);
		return $result;
	}
        
        
        public function custom_query($select) {
		$query = "$select";
		$result = $this->connection->query($query);
		return $result;
	}
	
	public function db_update($array, $tbname, $where) {
		$keys = array_keys($array);
		$set = array();
		foreach($keys as $key) {
			$set[] = "$key = '$array[$key]' ";
		}
		$set = implode(", ", $set);
		$query = "UPDATE $tbname SET $set $where";
		if (mysqli_query($this->connection, $query)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function db_delete($tbname, $where) {
		$query = "DELETE FROM $tbname $where";
		if (mysqli_query($this->connection, $query)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function check($tbname, $where) {
		$query = "SELECT * FROM $tbname $where";
		$query_result = mysqli_query($this->connection, $query);
		if (mysqli_num_rows($query_result) > 0) {
			return true;
		} else {
			return false;
		}
	}
        
        public function pagination($query,$p,$prev,$next,$pageSlug,$max){
           // echo $query;
            $result = $this->connection->query($query); //echo $max. ' '.mysqli_num_rows($result) / $max;
            
            $totalposts = ceil(mysqli_num_rows($result) / $max);
            $lpm1 = (ceil($totalposts / $max)) - 1;
            
            $adjacents = 3;
            if($totalposts > 1)
            {
                $pagination .= "<center><div class='pagination_custom'>";
                //previous button
                if ($p > 1)
                $pagination.= "<a href='$pageSlug/$prev'><< Previous</a> ";
                else
                $pagination.= "<span class='disabled'><< Previous</span> ";
                
                if ($totalposts < 7 + ($adjacents * 2)){
                    for ($counter = 1; $counter <= $totalposts; $counter++){
                        if ($counter == $p)
                        $pagination.= "<span class='current'>$counter</span>";
                        else
                        $pagination.= " <a href='$pageSlug/$counter'>$counter</a> ";}
                }else if($totalposts > 5 + ($adjacents * 2)){
                    if($p < 1 + ($adjacents * 2)){
                        for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
                            if ($counter == $p)
                            $pagination.= " <span class='current'>$counter</span> ";
                            else
                            $pagination.= " <a href='$pageSlug/$counter'>$counter</a> ";
                        }
                        $pagination.= " ... ";
                     //   $pagination.= " <a href='$pageSlug/$lpm1'>$lpm1</a> ";
                        $pagination.= " <a href='$pageSlug/$totalposts'>$totalposts</a> ";
                    }
                    //in middle; hide some front and some back
                    elseif($totalposts - ($adjacents * 2) > $p && $p > ($adjacents * 2)){
                        $pagination.= " <a href='$pageSlug/1'>1</a> ";
                        $pagination.= " <a href='$pageSlug/2'>2</a> ";
                        $pagination.= " ... ";
                        for ($counter = $p - $adjacents; $counter <= $p + $adjacents; $counter++){
                            if ($counter == $p)
                            $pagination.= " <span class='current'>$counter</span> ";
                            else
                            $pagination.= " <a href='$pageSlug/$counter'>$counter</a> ";
                        }
                        $pagination.= " ... ";
                       // $pagination.= " <a href='$pageSlug/$lpm1'>$lpm1</a> ";
                        $pagination.= " <a href='$pageSlug/$totalposts'>$totalposts</a> ";
                    }else{
                        $pagination.= " <a href='$pageSlug/1'>1</a> ";
                        $pagination.= " <a href='$pageSlug/2'>2</a> ";
                        $pagination.= " ... ";
                        for ($counter = $totalposts - (2 + ($adjacents * 2)); $counter <= $totalposts; $counter++){
                            if ($counter == $p)
                            $pagination.= " <span class='current'>$counter</span> ";
                            else
                            $pagination.= " <a href='$pageSlug/$counter'>$counter</a> ";
                        }
                    }
                }
                if ($p < $counter - 1)
                $pagination.= " <a href='$pageSlug/$next'>Next >></a>";
                else
                $pagination.= " <span class='disabled'>Next >></span>";
                
                $pagination.= "</center>\n";
            }
            return $pagination;
        }
 }