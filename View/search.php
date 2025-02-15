<?php
require_once('../Model/config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$searchResults = [];
$searchTerm ='';

//check if search query exists
if(isset($_GET['search']) && !empty(trim($_GET['search']))){
    $searchTerm = "%" . trim($_GET['search']) . "%";
//join users and responses tables to get usernames 
$sql = "SELECT users.username, responses.question, responses.answer
      FROM responses 
      INNER JOIN users on responses.user_id = users.id
      WHERE users.email LIKE ? or responses.question LIKE ?";
    
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $searchResults[] = $row;
    }
}
?>
<h2>Interview Questions</h2>
<form method="GET">
    <input type="text" name="search" placeholder="Enter keyword..." required>
    <button type="submit">Search</button>
</form>

<h3>Results</h3>
<ul>
    <?php if(!empty($searchResults)): ?>
        <?php foreach($searchResults as $result): ?>
            <li>
                <strong>User: <?php echo htmlspecialchars($result['username']); ?></strong><br>
                <strong>Question:</strong> <?php echo htmlspecialchars($result['question']); ?>
                <strong>Response:</strong> <?php echo htmlspecialchars($result['answer']); ?>
            </li>   
        <?php endforeach; ?>
    <?php else: ?>
        <li>No Results Found.</li>
    <?php endif; ?>
</ul>

<a href="Dashboard.php">Back to home Screen</a>