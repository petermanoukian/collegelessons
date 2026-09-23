<?php
// Include central database connection ($pdo)
require_once __DIR__ . '/../connection.inc.php';

$statusMessage = '';
if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $methodUsed = htmlspecialchars($_GET['method'] ?? 'POST');
    $statusMessage = "Record successfully saved using $methodUsed request!";
}

// ---------------------------------------------------------
// 1. READ SEARCH & FILTER INPUTS (REQUEST: POST or GET)
// ---------------------------------------------------------
$searchFirstName = trim($_REQUEST['first_name'] ?? '');
$searchLastName  = trim($_REQUEST['last_name'] ?? '');
$searchOperator  = ($_REQUEST['operator'] ?? 'AND') === 'OR' ? 'OR' : 'AND';
$searchMatchType = $_REQUEST['match_type'] ?? 'contains';

// Support direct filtering via distinct value clicks (e.g., ?filter_field=first_name&filter_val=John)
if (isset($_GET['filter_field'], $_GET['filter_val'])) {
    $filterField = $_GET['filter_field'];
    $filterVal   = trim($_GET['filter_val']);

    if ($filterField === 'first_name') {
        $searchFirstName = $filterVal;
        $searchMatchType = 'exact';
    } elseif ($filterField === 'last_name') {
        $searchLastName = $filterVal;
        $searchMatchType = 'exact';
    }
}

$whereClauses = [];
$params = [];

if ($searchFirstName !== '') {
    if ($searchMatchType === 'exact') {
        $whereClauses[] = "first_name = :first_name";
        $params[':first_name'] = $searchFirstName;
    } elseif ($searchMatchType === 'starts_with') {
        $whereClauses[] = "first_name LIKE :first_name";
        $params[':first_name'] = $searchFirstName . '%';
    } else { // 'contains'
        $whereClauses[] = "first_name LIKE :first_name";
        $params[':first_name'] = '%' . $searchFirstName . '%';
    }
}

if ($searchLastName !== '') {
    if ($searchMatchType === 'exact') {
        $whereClauses[] = "last_name = :last_name";
        $params[':last_name'] = $searchLastName;
    } elseif ($searchMatchType === 'starts_with') {
        $whereClauses[] = "last_name LIKE :last_name";
        $params[':last_name'] = $searchLastName . '%';
    } else { // 'contains'
        $whereClauses[] = "last_name LIKE :last_name";
        $params[':last_name'] = '%' . $searchLastName . '%';
    }
}

// Build query
$sql = "SELECT id, username, first_name, last_name, age, email, nickname, gender, membership, 
newsletter, created_at ,img, thumb, file
        FROM students";

if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" $searchOperator ", $whereClauses);
}
$sql .= " ORDER BY id DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $records = [];
    $errorMessage = "Database Error: " . $e->getMessage();
}

// ---------------------------------------------------------
// 2. PROCESS GROUP BY (REQUEST: POST or GET)
// ---------------------------------------------------------
$groupField = $_REQUEST['group_by'] ?? '';
$allowedGroupFields = ['gender', 'membership', 'newsletter'];
$groupedResults = [];

if (in_array($groupField, $allowedGroupFields, true)) {
    try {
        $groupSql = "SELECT {$groupField}, COUNT(*) as total_count, AVG(age) as avg_age 
                     FROM students 
                     GROUP BY {$groupField}";
        $groupStmt = $pdo->query($groupSql);
        $groupedResults = $groupStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errorMessage = "Grouping Error: " . $e->getMessage();
    }
}

// ---------------------------------------------------------
// 3. PROCESS SELECT DISTINCT (REQUEST: POST or GET)
// ---------------------------------------------------------
$distinctField = $_REQUEST['distinct_field'] ?? '';
$allowedDistinctFields = ['first_name', 'last_name', 'nickname'];
$distinctResults = [];

if (in_array($distinctField, $allowedDistinctFields, true)) {
    try {
        $distinctSql = "SELECT DISTINCT {$distinctField} 
                        FROM students 
                        WHERE {$distinctField} IS NOT NULL AND {$distinctField} != '' 
                        ORDER BY {$distinctField} ASC";
        $distinctStmt = $pdo->query($distinctSql);
        $distinctResults = $distinctStmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        $errorMessage = "Distinct Query Error: " . $e->getMessage();
    }
}