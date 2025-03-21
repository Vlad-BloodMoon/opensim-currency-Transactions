<?php
include('config_db.php'); // Centralized database connection

session_start();

// Vérifier si l'utilisateur est déjà authentifié
if (!isset($_SESSION['authenticated'])) {
    // Vérifier les informations d'identification
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (isset($auth_users[$username]) && $auth_users[$username] == $password) {
            $_SESSION['authenticated'] = true;
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    }

    // Si l'utilisateur n'est pas authentifié, afficher le formulaire de connexion
    if (!isset($_SESSION['authenticated'])) {
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Connexion</title>
        </head>
        <body>
            <h2>Connexion</h2>
            <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
            <form method="post">
                <label for="username">USER :</label>
                <input type="text" id="username" name="username" required>
                <br>
                <label for="password">PASSWORD :</label>
                <input type="password" id="password" name="password" required>
                <br>
                <input type="submit" value="Se connecter">
            </form>
        </body>
        </html>
        <?php
        exit;
    }
}

// Default number of transactions displayed (configurable)
$transactions_per_page = 50;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
        }
        #transactions-container {
            height: 700px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .avatar-info {
            font-weight: bold;
        }
        .server-info {
            font-size: 0.9em;
            color: gray;
        }
    </style>
</head>
<body>

<h1>Transactions History</h1>
<div id="transactions-container">
    <table>
        <thead>
            <tr>
                <th>Transaction UUID</th>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Amount</th>
                <th>Sender Balance</th>
                <th>Receiver Balance</th>
                <th>Object</th>
                <th>Description</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody id="transactions-body">
            <!-- Transactions will be dynamically loaded here -->
        </tbody>
    </table>
</div>

<script>
    let offset = 0;
    const limit = <?= $transactions_per_page ?>;
    let loading = false;

    function loadTransactions() {
        if (loading) return;
        loading = true;

        fetch(`load_transactions.php?offset=${offset}&limit=${limit}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error("Error:", data.error);
                    loading = false;
                    return;
                }

                let tableBody = document.getElementById("transactions-body");
                data.forEach(transaction => {
                    let row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${transaction.UUID}</td>
                        <td>
                            <span class="avatar-info">${transaction.sender_name || "Unknown"}</span><br>
                            <span class="server-info">${transaction.sender_server}</span>
                        </td>
                        <td>
                            <span class="avatar-info">${transaction.receiver_name || "Unknown"}</span><br>
                            <span class="server-info">${transaction.receiver_server}</span>
                        </td>
                        <td>${transaction.amount} BM$</td>
                        <td>${transaction.senderBalance}</td>
                        <td>${transaction.receiverBalance}</td>
                        <td>${transaction.objectName || "N/A"}</td>
                        <td>${transaction.description || "No description"}</td>
                        <td>${new Date(transaction.time * 1000).toLocaleString()}</td>
                    `;
                    tableBody.appendChild(row);
                });
                offset += limit;
                loading = false;
            })
            .catch(error => console.error("Error loading transactions:", error));
    }

    document.getElementById("transactions-container").addEventListener("scroll", function() {
        if (this.scrollTop + this.clientHeight >= this.scrollHeight - 10) {
            loadTransactions();
        }
    });

    // Load initial transactions on page load
    loadTransactions();
</script>

</body>
</html>