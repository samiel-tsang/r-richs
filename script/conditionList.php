<?php
include_once('../inc/global.php');
include_once("../config/route.php");
$user = unserialize($_SESSION['user']);
$today = Utility\WebSystem::displayDate(date("Y-m-d H:i:s"), 'Y-m-d');


$map = [
    'column_conditionID' => 'conditions.id',
    'column_conditionNo' => 'conditions.conditionNo',
    'column_description' => 'conditions.description',
    'column_deadline' => 'conditions.deadline',
    'column_status' => 'status.name',
    'column_function' => 'conditions.id'
];

$tpbID = $_GET['tpbID'];
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$searchQuery = " ";


$sqlAll = Database\Sql::select(['tpbCondition', 'conditions'])
->leftJoin(['tpb', 'tpb'], "conditions.tpbID = tpb.id")
->leftJoin(['generalStatus', 'generalStatus'], "conditions.status = generalStatus.id");

if(!empty($tpbID)){
    $sqlAll->where(['conditions.tpbID', '=', $tpbID]);
}

$sqlAll->setFieldValue('
    conditions.id conditionID, 
    tpb.id tpbID, 
    conditions.conditionNo conditionNo, 
    conditions.description description, 
    conditions.deadline deadline, 
    generalStatus.name statusName                  
');

if($searchValue != ''){
    $searchValue = addslashes($searchValue);
     $sqlAll->whereOp("(conditions.id LIKE '%".$searchValue."%'        
        OR conditions.conditionNo LIKE '%".$searchValue."%'
        OR conditions.description LIKE '%".$searchValue."%'
        OR conditions.deadline LIKE '%".$searchValue."%'
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

$stmAll = $sqlAll->prepare();
$stmAll->execute();

if($stmAll->rowCount()==0 && $filter) {
    $sqlAll = Database\Sql::select(['tpbCondition', 'conditions'])
    ->leftJoin(['tpb', 'tpb'], "conditions.tpbID = tpb.id")
    ->leftJoin(['generalStatus', 'generalStatus'], "conditions.status = generalStatus.id");
    
    if(!empty($tpbID)){
        $sqlAll->where(['conditions.tpbID', '=', $tpbID]);
    }
    
    $sqlAll->setFieldValue('
        conditions.id conditionID, 
        tpb.id tpbID, 
        conditions.conditionNo conditionNo, 
        conditions.description description, 
        conditions.deadline deadline, 
        generalStatus.name statusName                  
    ');
};

$sql = $sqlAll->order($map[$columnName],$columnSortOrder)->limit($rowperpage, $row);

$stm = $sql->prepare();
$stm->execute();

$returnArr = [];
$contentArr = [];
$lineCount = 0;
foreach($stm as $data){    
    if(@$_GET['mode']=="edit") {
        $dataArr = [
            "column_conditionID"=>$data['conditionID'],
            "column_conditionNo"=>$data['conditionNo'],
            "column_description"=>$data['description'],
            "column_deadline"=>$data['deadline'],
            "column_status"=>$data['statusName'],        
            "column_function"=>"
                <div class='btn-group' role='group' aria-label=''>
                    <div class='btn-group' role='group'>
                        <button id='btnGroupDrop".$data['conditionID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                            ".L('Actions')."
                        </button>
                    <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['conditionID']."'>
                        <li>
                            <div class='d-grid'>                            
                                <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['conditionID']."' data-tpbid='".$data['tpbID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>
                                <button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$data['conditionID']."' data-tpbid='".$data['tpbID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>
                                <button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$data['conditionID']."' data-tpbid='".$data['tpbID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>
                            </div>
                        </li>
                    </ul>
                    </div>
                </div>
            "
        ];    
    } else {
        $dataArr = [
            "column_conditionID"=>$data['conditionID'],
            "column_conditionNo"=>$data['conditionNo'],
            "column_description"=>$data['description'],
            "column_deadline"=>$data['deadline'],
            "column_status"=>$data['statusName'],        
            "column_function"=>"
                <div class='btn-group' role='group' aria-label=''>
                    <div class='btn-group' role='group'>
                        <button id='btnGroupDrop".$data['conditionID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                            ".L('Actions')."
                        </button>
                    <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['conditionID']."'>
                        <li>
                            <div class='d-grid'>                            
                                <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['conditionID']."' data-tpbid='".$data['tpbID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>                              
                            </div>
                        </li>
                    </ul>
                    </div>
                </div>
            "
        ];           
    }

    $lineCount++;
    $contentArr[] = $dataArr;
}

$returnArr['draw'] = intval($draw);
$returnArr["iTotalDisplayRecords"] = $stmAll->rowCount();
$returnArr["iTotalRecords"] = $stmAll->rowCount();
$returnArr["data"] = $contentArr;

echo json_encode($returnArr);

?>