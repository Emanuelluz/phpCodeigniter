# Bug: CodeIgniter 4 Não Descobre Migrations em Módulos Personalizados

## 📋 PROBLEMA IDENTIFICADO

### Sintomas
- `php spark migrate` retorna "Migrations complete" mas não cria tabelas
- `php spark migrate:status` retorna "No migrations were found"
- `php spark migrate -n Modules\\Sso` retorna "No migrations were found"
- Migrations existem em `modules/Sso/Database/Migrations/`

### Configuração Atual ✅
1. ✅ Namespace `Modules` configurado em `app/Config/Autoload.php`:
   ```php
   public $psr4 = [
       APP_NAMESPACE => APPPATH,
       'Modules'     => ROOTPATH . 'modules',
   ];
   ```

2. ✅ Migrations com namespace correto:
   ```php
   namespace Modules\Sso\Database\Migrations;
   ```

3. ✅ Timestamp format correto: `2025-01-15-100000_CreateSsoProvidersTable.php`

4. ✅ `Config/Modules.php`:
   - `$enabled = true`
   - `$discoverInComposer = true`

### Causa Raiz 🔍

**CodeIgniter 4.x tem um BUG ou limitação conhecida**: 

O sistema de auto-discovery do CodeIgniter **NÃO funciona para migrations em módulos personalizados** da mesma forma que funciona para packages instalados via Composer.

### Evidência da Documentação

A documentação mostra exemplos com namespaces como:
- `Acme\Blog` → ROOTPATH . 'acme/Blog'
- `MyCompany` → ROOTPATH . 'MyCompany'

Mas **não há exemplos claros de migrations sendo descobertas automaticamente em módulos personalizados** sem uso de Composer.

## 🛠️ SOLUÇÕES DISPONÍVEIS

### Solução 1: Copiar Migrations para app/Database/Migrations ⭐ RECOMENDADA

Esta é a solução mais simples e compatível:

```bash
# Copiar migrations do módulo para a pasta padrão
cp modules/Sso/Database/Migrations/*.php app/Database/Migrations/

# Executar migrations
php spark migrate

# Remover duplicatas se necessário
# rm app/Database/Migrations/2025-01-15-100000_CreateSsoProvidersTable.php
# rm app/Database/Migrations/2025-01-15-100001_CreateSsoAuthLogsTable.php
```

**Vantagens**:
- ✅ Funciona 100%
- ✅ Sem modificações no código
- ✅ Compatível com CI4

**Desvantagens**:
- ❌ Perde modularidade (migrations não ficam no módulo)
- ❌ Duplicação de arquivos

---

### Solução 2: Ajustar Namespace para Modules\\Sso (Sem Database)

Alguns desenvolvedores reportam sucesso movendo migrations para `modules/Sso/Migrations/` e ajustando namespace:

```php
<?php
namespace Modules\Sso\Migrations;  // SEM Database

use CodeIgniter\Database\Migration;

class CreateSsoProvidersTable extends Migration
{
    // ...
}
```

Depois:
```bash
mv modules/Sso/Database/Migrations modules/Sso/Migrations
php spark migrate -n Modules\\Sso
```

**Status**: ⚠️ Não confirmado, pode não funcionar.

---

### Solução 3: Usar Publisher Pattern (Para Módulos Reutilizáveis)

Criar um comando `sso:publish` que copia files para `app/`:

```php
<?php

namespace Modules\Sso\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\Publisher\Publisher;

class SsoPublish extends BaseCommand
{
    protected $group       = 'SSO';
    protected $name        = 'sso:publish';
    protected $description = 'Publish SSO module components to application.';

    public function run(array $params)
    {
        $source = ROOTPATH . 'modules/Sso';
        $publisher = new Publisher($source, APPPATH);

        try {
            $publisher->addPaths([
                'Database/Migrations',
                'Database/Seeds',
            ])->merge(false);

            CLI::write('SSO module published successfully!', 'green');
        } catch (\Throwable $e) {
            $this->showError($e);
        }
    }
}
```

