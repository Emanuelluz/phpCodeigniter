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


name: CI/CD - Build and Deploy

on:
  push:
    branches: [ "main" ]
    paths:
      - 'app/**'
      - 'modules/**'
      - 'Dockerfile'
      - 'docker-compose.yml'
      - 'composer.json'
      - 'composer.lock'
  pull_request:
    branches: [ "main" ]

permissions:
  contents: read

env:
  IMAGE_TAG: phpcodeigniter:latest

jobs:
  deploy:
    name: Build & Deploy (Swarm)
    runs-on: self-hosted

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Build Docker image
        run: |
          docker build --pull -t ${{ env.IMAGE_TAG }} .

      - name: Ensure Docker Swarm is active
        shell: bash
        run: |
          if ! docker info 2>/dev/null | grep -q "Swarm: active"; then
            docker swarm init
          fi

      - name: Ensure external overlay network labnet exists
        shell: bash
        run: |
          if ! docker network inspect labnet >/dev/null 2>&1; then
            echo "Creating overlay network: labnet"
            docker network create -d overlay --attachable labnet
          else
            echo "Overlay network 'labnet' already exists"
          fi
          echo "Networks snapshot:" && docker network ls | grep -E "labnet|phpcodeigniter|INGRESS|ingress" || true

      - name: Stop and remove existing stack (zero-downtime not required)
        shell: bash
        run: |
          if docker stack ls | grep -q "^phpcodeigniter\b"; then
            echo "Removing existing stack phpcodeigniter..."
            docker stack rm phpcodeigniter
            # Aguarda remoção completa do stack (services/networks)
            for i in {1..60}; do
              docker stack ls | grep -q "^phpcodeigniter\b" || break
              echo "Waiting stack removal... $i"; sleep 2
            done
          else
            echo "No existing stack named phpcodeigniter."
          fi

      - name: Deploy stack
        run: |
          docker stack deploy -c docker-compose.yml phpcodeigniter --with-registry-auth

      - name: Verify services
        shell: bash
        run: |
          docker stack ls
          docker stack services phpcodeigniter || true
          echo "Service tasks:" && docker service ps phpcodeigniter_app --no-trunc || true

      - name: Cleanup unused images
        shell: bash
        run: |
          echo "Pruning dangling images..."
          docker image prune -f
          echo "Optionally prune all unused images (commented by default)"
          echo "# docker image prune -a -f"