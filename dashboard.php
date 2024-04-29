<?php
// Include file database.php untuk koneksi ke database
include 'database.php';

// Fungsi untuk membersihkan input pengguna dari karakter khusus dan mencegah SQL injection
function clean_input($input) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags($input)));
}

// Tangkap pesan yang dikirim oleh pengguna
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["message"])) {
    // Bersihkan pesan yang dikirim oleh pengguna
    $pesan = clean_input($_POST["message"]);

    // Periksa apakah pesan tidak kosong
    if (!empty($pesan)) {
        // Simpan pesan ke dalam tabel history_chat
        $sql = "INSERT INTO history_chat (message) VALUES (?)";
        $stmt = $conn->prepare($sql);

        // Bind parameter pesan ke pernyataan SQL
        $stmt->bind_param("s", $pesan);

        // Eksekusi pernyataan SQL
        $stmt->execute();

        // Tutup pernyataan
        $stmt->close();
    }
}

// Tutup koneksi ke database
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
       <style>
        /* style.css */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f2f2f2;
        }

        .chat-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            padding: 20px;
            position: relative; /* Tambahkan untuk posisi tombol logout */
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
        }

        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            word-wrap: break-word;
        }

        .message.outgoing {
            background-color: #0088cc;
            color: #fff;
            align-self: flex-end;
        }

        .message.incoming {
            background-color: #f2f2f2;
            color: #333;
            align-self: flex-start;
        }

        .chat-input {
            display: flex;
            align-items: center;
            border-top: 1px solid #ccc;
        }

        .chat-input input {
            flex: 1;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px 0 0 5px;
            outline: none;
        }

        .chat-input button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #0088cc;
            color: #fff;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .chat-input button:hover {
            background-color: #005580;
        }

        /* Tambahkan gaya untuk tombol logout */
        .logout-button {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            font-size: 14px;
            background-color: #d9534f;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .logout-button:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-messages" id="chatMessages">
            <!-- Chat messages will be displayed here -->
        </div>
        <div class="chat-input">
            <input id="messageInput" type="text" placeholder="Type your message...">
            <button id="sendMessage">Send</button>
        </div>
    </div>

    <script>
        // Function to fetch messages from fetch_message.php
        function fetchMessages() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var messages = JSON.parse(xhr.responseText);
                    // Display new messages
                    displayMessages(messages);
                }
            };
            xhr.open('GET', 'fetch_message.php?last_fetch_time=' + lastFetchTime, true);
            xhr.send();
        }

        // Function to display messages
        function displayMessages(messages) {
            var chatMessages = document.getElementById('chatMessages');
            messages.forEach(function(message) {
                var messageDiv = document.createElement('div');
                messageDiv.textContent = message;
                chatMessages.appendChild(messageDiv);
            });
            // Scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Variable to store the last fetched time
        var lastFetchTime = Math.floor(Date.now() / 1000);

        // Call fetchMessages function initially
        fetchMessages();

        // Set interval to call fetchMessages every 5 seconds
        setInterval(fetchMessages, 5000);

        // Other JavaScript code for sending messages, etc.
        var sendMessageButton = document.getElementById('sendMessage');
        var messageInput = document.getElementById('messageInput');

        // Fungsi untuk mengirim perintah ke respon.php atau respon_trx.php dengan menggunakan AJAX
        function sendCommand(command) {
            var xhr = new XMLHttpRequest();
            // Memeriksa apakah perintah dimulai dengan "sal"
            if (command.startsWith("sal")) {
                xhr.open('GET', 'respon.php?command=sal', true);
            }
            // Memeriksa apakah perintah dimulai dengan "trx"
            else if (command.startsWith("trx")) {
                xhr.open('GET', 'respon_trx.php?message=' + encodeURIComponent(command), true);
            } else {
                // Jika perintah tidak sesuai dengan format yang diharapkan
                displayMessage('Invalid command', 'incoming');
                return;
            }

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = xhr.responseText;
                        displayMessage(response, 'incoming');
                    } else {
                        displayMessage('Error: ' + xhr.statusText, 'incoming');
                    }
                }
            };
            xhr.send();
        }

// Fungsi untuk menampilkan pesan dalam kotak chat
    function displayMessage(message, sender) {
        var chatMessages = document.querySelector('.chat-messages');
        var lastMessage = chatMessages.lastElementChild;

        // Check if the message is the same as the last message
        if (lastMessage && lastMessage.textContent.trim() === message.trim()) {
            return; // Skip adding the message if it's the same as the last one
        }

        var newMessage = document.createElement('div');
        newMessage.className = 'message ' + sender;
        newMessage.innerHTML = '<p>' + message + '</p>';
        chatMessages.appendChild(newMessage);
        // Gulir ke bawah untuk menunjukkan pesan terbaru
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Ketika tombol "Send" ditekan, panggil fungsi sendMessage
    sendMessageButton.addEventListener('click', function() {
        var message = messageInput.value.trim();
        if (message) {
            displayMessage(message, 'outgoing');
            // Kirim perintah ke respon.php atau respon_trx.php sesuai dengan pesan
            sendCommand(message.toLowerCase());
            // Bersihkan input pesan
            messageInput.value = '';
        }
    });

    // Fungsi untuk mengirim pesan ketika tombol Enter ditekan
    messageInput.addEventListener('keypress', function(event) {
        if (event.keyCode === 13) {
            event.preventDefault(); // Mencegah pengiriman form default
            sendMessageButton.click(); // Menekan tombol "Send" secara otomatis
        }
    });
</script>
</body>
</html>
