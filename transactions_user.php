<?php
include('config_db.php');

$transactions_per_page = 50;
$uuid = isset($_GET['uuid']) ? $_GET['uuid'] : null;
if (!$uuid) {
    die("Missing user UUID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Transactions</title>
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

<h1>Your Transactions</h1>
<div id="transactions-container">
    <table>
        <thead>
            <tr>
                <th>Transaction UUID</th>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Amount</th>
                <th id="sender-balance-header">Sender Balance</th>
                <th id="receiver-balance-header">Receiver Balance</th>
                <th>Object</th>
                <th>Description</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody id="transactions-body"></tbody>
    </table>
</div>

<script>
    let offset = 0;
    const limit = <?= $transactions_per_page ?>;
    let loading = false;
    const uuid = "<?= $uuid ?>";

    function loadTransactions() {
        if (loading) return;
        loading = true;

        fetch(`load_transactions_user.php?uuid=${uuid}&offset=${offset}&limit=${limit}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error("Error:", data.error);
                    loading = false;
                    return;
                }

                let tableBody = document.getElementById("transactions-body");
                data.forEach(transaction => {
                    let senderBalance = transaction.senderBalance !== null ? transaction.senderBalance : "N/A";
                    let receiverBalance = transaction.receiverBalance !== null ? transaction.receiverBalance : "N/A";

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
                        <td>${senderBalance}</td>
                        <td>${receiverBalance}</td>
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

    document.getElementById("transactions-container").addEventListener("scroll", function () {
        if (this.scrollTop + this.clientHeight >= this.scrollHeight - 10) {
            loadTransactions();
        }
    });

    loadTransactions();
</script>

</body>
</html>
