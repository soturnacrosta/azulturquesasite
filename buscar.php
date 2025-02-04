<?php
// Conectar ao banco de dados
$con = mysqli_connect('localhost','root','', 'tags');
// Verificar conexão
if (!$con) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Receber as tags do formulário
if (isset($_GET['tags'])) {
    $tags = trim($_GET['tags']); // Remover espaços em branco
    $tags_array = explode(',', $tags); // Dividir as tags por vírgula

    // Limpar e sanitizar as tags
    $tags_array = array_map('trim', $tags_array);
    $tags_array = array_map('mysqli_real_escape_string', array_fill(0, count($tags_array), $con), $tags_array);

    // Montar a consulta SQL
    $sql = "
        SELECT p.id, p.titulo, p.conteudo, COUNT(pt.tag_id) AS relevancia
        FROM paginas p
        JOIN pagina_tags pt ON p.id = pt.pagina_id
        JOIN tags t ON pt.tag_id = t.id
        WHERE t.nome IN ('" . implode("','", $tags_array) . "')
        GROUP BY p.id
        ORDER BY relevancia DESC, p.titulo ASC
    ";

    // Executar a consulta
    $result = mysqli_query($con, $sql);

    if (!$result) {
        die("Erro na consulta: " . mysqli_error($con));
    }

    // Exibir os resultados
    if (mysqli_num_rows($result) > 0) {
        echo "<h2>Resultados da busca:</h2>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;'>";
            echo "<h3><a href='pagina.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['titulo']) . "</a></h3>";
            echo "<p>" . htmlspecialchars($row['conteudo']) . "</p>";
            echo "<small>Relevância: " . $row['relevancia'] . "</small>";
            echo "</div>";
        }
    } else {
        echo "<p>Nenhuma página encontrada para as tags informadas.</p>";
    }   
} else {
    echo "<p>Nenhuma tag informada.</p>";
}

// Fechar a conexão
mysqli_close($con);
?>