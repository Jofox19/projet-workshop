<?php

// Fonction pour se connecter à la base de données
function connectToDatabase() {
    $servername = "localhost";
    $username = "root"; 
    $password = ""; 
    $dbname = "danceclub"; 

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        return null;
    }
}

function addComment($userId, $articleId, $content, $stars)
{
    // Se connecter à la base de données
    $conn = connectToDatabase();
    if ($conn) {
        try {
            // Préparer la requête d'insertion
            $stmt = $conn->prepare("INSERT INTO comments (user_id, article_id, content, stars, created_at) VALUES (:user_id, :article_id, :content, :stars, CURRENT_TIMESTAMP)");

            // Liaison des paramètres
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':article_id', $articleId);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':stars', $stars);

            // Exécution de la requête
            $stmt->execute();
            return true; // Retourne vrai si l'insertion a réussi
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false; // Retourne faux en cas d'erreur
        }
    }
    return false; // Retourne faux si la connexion à la base de données a échoué
}

function deleteComment($commentId)
{
    // Se connecter à la base de données
    $conn = connectToDatabase();
    if ($conn) {
        try {
            // Préparer la requête de suppression
            $stmt = $conn->prepare("DELETE FROM comments WHERE id = :comment_id");

            // Liaison des paramètres
            $stmt->bindParam(':comment_id', $commentId);

            // Exécution de la requête
            $stmt->execute();
            return true; // Retourne vrai si la suppression a réussi
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false; // Retourne faux en cas d'erreur
        }
    }
    return false; // Retourne faux si la connexion à la base de données a échoué
}

function editComment($commentId, $newContent, $userId)
{
    // Se connecter à la base de données
    $conn = connectToDatabase();
    if ($conn) {
        try {
            // Vérifier si l'utilisateur connecté est le même que celui qui a laissé le commentaire
            session_start();
            if ($_SESSION['user_id'] == $userId) {
                // Préparer la requête de mise à jour du commentaire
                $stmt = $conn->prepare("UPDATE comments SET content = :new_content WHERE id = :comment_id");

                // Liaison des paramètres
                $stmt->bindParam(':new_content', $newContent);
                $stmt->bindParam(':comment_id', $commentId);

                // Exécution de la requête
                $stmt->execute();
                return true; // Retourne vrai si la mise à jour a réussi
            } else {
                return false; // Retourne faux si l'utilisateur connecté n'est pas autorisé à modifier ce commentaire
            }
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false; // Retourne faux en cas d'erreur
        }
    }
    return false; // Retourne faux si la connexion à la base de données a échoué