<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Module</title>
</head>
<body>
    <h1>Welcome to the Admin Module! </h1>
    
    Caso ainda use a imagem local sem registry:
    Eu já adicionei o placement constraint no serviço app para rodar no manager (onde a imagem é criada pelo runner). Isso evita inconsistências quando não há registro de imagens.
    Sobre “parar, limpar e subir nova versão”:
    Seu workflow já foi atualizado para:
    remover o stack anterior,
    rebuildar a imagem com pull,
    e redeployar o stack.
    Assim, mudanças no index.php passam a refleter imediatamentelegal.
</body>
</html>