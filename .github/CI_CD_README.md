# Pipeline CI/CD

Este projeto usa GitHub Actions para CI/CD automatizado.

## Workflow: CI/CD - Build and Deploy

### Triggers
- Push na branch `main`
- Apenas quando há mudanças em:
  - `app/**` (código da aplicação)
  - `modules/**` (módulos)
  - `Dockerfile`
  - `docker-compose.yml`
  - `composer.json` / `composer.lock`

### Jobs

#### 1. Test & Validate
- Instala extensões PHP necessárias
- Valida `composer.json` e `composer.lock`
- Instala dependências via Composer
- Cache de pacotes Composer para builds mais rápidos

#### 2. Deploy
- Só roda em pushes na `main` (não em PRs)
- Depende do job de teste
- Build da imagem Docker
- Para containers antigos
- Deploy com Docker Compose
- Verifica o deployment
- Limpa imagens antigas

## Requisitos

### Self-Hosted Runner
- Docker e Docker Compose instalados
- PHP 8.3 com extensões: dom, xml, mbstring
- Composer instalado

## Estrutura

```
.github/
└── workflows/
    ├── php.yml      # CI/CD principal
    └── deploy.yml   # Workflow alternativo
```