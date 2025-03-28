<?php
include_once('../inc/global.php');
include_once("../config/route.php");
$user = unserialize($_SESSION['user']);
$today = Utility\WebSystem::displayDate(date("Y-m-d H:i:s"), 'Y-m-d');


$map = [
    'column_clientTypeStatus' => 'status.name',
    'column_clientTypeID' => 'clientType.id',
    'column_clientTypeName' => 'clientType.name',
    'column_clientTotal' => 'sum(client.id)',
    'column_function' => 'clientType.id'
];

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$searchQuery = " ";

$sqlAll = Database\Sql::select(['clientType', 'clientType'])->leftJoin(['client', 'client'], "client.clientTypeID = clientType.id")->leftJoin(['status', 'status'], "clientType.status = status.id");

$sqlAll->whereOp("(client.status IS NULL OR client.status = '1')");

$sqlAll->setFieldValue('
    clientType.id clientTypeID, 
    clientType.name clientTypeName,
    count(client.id) totalClient,
    status.name statusName    
');

if($searchValue != ''){
    $searchValue = addslashes($searchValue);
    $sqlAll->whereOp("(clientType.id LIKE '%".$searchValue."%' 
        OR clientType.name LIKE '%".$searchValue."%'
        OR status.name LIKE '%".$searchValue."%'
    )");
}

foreach($_POST['columns'] as $idx => $column){
    if(!empty($column['search']['value'])) {
        $idxSearchValue = substr($column['search']['value'], 1, -1);
        if($_POST['columns'][$idx]['data']=="column_clientTypeStatus"){
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

$sqlAll->where(['1', 'GROUP BY', 'clientType.id']);


$stmAll = $sqlAll->prepare();
$stmAll->execute();

if($stmAll->rowCount()==0 && $filter) {
    $sqlAll = Database\Sql::select(['clientType', 'clientType'])->leftJoin(['client', 'client'], "client.clientTypeID = clientType.id")->leftJoin(['status', 'status'], "clientType.status = status.id");

    $sqlAll->whereOp("(client.status IS NULL OR client.status = '1')");    
    
    $sqlAll->setFieldValue('
        clientType.id clientTypeID, 
        clientType.name clientTypeName,
        count(client.id) totalClient,
        status.name statusName    
    ');

    $sqlAll->where(['1', 'GROUP BY', 'clientType.id']);
};



$sql = $sqlAll->order($map[$columnName],$columnSortOrder)->limit($rowperpage, $row);



$stm = $sql->prepare();
$stm->execute();

$returnArr = [];
$contentArr = [];
$lineCount = 0;
foreach($stm as $data){    

    $dataArr = [
        "column_clientTypeStatus"=>L($data['statusName']),
        "column_clientTypeID"=>$data['clientTypeID'],
        "column_clientTypeName"=>$data['clientTypeName'],       
        "column_clientTotal"=>$data['totalClient'],        
        "column_function"=>"
            <div class='btn-group' role='group' aria-label=''>
                <div class='btn-group' role='group'>
                    <button id='btnGroupDrop".$data['clientTypeID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                        ".L('Actions')."
                    </button>
                <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['clientTypeID']."'>
                    <li>
                        <div class='d-grid'>
                            <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['clientTypeID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>
                            <button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$data['clientTypeID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>
                            <button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$data['clientTypeID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>
                        </div>
                    </li>
                </ul>
                </div>
            </div>
        "
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