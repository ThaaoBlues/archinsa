<?php
session_start();
include("test_creds.php");

// Check if user is logged in and is an admin
if (!isset($_SESSION["utilisateur_authentifie"]) || $_SESSION["utilisateur_authentifie"] !== true || !$_SESSION["admin"]) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli($servername, $db_username, $db_password,$dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $admin = isset($_POST['admin']) ? 1 : 0;
        $stmt = $conn->prepare("UPDATE users SET username = ?, admin = ? WHERE id = ?");
        $stmt->bind_param("sii", $username, $admin, $id);
        $stmt->execute();
        $stmt->close();
    }
}

$result = $conn->query("SELECT id, username, admin FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Liste des utilisateurs</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Admin</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <form method="post" action="utilisateurs.php">
                <td><?php echo $row['id']; ?></td>
                <td><input type="text" name="username" value="<?php echo $row['username']; ?>"></td>
                <td><input type="checkbox" name="admin" <?php if ($row['admin']) echo "checked"; ?>></td>
                <td>
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="update">Update</button>
                    <button type="submit" name="delete" onclick="return confirm('T\'es sur sur sur de le supprimer ? ');">Delete</button>
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
