# Configuração de Cache - Redis

## Visão Geral

O sistema está configurado para usar cache diferente dependendo do ambiente:

- **Desenvolvimento Local**: Cache de arquivos (File Handler)
- **Produção (Easypanel + Paketo Buildpacks)**: Redis

## Configuração por Ambiente

### Desenvolvimento Local (.env)

```env
cache.handler = file
```

O cache será armazenado em `writable/cache/` usando o `FileHandler` padrão do CodeIgniter.

### Produção (.env.production)

```env
cache.handler = redis
cache.redis.host = deppen_redis
cache.redis.port = 6379
cache.redis.password = dti@fb
cache.redis.database = 0
cache.redis.username = default
```

## Como Funciona

1. **app/Config/Cache.php**:
   - Define valores padrão para desenvolvimento local
   - No construtor, verifica variáveis de ambiente e sobrescreve as configurações se presentes
   - Permite configuração flexível sem alterar código

2. **Variáveis de Ambiente**:
   - `cache.handler`: Define o handler (file, redis, memcached, etc.)
   - `cache.redis.host`: Hostname do servidor Redis
   - `cache.redis.port`: Porta do Redis (padrão: 6379)
   - `cache.redis.password`: Senha de autenticação
   - `cache.redis.database`: Número do banco de dados Redis (0-15)
   - `cache.redis.username`: Nome de usuário (Redis 6+)

## Deploy no Easypanel

Ao fazer deploy no Easypanel com Paketo Buildpacks:

1. O buildpack detectará o arquivo `.env.production`
2. As variáveis de cache serão carregadas automaticamente
3. O sistema se conectará ao Redis configurado

## Verificação

Para verificar qual handler está sendo usado:

```php
// No seu código
$cache = \Config\Services::cache();
echo get_class($cache); // Mostrará o handler ativo
```

## Configurações Adicionais

### Timeout
Por padrão, o timeout do Redis é 0 (sem timeout). Para alterar:

```env
cache.redis.timeout = 5
```

### Prefix
Para evitar colisões de chave entre múltiplas aplicações:

```env
cache.prefix = myapp_
```

### TTL Padrão
Tempo de vida padrão do cache (em segundos):

```env
cache.ttl = 3600
```

## Troubleshooting

### Redis não conecta em produção

1. Verifique se o serviço Redis está rodando: `redis-cli ping`
2. Confirme o hostname: `deppen_redis` deve resolver corretamente
3. Teste a autenticação: `redis-cli -h deppen_redis -p 6379 -a dti@fb`
4. Verifique logs: `writable/logs/`

### Cache ainda usa arquivos em produção

1. Confirme que `CI_ENVIRONMENT = production` está setado
2. Verifique se `.env.production` está sendo carregado
3. Force limpeza: `php spark cache:clear`

### Fallback para File Handler

Se Redis não estiver disponível, o sistema automaticamente usará o backup handler (dummy por padrão). Para usar file como backup:

```php
// app/Config/Cache.php
public string $backupHandler = 'file';
```

## Segurança

⚠️ **Importante**: Nunca commite senhas no repositório!

- Use variáveis de ambiente do Easypanel para configurações sensíveis
- Adicione `.env*` no `.gitignore`
- Em produção, configure as variáveis diretamente no painel do Easypanel

## Referências

- [CodeIgniter Cache Documentation](https://codeigniter.com/user_guide/libraries/caching.html)
- [Redis Documentation](https://redis.io/documentation)
- [Paketo Buildpacks](https://paketo.io/)
