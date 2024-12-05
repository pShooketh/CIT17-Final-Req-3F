<?php
function executeQuery($pdo, $query, $params = []) {
    try {
        // Check connection before executing query
        $pdo = checkConnection($pdo);
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        // If the connection was lost, try to reconnect once
        if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
            $pdo = checkConnection($pdo);
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            return $stmt;
        }
        throw $e;
    }
}
?> 