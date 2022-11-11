<?php

class DbHandler {
    private $conn;
	
	private $stud_profile_pic_path='../uploads/';
    private $stud_profile_pic_url='http://localhost/student_demo/uploads/';
	
    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    public function fetchAllStudents()
    {
        $sql_query="CALL fetchAllStudents()"; //CALL PROCEDURE     
        $stmt = $this->conn->query($sql_query); // query fatch
        $this->conn->next_result();            // privous stement repeat for fatching multiple query
        $list=array();
        while ( $row = $stmt->fetch_assoc()) {      // fetching data as group           
            $list[]=$row;
        }

        $stmt->close();

        if (count($list)>0)
        {
            $result=array(
                'success'=>true,
                'student_list'=>$list
            );
        }
        else
        {
            $result=array(
                'success'=>false,
                'message'=>NOT_FOUND
            );
        }
        return $result;
    }

    public function getStudent($id)
    {
        $sql_query="CALL getStudent($id)";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $list=array();
        while ( $row = $stmt->fetch_assoc()) {
            $list[]=array(
                'name'=>$row['name'],
                'dept'=>$row['dept'],
               // 'mobile'=>$row['mobile'],
                'image'=>$this->getPhoto($row['image'])
            );
        }

        $stmt->close();

        if (count($list)>0)
        {
            $result=array(
                'success'=>true,
                'student_list'=>$list
            );
        }
        else
        {
            $result=array(
                'success'=>false,
                'message'=>NOT_FOUND
            );
        }
        return $result;
    }

    public function getStudentImages($id)
    {
        $sql_query="SELECT * FROM photos WHERE stud_id = $id";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $list=array();
        while ( $row = $stmt->fetch_assoc()) {
            // $list[]=array(
            //     'image'=>$this->getPhoto($row['image'])
            // );
            array_push($list, $this->getPhoto($row['image']));
        }

        $stmt->close();

        if (count($list)>0)
        {
            $result=array(
                'success'=>true,
                'image_list'=>$list
            );
        }
        else
        {
            $result=array(
                'success'=>false,
                'message'=>NOT_FOUND
            );
        }
        return $result;
    }

    public function addStudent($name, $dept, $mobile)
    {
        $sql_query="CALL addStudent(?,?,?,@is_done,@s_id)";
        $stmt      = $this->conn->prepare($sql_query);
        $stmt->bind_param('sss', $name, $dept, $mobile);
        $stmt->execute();
        $stmt->close();
                
        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done,@s_id AS s_id");
        $stmt1->execute();
        $stmt1->bind_result($is_done, $s_id);       
        $stmt1->fetch();
        $stmt1->close();
            
        if ($is_done) {
            $result=array(
                'success'=>true,
                'student_id'=>$s_id,
                'message'=>RECORD_ADD_SUCCESS
            );
        }
        else
        {
            $result=array(
                'success'=>false,
                'message'=>RECORD_ADD_FAIL
            );
        }
        return $result;
    }

    public function updateStudent($id, $name)
    {

        $sql_query="CALL updateStudent($id,'$name',@is_done)";
        $stmt = $this->conn->query($sql_query);

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);       
        $stmt1->fetch();
        $stmt1->close();
            
        if ($is_done) {
            $result=array(
                'success'=>true,
                'message'=>UPDATE_SUCCESS
            );
        }
        else
        {
            $result=array(
                'success'=>false,
                'message'=>UPDATE_FAIL
            );
        }
        return $result;
    }
    
    public function deleteStudent($id)
    {
        $sql_query="CALL deleteStudent($id,@is_done)";
        $stmt = $this->conn->query($sql_query);
        
        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);       
        $stmt1->fetch();
        $stmt1->close();
            
        if ($is_done) {
            $result=array(
                'success'=>true,
                'message'=>DELETE_SUCCESS
            );
        }
        else
        {
            $result=array(
                'success'=>false,
                'message'=>DELETE_FAIL
            );
        }
        return $result;
    }

    public function updateStudentProfilePic($id,$photo)
	{
		$filename = '';		
        if (!file_exists($this->stud_profile_pic_path)) {
            mkdir($this->stud_profile_pic_path, 0777, true);
        }

		$extension = pathinfo($photo['name'],PATHINFO_EXTENSION);
		$filename = $id .'.'. $extension;
		$file = $this->stud_profile_pic_path . $filename;

		if (move_uploaded_file($photo['tmp_name'], $file)) {
			chmod($file, 0777);

            //echo "CALL updateStudPhoto($id,'$filename',@is_done)";
		    $sql_query="CALL updateStudPhoto($id,'$filename',@is_done)";
			$stmt = $this->conn->query($sql_query);

            $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
            $stmt1->execute();
            $stmt1->bind_result($is_done);       
            $stmt1->fetch();
            $stmt1->close();
            
            if ($is_done) {
                $result=array(
                    'success'=>true,
                    'message'=>UPLOAD_SUCCESS
                );
            }
            else
            {
                $result=array(
                    'success'=>false,
                    'message'=>UPLOAD_FAIL
                );
            }
        }else
        {
            $result=array(
                'success'=>false,
                'message'=>UPLOAD_FAIL
            );
        }
        return $result;
	}

    public function uploadMultipleImage($id,$photos,$is_photo_set)
	{
		if ($is_photo_set) {
            if (!file_exists($this->stud_profile_pic_path)) {
                mkdir($this->stud_profile_pic_path, 0777, true);
            }

            $count = count($photos["name"]);
            for ($i=0; $i<$count; $i++){       
                $extension   = pathinfo($photos['name'][$i], PATHINFO_EXTENSION);
                $filename    = time().$id. $i . '.' . $extension;
                $file        = $this->stud_profile_pic_path . $filename;
            
                if (move_uploaded_file($photos['tmp_name'][$i], $file)) {
                    //echo "INSERT INTO `photos`( `stud_id`, `image`) VALUES($id,'$filename');";
                    $sql_query = "INSERT INTO `photos`( `stud_id`, `image`) VALUES($id,'$filename');";
                    $stmt1 = $this->conn->query($sql_query);

                    $result = array(
                        'success' => true,
                        'message' => UPLOAD_SUCCESS
                    );
                }else{
                    $result = array(
                        'success' => false,
                        'message' => UPLOAD_FAIL
                    );
                }
            }
        }else{
            $result=array(
                'success'=>false,
                'message'=>UPLOAD_FAIL
            );
        }
        return $result;
	}

    private function getPhoto($pic)
    {
        $photo = '';
        if (strcmp($pic, '') != 0) {
            if (strpos($pic, 'http://') !== false || strpos($pic, 'https://') !== false) {
                $photo = $pic;
            } else {
                $file_temp = $this->stud_profile_pic_path . $pic;
                
                if (file_exists($file_temp)) {
                    $photo = $this->stud_profile_pic_url . $pic;
                }
            }
        }
        
        return $photo;
    }
}

?>
