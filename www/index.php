<?php
// Configuração do banco de dados
$servername = "db";
$username = "user";
$password = "password";
$dbname = "todolist";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Adiciona uma tarefa se o formulário for enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task']) && !empty($_POST['title'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $sql = "INSERT INTO tasks (title) VALUES ('$title')";
    if ($conn->query($sql) === FALSE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Marca uma tarefa como concluída
if (isset($_GET['complete_task'])) {
    $id = intval($_GET['complete_task']);
    $sql = "UPDATE tasks SET completed = 1 WHERE id = $id";
    if ($conn->query($sql) === FALSE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Deleta uma tarefa
if (isset($_GET['delete_task'])) {
    $id = intval($_GET['delete_task']);
    $sql = "DELETE FROM tasks WHERE id = $id";
    if ($conn->query($sql) === FALSE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Atualiza uma tarefa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_task']) && !empty($_POST['new_title'])) {
    $id = intval($_POST['task_id']);
    $new_title = $conn->real_escape_string($_POST['new_title']);
    $sql = "UPDATE tasks SET title = '$new_title' WHERE id = $id";
    if ($conn->query($sql) === FALSE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Obtem as tarefas
$sql = "SELECT id, title, completed FROM tasks";
$result = $conn->query($sql);

if ($result === FALSE) {
    echo "Error: " . $sql . "<br>" . $conn->error;
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>To-Do List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            margin: 0;
            padding-top: 160px;
        }

        header {
            position: fixed; 
            top: 0;          
            left: 0;
            right: 0;
            width: 100%;     
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;   
            padding: 20px 0; 
            text-align: center;
        }

        h1 {
            color: #333;
            font-family: fantasy;
            text-align: center;
            font-size: 55px;
            margin: 0;
        }

        form {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            width: 100%;
        }

        input[type="text"] {
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 70%;
            font-size: 16px;
        }

        button {
            padding: 15px 30px;
            border: none;
            background-color: #5cb85c;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
            font-size: 16px;
        }

        button:hover {
            background-color: #4cae4c;
        }

        ul {
            list-style-type: none;
            padding: 0;
            width: 80%;
            margin-top: 60px;
        }

        li.task {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 18px;
            width: 400px;
        }

        .completed {
            text-decoration: line-through;
            color: grey;
        }

        .task div {
            display: flex;
            gap: 10px;
        }

        .task a {
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .task a img {
            width: 20px;
            height: 20px;
        }

        .edit-form {
            display: none;
            flex-direction: column;
        }

        .edit-form input[type="text"] {
            width: auto;
            padding: 10px;
            font-size: 16px;
        }

        .edit-form button {
            padding: 10px 20px;
            font-size: 16px;
        }

        .update-button {
            margin: 10px 0 0 0;
        }
    </style>
    <script>
        function toggleEditForm(taskId) {
            var form = document.getElementById('edit-form-' + taskId);
            var text = document.getElementById('task-text-' + taskId);
            if (form.style.display === 'none') {
                form.style.display = 'flex';
                text.style.display = 'none';
            } else {
                form.style.display = 'none';
                text.style.display = 'flex';
            }
        }
    </script>
</head>
<body>
    <div>
        <header>
            <h1>To-Do List</h1>
            <form method="POST">
                <input type="text" name="title" placeholder="Add a new task" required>
                <button type="submit" name="add_task">Add</button>
            </form>
        </header>
        <ul>
            <?php while($row = $result->fetch_assoc()): ?>
                <li class="task <?php echo $row['completed'] ? 'completed' : ''; ?>">
                    <span id="task-text-<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['title']); ?>
                    </span>
                    <form method="POST" class="edit-form" id="edit-form-<?php echo $row['id']; ?>">
                        <input type="hidden" name="task_id" value="<?php echo $row['id']; ?>">
                        <input type="text" name="new_title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                        <button class="update-button" type="submit" name="edit_task">Update</button>
                    </form>
                    <div>
                        <?php if (!$row['completed']): ?>
                            <a class="complete" href="?complete_task=<?php echo $row['id']; ?>">
                                <img src="img/verificar.png" alt="Complete">
                            </a>
                        <?php endif; ?>
                        <a class="delete" href="?delete_task=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this task?');">
                            <img src="img/lixeira-xmark.png" alt="Delete">
                        </a>
                        <a class="edit" href="javascript:void(0);" onclick="toggleEditForm(<?php echo $row['id']; ?>)">
                            <img src="img/lapis.png" alt="Edit">
                        </a>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
