<?php
include_once('../inc/global.php');
include_once("../config/route.php");
$user = unserialize($_SESSION['user']);
$today = Utility\WebSystem::displayDate(date("Y-m-d H:i:s"), 'Y-m-d');

$map = [
    'column_sttID' => 'stt.id',
    'column_refNo' => 'stt.refNo',
    'column_tpbNo' => 'tpb.TPBNo',
    'column_client' => 'client.contactPerson',
    'column_addressDDLot' => 'stt.addressDDLot',
    'column_submissionDate' => 'stt.submissionDate',
    'column_status' => 'generalStatus.name',
    'column_function' => 'stt.id'
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


$sqlAll = Database\Sql::select(['stt', 'stt'])        
->leftJoin(['generalStatus', 'generalStatus'], "stt.status = generalStatus.id")
->leftJoin(['tpb', 'tpb'], "stt.tpbID = tpb.id")
->leftJoin(['client', 'client'], "stt.clientID = client.id");

if(!empty($tpbID)){
    $sqlAll->where(['stt.tpbID', '=', $tpbID]);
}

$sqlAll->setFieldValue('
   stt.id sttID, 
   stt.refNo sttRefNo, 
   tpb.TPBNo tpbNo, 
   client.contactPerson contactPerson, 
   stt.addressDDLot addressDDLot, 
   stt.submissionDate submissionDate, 
   generalStatus.name statusName                         
');

if($searchValue != ''){
    $searchValue = addslashes($searchValue);
     $sqlAll->whereOp("(stt.id LIKE '%".$searchValue."%' 
        OR stt.refNo LIKE '%".$searchValue."%'
        OR tpb.TPBNo LIKE '%".$searchValue."%'
        OR client.contactPerson LIKE '%".$searchValue."%'
        OR stt.addressDDLot LIKE '%".$searchValue."%'
        OR stt.submissionDate LIKE '%".$searchValue."%'
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
    $sqlAll = Database\Sql::select(['stt', 'stt'])        
    ->leftJoin(['generalStatus', 'generalStatus'], "stt.status = generalStatus.id")
    ->leftJoin(['tpb', 'tpb'], "stt.tpbID = tpb.id")
    ->leftJoin(['client', 'client'], "stt.clientID = client.id");

    if(!empty($tpbID)){
        $sqlAll->where(['stt.tpbID', '=', $tpbID]);
    }
        
    $sqlAll->setFieldValue('
       stt.id sttID, 
       stt.refNo sttRefNo, 
       tpb.TPBNo tpbNo, 
       client.contactPerson contactPerson, 
       stt.addressDDLot addressDDLot, 
       stt.submissionDate submissionDate, 
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
            "column_sttID"=>$data['sttID'],
            "column_refNo"=>$data['sttRefNo'],
            "column_tpbNo"=>$data['tpbNo'],
            "column_client"=>$data['contactPerson'],
            "column_addressDDLot"=>$data['addressDDLot'],
            "column_submissionDate"=>($data['submissionDate']=="0000-00-00"?" ":$data['submissionDate']),   
            "column_status"=>$data['statusName'],        
            "column_function"=>"
                <div class='btn-group' role='group' aria-label=''>
                    <div class='btn-group' role='group'>
                        <button id='btnGroupDrop".$data['sttID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                            ".L('Actions')."
                        </button>
                    <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['sttID']."'>
                        <li>
                            <div class='d-grid'>
                                <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['sttID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>    
                                <button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$data['sttID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>
                                <button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$data['sttID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>
                            </div>
                        </li>
                    </ul>
                    </div>
                </div>
            "
        ];  
    } else {
        $dataArr = [
            "column_sttID"=>$data['sttID'],
            "column_refNo"=>$data['sttRefNo'],
            "column_tpbNo"=>$data['tpbNo'],
            "column_client"=>$data['contactPerson'],
            "column_addressDDLot"=>$data['addressDDLot'],
            "column_submissionDate"=>($data['submissionDate']=="0000-00-00"?" ":$data['submissionDate']),   
            "column_status"=>$data['statusName'],        
            "column_function"=>"
                <div class='btn-group' role='group' aria-label=''>
                    <div class='btn-group' role='group'>
                        <button id='btnGroupDrop".$data['sttID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                            ".L('Actions')."
                        </button>
                    <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['sttID']."'>
                        <li>
                            <div class='d-grid'>
                                <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['sttID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>                                    
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