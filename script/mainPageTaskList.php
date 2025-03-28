<?php
include_once('../inc/global.php');
include_once("../config/route.php");
$currentUserObj = unserialize($_SESSION['user']);
$today = Utility\WebSystem::displayDate(date("Y-m-d H:i:s"), 'Y-m-d');

$map = [
    'column_taskID' => 'task.id',
    'column_officer' => 'user.displayName',
    'column_tpbNo' => 'tpb.TPBNo',
    'column_conditionNo' => 'conditions.conditionNo',
    'column_description' => 'task.description',
    'column_deadline' => 'task.deadline',
    'column_status' => 'status.name',
    'column_function' => 'task.id'
];


$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$searchQuery = " ";

$sqlAll = Database\Sql::select(['task', 'task'])->leftJoin(['conditionStatus', 'status'], "task.status = status.id")
->leftJoin(['tpbCondition', 'conditions'], "task.conditionID = conditions.id")
->leftJoin(['tpb', 'tpb'], "task.tpbID = tpb.id")
->leftJoin(['user', 'user'], "task.userID = user.id");

$sqlAll->setFieldValue('
    task.id id, 
    user.displayName officer,
    task.deadline deadline, 
    task.description description, 
    conditions.conditionNo conditionNo,
    tpb.TPBNo tpbNumber,
    status.name statusName     
');

if($searchValue != ''){
    $searchValue = addslashes($searchValue);
    $sqlAll->whereOp("(user.displayName LIKE '%".$searchValue."%'
        OR task.deadline LIKE '%".$searchValue."%'
        OR task.description LIKE '%".$searchValue."%'        
    )");
}

foreach($_POST['columns'] as $idx => $column){
    if(!empty($column['search']['value'])) {
        $idxSearchValue = substr($column['search']['value'], 1, -1);
        if($_POST['columns'][$idx]['data']=="column_status"){
           if($idxSearchValue=="有效") {
             $idxSearchValue="Enabled";
           }

           if($idxSearchValue=="無效") {
             $idxSearchValue="Disabled";
           }           
        } 

        $sqlAll->where([$map[$_POST['columns'][$idx]['data']], '=', '"'.strip_tags($idxSearchValue).'"']);
    }
}

$sqlAll->where(['task.status', '!=', 2]);

if($currentUserObj->roleID!=1) {
    $sqlAll->where(['task.userID', '=', '"'.$currentUserObj->id.'"']);
}

if($_GET['mode']==1){
    $sqlAll->where(['task.deadline', '>=', 'NOW()']);
} else {
    $sqlAll->where(['task.deadline', '<', 'NOW()']);
}

$stmAll = $sqlAll->prepare();
$stmAll->execute();

if($stmAll->rowCount()==0 && $filter) {
    $sqlAll = Database\Sql::select(['task', 'task'])->leftJoin(['conditionStatus', 'status'], "task.status = status.id")
    ->leftJoin(['tpbCondition', 'conditions'], "task.conditionID = conditions.id")
    ->leftJoin(['tpb', 'tpb'], "task.tpbID = tpb.id")
    ->leftJoin(['user', 'user'], "task.userID = user.id");
    
    $sqlAll->setFieldValue('
        task.id id, 
        user.displayName officer,
        task.deadline deadline, 
        task.description description, 
        conditions.conditionNo conditionNo,
        tpb.TPBNo tpbNumber,
        status.name statusName     
    ');

    $sqlAll->where(['task.status', '!=', 2]);

    if($currentUserObj->roleID!=1) {
        $sqlAll->where(['task.userID', '=', '"'.$currentUserObj->id.'"']);
    }

    if($_GET['mode']==1){
        $sqlAll->where(['task.deadline', '>=', 'NOW()']);
    } else {
        $sqlAll->where(['task.deadline', '<', 'NOW()']);
    }    
    
};

$sql = $sqlAll->order($map[$columnName],$columnSortOrder)->limit($rowperpage, $row);

$stm = $sql->prepare();
$stm->execute();

$returnArr = [];
$contentArr = [];
$lineCount = 0;
foreach($stm as $data){    

    $dataArr = [
        "column_officer"=>$data['officer'],
        "column_description"=>$data['description'],
        "column_deadline"=>$data['deadline'],        
        "column_function"=>"<input type='checkbox' class='form-check-input taskDone' data-id='".$data['id']."' value='1'>"
    ];    

    $lineCount++;
    $contentArr[] = $dataArr;
}

$returnArr['draw'] = intval($draw);
$returnArr["iTotalDisplayRecords"] = $stmAll->rowCount();
$returnArr["iTotalRecords"] = $stmAll->rowCount();
$returnArr["data"] = $contentArr;

echo json_encode($returnArr);

?>