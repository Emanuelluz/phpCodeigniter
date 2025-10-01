# Docker Swarm Deploy Guide

## Comandos Úteis

### Inicializar Swarm
```bash
docker swarm init
```

### Deploy do Stack
```bash
docker stack deploy -c docker-compose.yml phpcodeigniter
```

### Verificar Serviços
```bash
docker stack services phpcodeigniter
docker service ls
docker service ps phpcodeigniter_app
```

### Ver Logs
```bash
docker service logs phpcodeigniter_app -f
docker service logs phpcodeigniter_mariadb -f
```

### Escalar Serviço
```bash
docker service scale phpcodeigniter_app=3
```

### Update de Imagem (Rolling Update)
```bash
docker service update --image phpcodeigniter:latest phpcodeigniter_app
```

### Remover Stack
```bash
docker stack rm phpcodeigniter
```

## Integração com Portainer

1. **Portainer detecta Swarm automaticamente**
2. **Vá em Stacks > Add Stack**
3. **Use "Web editor" ou "Repository" (GitOps)**
4. **Deploy como Swarm Stack**

## Vantagens do Swarm

- ✅ Rolling updates sem downtime
- ✅ Load balancing automático
- ✅ Health checks nativos
- ✅ Replica automática em caso de falha
- ✅ Melhor integração com Portainer
- ✅ Service discovery nativo
