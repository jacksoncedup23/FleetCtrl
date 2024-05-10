<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Processar</title>
</head>
<body>

<?php
// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se foram selecionadas linhas
    if (isset($_POST["linhas"])) {
        echo "<h2>Linhas selecionadas:</h2>";
        // Percorre as linhas selecionadas
        foreach ($_POST["linhas"] as $linha) {
            echo "<p>ID: " . $_POST["id"][$linha - 1] . ", Linha: $linha</p>";
        }
    } else {
        echo "<p>Nenhuma linha foi selecionada.</p>";
    }
} else {
    echo "<p>Este arquivo não pode ser acessado diretamente.</p>";
}
?>

</body>
</html>
