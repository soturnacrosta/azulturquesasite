<?php
// Conectar ao banco de dados
$con = mysqli_connect('localhost','root','', 'tags');

// Verificar conexão
if (!$con) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Receber o ID da página da URL
if (isset($_GET['id'])) {
    $pagina_id = intval($_GET['id']); // Garantir que o ID seja um número inteiro

    // Buscar os detalhes da página
    $sql = "SELECT titulo, conteudo, link_real FROM paginas WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $pagina_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    // Corrigir aqui: adicionar 'link_real' na listagem de variáveis
    mysqli_stmt_bind_result($stmt, $titulo, $conteudo, $link_real);

    if (mysqli_stmt_fetch($stmt)) {
        // Verificar se link_real não está vazio
        if ($link_real) {
            // Redirecionar para a página real
            header("Location: " . $link_real);
            exit(); // Para garantir que o código pare após o redirecionamento
        } else {
            // Exibir o conteúdo da página normalmente
            echo "<h1>" . htmlspecialchars($titulo) . "</h1>";
            echo "<p>" . htmlspecialchars($conteudo) . "</p>";
        }
    } else {
        echo "<p>Página não encontrada.</p>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<p>ID da página não especificado.</p>";
}

// Fechar a conexão
mysqli_close($con);
?>
