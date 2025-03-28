<?php
include_once('../inc/global.php');
include_once("../config/route.php");
$user = unserialize($_SESSION['user']);
$today = Utility\WebSystem::displayDate(date("Y-m-d H:i:s"), 'Y-m-d');


$map = [
    'column_eotID' => 'eot.id',
    'column_extendMonth' => 'eot.extendMonth',
    'column_reason' => 'eot.reason',
    'column_submissionDate' => 'eot.contactPerson',
    'column_submissionMode' => 'submissionDate.name',
    'column_status' => 'status.name',
    'column_function' => 'eot.id'
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


$sqlAll = Database\Sql::select(['tpbEOT', 'eot'])
->leftJoin(['tpb', 'tpb'], "eot.tpbID = tpb.id")
->leftJoin(['submissionMode', 'submissionMode'], "eot.submissionModeID = submissionMode.id")
->leftJoin(['generalStatus', 'generalStatus'], "eot.status = generalStatus.id");

if(!empty($tpbID)){
    $sqlAll->where(['eot.tpbID', '=', $tpbID]);
}

$sqlAll->setFieldValue('
    eot.id eotID, 
    tpb.id tpbID, 
    eot.extendMonth extendMonth, 
    eot.reason reason, 
    eot.submissionDate submissionDate, 
    submissionMode.name sumissionMode,
    eot.EOTDocID EOTDocID, 
    generalStatus.name statusName                  
');

if($searchValue != ''){
    $searchValue = addslashes($searchValue);
     $sqlAll->whereOp("(eot.id LIKE '%".$searchValue."%'        
        OR eot.extendMonth LIKE '%".$searchValue."%'
        OR eot.reason LIKE '%".$searchValue."%'
        OR eot.submissionDate LIKE '%".$searchValue."%'
        OR submissionMode.name LIKE '%".$searchValue."%'
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
    $sqlAll = Database\Sql::select(['tpbEOT', 'eot'])
    ->leftJoin(['tpb', 'tpb'], "eot.tpbID = tpb.id")
    ->leftJoin(['submissionMode', 'submissionMode'], "eot.submissionModeID = submissionMode.id")
    ->leftJoin(['generalStatus', 'generalStatus'], "eot.status = generalStatus.id");
    
    if(!empty($tpbID)){
        $sqlAll->where(['eot.tpbID', '=', $tpbID]);
    }
    
    $sqlAll->setFieldValue('
        eot.id eotID, 
        tpb.id tpbID, 
        eot.extendMonth extendMonth, 
        eot.reason reason, 
        eot.submissionDate submissionDate, 
        submissionMode.name sumissionMode,
        eot.EOTDocID EOTDocID, 
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
            "column_eotID"=>$data['eotID'],
            "column_extendMonth"=>$data['extendMonth'],
            "column_reason"=>$data['reason'],
            "column_submissionDate"=>$data['submissionDate']=="0000-00-00 00:00:00"?"":$data['submissionDate'],
            "column_submissionMode"=>$data['sumissionMode'],
            "column_status"=>$data['statusName'],        
            "column_function"=>"
                <div class='btn-group' role='group' aria-label=''>
                    <div class='btn-group' role='group'>
                        <button id='btnGroupDrop".$data['eotID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                            ".L('Actions')."
                        </button>
                    <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['eotID']."'>
                        <li>
                            <div class='d-grid'>
                                <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['eotID']."' data-tpbid='".$data['tpbID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>
                                <button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$data['eotID']."' data-tpbid='".$data['tpbID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>
                                <button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$data['eotID']."' data-tpbid='".$data['tpbID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>
                            </div>
                        </li>
                    </ul>
                    </div>
                </div>
            "
        ];    
    } else {
        $dataArr = [
            "column_eotID"=>$data['eotID'],
            "column_extendMonth"=>$data['extendMonth'],
            "column_reason"=>$data['reason'],
            "column_submissionDate"=>$data['submissionDate']=="0000-00-00 00:00:00"?"":$data['submissionDate'],
            "column_submissionMode"=>$data['sumissionMode'],
            "column_status"=>$data['statusName'],        
            "column_function"=>"
                <div class='btn-group' role='group' aria-label=''>
                    <div class='btn-group' role='group'>
                        <button id='btnGroupDrop".$data['eotID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                            ".L('Actions')."
                        </button>
                    <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['eotID']."'>
                        <li>
                            <div class='d-grid'>
                                <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['eotID']."' data-tpbid='".$data['tpbID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>                                
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