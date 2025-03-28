<?php
include_once('../inc/global.php');
include_once("../config/route.php");
$user = unserialize($_SESSION['user']);
$today = Utility\WebSystem::displayDate(date("Y-m-d H:i:s"), 'Y-m-d');


$map = [
    'column_clientStatus' => 'status.name',
    'column_clientID' => 'client.id',
    'column_clientType' => 'clientType.name',
    'column_clientTitle' => 'client.title',
    'column_clientContactPerson' => 'client.contactPerson',
    'column_clientPosition' => 'client.position',
    'column_clientPhone' => 'client.phone',
    'column_clientEmail' => 'client.email',
    'column_function' => 'client.id'
];

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$searchQuery = " ";

$sqlAll = Database\Sql::select(['client', 'client'])->leftJoin(['clientType', 'clientType'], "client.clientTypeID = clientType.id")->leftJoin(['status', 'status'], "client.status = status.id");

$sqlAll->setFieldValue('
    client.id clientID, 
    client.title clientTitle,
    client.contactPerson clientContactPerson,
    client.position clientPosition,
    client.email clientEmail,
    client.phone clientPhone,
    clientType.name clientType,
    status.name statusName    
');

if($searchValue != ''){
    $searchValue = addslashes($searchValue);
    $sqlAll->whereOp("(client.id LIKE '%".$searchValue."%' 
        OR client.title LIKE '%".$searchValue."%'
        OR client.contactPerson LIKE '%".$searchValue."%'
        OR client.position LIKE '%".$searchValue."%'
        OR client.phone LIKE '%".$searchValue."%'
        OR client.email LIKE '%".$searchValue."%'
        OR clientType.name LIKE '%".$searchValue."%'
        OR status.name LIKE '%".$searchValue."%'
    )");
}

foreach($_POST['columns'] as $idx => $column){
    if(!empty($column['search']['value'])) {
        $idxSearchValue = substr($column['search']['value'], 1, -1);
        if($_POST['columns'][$idx]['data']=="column_clientStatus"){
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
    $sqlAll = Database\Sql::select(['client', 'client'])->leftJoin(['clientType', 'clientType'], "client.clientTypeID = clientType.id")->leftJoin(['status', 'status'], "client.status = status.id");

    $sqlAll->setFieldValue('
        client.id clientID, 
        client.title clientTitle,
        client.contactPerson clientContactPerson,
        client.position clientPosition,
        client.email clientEmail,
        client.phone clientPhone,
        clientType.name clientType,
        status.name statusName    
    ');
};

$sql = $sqlAll->order($map[$columnName],$columnSortOrder)->limit($rowperpage, $row);

$stm = $sql->prepare();
$stm->execute();

$returnArr = [];
$contentArr = [];
$lineCount = 0;
foreach($stm as $data){    

    $dataArr = [
        "column_clientStatus"=>L($data['statusName']),
        "column_clientID"=>$data['clientID'],
        "column_clientTitle"=>$data['clientTitle'], 
        "column_clientContactPerson"=>$data['clientContactPerson'], 
        "column_clientPosition"=>$data['clientPosition'], 
        "column_clientEmail"=>$data['clientEmail'], 
        "column_clientPhone"=>$data['clientPhone'],    
        "column_clientType"=>$data['clientType'],       
        "column_function"=>"
            <div class='btn-group' role='group' aria-label=''>
                <div class='btn-group' role='group'>
                    <button id='btnGroupDrop".$data['clientID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                        ".L('Actions')."
                    </button>
                <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['clientID']."'>
                    <li>
                        <div class='d-grid'>
                            <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['clientID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>
                            <button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$data['clientID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>
                            <button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$data['clientID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>
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