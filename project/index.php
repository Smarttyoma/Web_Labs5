<?php
// Папки-категории
$categories = ['programs' => 'Программы', 'games' => 'Игры', 'apps' => 'Приложения'];

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $category = $_POST['category'];
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);

    if (!empty($email) && !empty($category) && !empty($title) && !empty($message)) {
        // Очистить заголовок для имени файла (убрать пробелы, опасные символы)
        $safeTitle = preg_replace('/[^a-zA-Z0-9-_]/', '_', $title);

        $filepath = "$category/{$safeTitle}.txt";
        $content = "Email: $email\n\nТекст объявления:\n$message";

        file_put_contents($filepath, $content);
    }
}

// Функция для загрузки всех объявлений
function loadAds($categories) {
    $ads = [];

    foreach ($categories as $folder => $name) {
        if (is_dir($folder)) {
            $files = scandir($folder);

            foreach ($files as $file) {
                if (is_file("$folder/$file") && pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
                    $title = pathinfo($file, PATHINFO_FILENAME);
                    $text = file_get_contents("$folder/$file");

                    $ads[] = [
                        'category' => $name,
                        'title' => $title,
                        'content' => nl2br(htmlspecialchars($text)),
                    ];
                }
            }
        }
    }

    return $ads;
}

$ads = loadAds($categories);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Доска объявлений</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>

<h2>Добавить объявление</h2>

<form method="POST">
    <p>Email: <input type="email" name="email" required></p>

    <p>Категория:
        <select name="category" required>
            <?php foreach ($categories as $folder => $displayName): ?>
                <option value="<?= htmlspecialchars($folder) ?>"><?= htmlspecialchars($displayName) ?></option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>Заголовок объявления: <input type="text" name="title" required></p>

    <p>Текст объявления:<br>
        <textarea name="message" rows="5" cols="50" required></textarea>
    </p>

    <button type="submit">Добавить</button>
</form>

<h2>Список объявлений</h2>

<table>
    <thead>
        <tr>
            <th>Категория</th>
            <th>Заголовок</th>
            <th>Содержание</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($ads)): ?>
            <tr><td colspan="3">Объявлений пока нет.</td></tr>
        <?php else: ?>
            <?php foreach ($ads as $ad): ?>
                <tr>
                    <td><?= $ad['category'] ?></td>
                    <td><?= htmlspecialchars($ad['title']) ?></td>
                    <td><?= $ad['content'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