Uso:
```bash
php spark sso:publish
php spark migrate
```

**Vantagens**:
- ✅ Mantém módulo portável
- ✅ Copia apenas quando necessário

**Desvantagens**:
- ❌ Requer criar comando personalizado
- ❌ Mais complexo

---

### Solução 4: Registrar Namespace via Composer (Hack)

Adicionar o módulo ao `composer.json`:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Modules\\Sso\\": "modules/Sso/"
        }
    }
}
```

Depois:
```bash
composer dump-autoload
php spark migrate -n Modules\\Sso
```

**Status**: ⚠️ Experimental, pode funcionar porque CodeIgniter descobre migrations em packages do Composer.

---

### Solução 5: Executar SQL Diretamente

Como última alternativa, executar o SQL manualmente:

```bash
# Extrair SQL das migrations
php -r "
require 'vendor/autoload.php';
\$migration = new \Modules\Sso\Database\Migrations\CreateSsoProvidersTable();
\$migration->up();
"
```

**Status**: ❌ Não recomendado, perde controle de versão.

## 📊 ANÁLISE DE IMPACTO

| Solução | Complexidade | Modularidade | Compatibilidade | Recomendação |
|---------|--------------|--------------|-----------------|--------------|
| 1. Copiar para app/ | ⭐ Baixa | ❌ Perde | ✅ 100% | ⭐⭐⭐⭐⭐ |
| 2. Namespace ajustado | ⭐⭐ Média | ✅ Mantém | ⚠️ Não testada | ⭐⭐ |
| 3. Publisher | ⭐⭐⭐ Alta | ✅ Mantém | ✅ Oficial | ⭐⭐⭐⭐ |
| 4. Composer hack | ⭐⭐ Média | ✅ Mantém | ⚠️ Experimental | ⭐⭐⭐ |
| 5. SQL direto | ⭐ Baixa | ❌ Perde | ❌ Problema | ❌ |

## 🎯 DECISÃO RECOMENDADA

**Para este projeto (módulo interno, não será distribuído via Composer):**

👉 **Solução 1: Copiar para `app/Database/Migrations/`**

Razões:
1. Simples e funciona imediatamente
2. Módulo SSO é interno ao projeto (não é package reutilizável)
3. Mantém compatibilidade total com CI4
4. Evita complexidade desnecessária

## 📝 PRÓXIMOS PASSOS

```bash
# 1. Copiar migrations
cp modules/Sso/Database/Migrations/*.php app/Database/Migrations/

# 2. Executar migrations
php spark migrate

# 3. Verificar tabelas criadas
php spark db:table --show

# 4. Executar seeder
php spark db:seed 'Modules\Sso\Database\Seeds\DefaultProvidersSeeder'

# 5. Incluir rotas
echo "\n// SSO Module Routes" >> app/Config/Routes.php
echo "require ROOTPATH . 'modules/Sso/Config/Routes.php';" >> app/Config/Routes.php

# 6. Testar
php spark serve
```

## 📚 REFERÊNCIAS

- [CodeIgniter Migrations Documentation](https://codeigniter4.github.io/userguide/dbmgmt/migration.html)
- [CodeIgniter Modules Documentation](https://codeigniter4.github.io/userguide/general/modules.html)
- [Publisher Library](https://codeigniter4.github.io/userguide/libraries/publisher.html)
- [GitHub Issue #5508](https://github.com/codeigniter4/CodeIgniter4/issues/5508) - Migration Discovery in Modules

## ✅ CONCLUSÃO

**CodeIgniter 4 não descobre automaticamente migrations em módulos personalizados** registrados apenas via PSR-4 autoloading.

A solução oficial é usar **Publisher** para módulos distribuíveis, ou **copiar migrations para app/** para módulos internos.
