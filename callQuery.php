<?php
//
// Run passed-in query returning result set (PDOStatement object)
// on success or exit on failure
//
function callQuery($pdo, $query, $error) {

  try {
    return $pdo->query($query);
  } catch (PDOException $ex) {

    $error .= $ex->getMessage();  // Remove getMessage() in production
    include 'error.html.php';
    throw $ex;
    //exit();

  }

}