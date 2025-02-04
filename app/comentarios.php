<?php
// Depuração: Exibir o conteúdo de $_POST
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Conectar ao banco de dados
$con = mysqli_connect('localhost','root','', 'tags');
// Verificar conexão
if (!$con) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Resto do código...
?>
<?php
// Conectar ao banco de dados
$con = mysqli_connect('localhost','root','', 'tags');
// Verificar conexão
if (!$con) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Receber dados do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se o campo pagina_id foi enviado
    if (!isset($_POST['pagina_id'])) {
        die("Erro: O campo 'pagina_id' não foi enviado.");
    }

    // Verificar se o valor de pagina_id é válido
    $pagina_id = $_POST['pagina_id'];
    if (!is_numeric($pagina_id)) {
        die("Erro: O valor de 'pagina_id' não é um número válido.");
    }

    $pagina_id = intval($pagina_id); // Converter para inteiro
    if ($pagina_id <= 0) {
        die("Erro: O valor de 'pagina_id' deve ser um número inteiro positivo.");
    }

    $nome = trim($_POST['nome']);
    $comentario = trim($_POST['comentario']);

    // Validar dados
    if (empty($nome) || empty($comentario)) {
        die("Por favor, preencha todos os campos.");
    }

    // Verificar se o ID da página existe na tabela `paginas`
    $sql = "SELECT id FROM paginas WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);

    if (!$stmt) {
        die("Erro na preparação da consulta: " . mysqli_error($con));
    }

    // Vincular parâmetros e executar a consulta
    mysqli_stmt_bind_param($stmt, 'i', $pagina_id);
    if (!mysqli_stmt_execute($stmt)) {
        die("Erro ao executar a consulta: " . mysqli_stmt_error($stmt));
    }

    // Armazenar o resultado
    mysqli_stmt_store_result($stmt);

    // Verificar se o ID da página existe
    if (mysqli_stmt_num_rows($stmt) == 0) {
        die("ID da página inválido. O ID $pagina_id não foi encontrado na tabela `paginas`.");
    }

    mysqli_stmt_close($stmt);

    // Inserir comentário no banco de dados
    $sql = "INSERT INTO comentarios (pagina_id, nome, comentario) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);

    if (!$stmt) {
        die("Erro na preparação da consulta de inserção: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmt, 'iss', $pagina_id, $nome, $comentario);

    if (mysqli_stmt_execute($stmt)) {
        echo "Comentário adicionado com sucesso!";
    } else {
        echo "Erro ao adicionar comentário: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Método de requisição inválido.";
}

// Fechar a conexão
mysqli_close($con);

// Redirecionar de volta para a página de origem
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: index.php"); // Redirecionar para a página inicial se não houver referência
}
exit();
?>