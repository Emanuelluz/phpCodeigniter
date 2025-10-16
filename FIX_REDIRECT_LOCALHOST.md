# 🔧 Fix: Redirecionamento para localhost após login

## 🐛 Problema

Após fazer login em `https://pppr.ecl.dev.br/sso/login`, a aplicação redireciona para `http://localhost:8080/`.

## ✅ Solução

A aplicação estava usando o `baseURL` hardcoded no arquivo `app/Config/App.php`. Foi implementada uma solução para ler da variável de ambiente.

## 📝 Configuração no Easypanel

Configure a seguinte variável de ambiente:

```env
app.baseURL=https://pppr.ecl.dev.br/
```

⚠️ **IMPORTANTE:**
- Use `app.baseURL` (não `APP_BASE_URL`)
- A URL deve terminar com `/` (barra final)
- Use `https://` em produção

## 🔍 Verificação

Após configurar a variável de ambiente e fazer um novo deploy:

1. Acesse: `https://pppr.ecl.dev.br/sso/login`
2. Faça login com: `admin` / `DtiFB@2025`
3. Você deve ser redirecionado para: `https://pppr.ecl.dev.br/sso/admin`

## 🛠️ Como Funciona

O arquivo `app/Config/App.php` agora verifica variáveis de ambiente na seguinte ordem:

1. `APP_BASE_URL` (formato alternativo)
2. `app.baseURL` (formato recomendado)
3. Se nenhuma estiver definida, usa o padrão: `http://localhost:8080/`

```php
public function __construct()
{
    parent::__construct();

    // Sobrescrever baseURL com variável de ambiente se existir
    if (getenv('APP_BASE_URL')) {
        $this->baseURL = rtrim(getenv('APP_BASE_URL'), '/') . '/';
    } elseif (getenv('app.baseURL')) {
        $this->baseURL = rtrim(getenv('app.baseURL'), '/') . '/';
    }
}
```

## 📋 Checklist de Configuração

Para garantir que tudo funcione corretamente no Easypanel:

- [ ] Configurar variável de ambiente: `app.baseURL=https://pppr.ecl.dev.br/`
- [ ] Fazer novo deploy (push para o repositório)
- [ ] Aguardar o Easypanel concluir o deploy
- [ ] Limpar cache do navegador (ou usar modo anônimo)
- [ ] Testar login novamente
- [ ] Verificar se redireciona corretamente para `https://pppr.ecl.dev.br/sso/admin`

## 🔄 Outras Variáveis Importantes

Certifique-se de que todas as variáveis de ambiente estão configuradas:

```env
# Ambiente
CI_ENVIRONMENT=production

# Base URL
app.baseURL=https://pppr.ecl.dev.br/

# Banco de Dados
DB_HOST=seu-mysql-host.easypanel.host
DB_NAME=nome_do_banco
DB_USER=usuario_mysql
DB_PASS=senha_mysql
DB_DRIVER=MySQLi
```

## 🆘 Troubleshooting

### Ainda redireciona para localhost

**Causa:** A variável de ambiente não foi configurada corretamente ou o cache não foi limpo.

**Solução:**
1. Verifique no painel do Easypanel se `app.baseURL` está configurada
2. Faça um novo deploy para aplicar as mudanças
3. Limpe o cache do navegador
4. Tente em uma aba anônima

### Redireciona mas perde a sessão

**Causa:** Cookie de sessão pode estar configurado para domínio errado.

**Solução:**
Adicione ao `.env` ou variáveis de ambiente:
```env
session.cookieDomain=.ecl.dev.br
session.cookieSecure=true
```

### Erro 404 após login

**Causa:** Rotas não estão sendo encontradas.

**Solução:**
Verifique se o arquivo `.htaccess` está presente em `public/` e se `app.indexPage` está vazio:
```php
public string $indexPage = '';  // deve estar vazio
```

---

**Data da correção:** 16 de Outubro de 2025
