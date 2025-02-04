<?php

$voto = $_GET['voto'];

$con = mysqli_connect('localhost','root','', 'enquete');

$user_ip = $_SERVER['REMOTE_ADDR'];

// Verificar se o IP já votou
$sql_check_ip = "SELECT * FROM votos_ip WHERE ip_address = '$user_ip'";
$result = mysqli_query($con, $sql_check_ip);

if (mysqli_num_rows($result) > 0) {
    echo "Você já votou! Não é permitido votar novamente.";
    exit;
}

    if ($voto = $_GET['voto']=='Sim'){
        $sql = "UPDATE enquete SET quant_votos_sim = quant_votos_sim + 1 where id = 1";
        $query = mysqli_query ($con, $sql);

    }

    if ($voto = $_GET['voto']=='Nao'){
        $sql = "UPDATE enquete SET quant_votos_nao = quant_votos_nao + 1 where id = 1";
        $query = mysqli_query ($con, $sql);

    }

    if ($voto = $_GET['voto']=='Um pouco'){
        $sql = "UPDATE enquete SET quant_votos_um_pouco = quant_votos_um_pouco + 1 where id = 1";
        $query = mysqli_query ($con, $sql);

    }

// Registrar o IP como já tendo votado
$sql_insert_ip = "INSERT INTO votos_ip (ip_address) VALUES ('$user_ip')";
mysqli_query($con, $sql_insert_ip);


$con->close();
header("Location: " . $_SERVER['HTTP_REFERER']); //faz retornar para a mesma página
exit;

?>
