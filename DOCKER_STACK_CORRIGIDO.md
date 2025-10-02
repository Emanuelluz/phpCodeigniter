# 🐳 Correção Final: Docker Stack Deploy - Network Conflicts

## 🚨 **PROBLEMA IDENTIFICADO**

**Erro Atual**: `network with name phpcodeigniter_internal_net already exists`

**Causa**: Conflito entre rede criada manualmente e rede criada pelo Docker Stack

### **📋 Nova Análise:**
1. **Passo manual** cria `phpcodeigniter_internal_net`
2. **Docker Stack** tenta criar a mesma rede → **CONFLITO** ❌
3. Deploy falha por conflito de nomes de rede

---

## ✅ **CORREÇÃO FINAL IMPLEMENTADA**

### **1. 🧹 Limpeza Agressiva de Redes Órfãs**
```yaml
- name: Clean orphaned networks if any
  shell: bash
  run: |
    echo "Cleaning any orphaned phpcodeigniter networks..."
    # Remove redes órfãs do phpcodeigniter se existirem
    docker network ls --filter "name=phpcodeigniter" --format "{{.Name}}" | while read network; do
      if [ ! -z "$network" ]; then
        echo "Removing orphaned network: $network"
        docker network rm "$network" 2>/dev/null || echo "Network $network already removed or in use"
      fi
    done
    
    # Force cleanup de recursos órfãos do sistema
    echo "Running system cleanup..."
    docker system prune -f --filter "label=com.docker.stack.namespace=phpcodeigniter" || true
    
    # Aguardar um pouco para estabilizar
    echo "Waiting for system stabilization..."
    sleep 5
```

### **2. 🌐 Docker-Compose Sem Nome Explícito**
```yaml
networks:
  labnet:
    external: true
    name: labnet
  internal_net:
    driver: overlay
    attachable: true    # ← SEM nome explícito! Docker Stack gerencia
```

### **3. � Deploy Simplificado com Debug**
```yaml
- name: Deploy stack
  shell: bash
  run: |
    echo "Deploying stack..."
    # Listar redes antes do deploy para debug
    echo "Networks before deploy:"
    docker network ls | grep -E "phpcodeigniter|labnet|NAME" || true
    
    # Deploy do stack
    docker stack deploy -c docker-compose.yml phpcodeigniter --with-registry-auth
    
    echo "Stack deployed successfully"
```

---

## 🔧 **ESTRATÉGIA CORRIGIDA**

### **❌ Abordagem Anterior (Problemática):**
1. Criar rede manualmente → `phpcodeigniter_internal_net`
2. Docker Stack tenta criar → **CONFLITO**
3. Deploy falha

### **✅ Nova Abordagem (Corrigida):**
1. **Remover** stack completamente
2. **Aguardar** limpeza de redes (60s)
3. **Forçar limpeza** de qualquer rede órfã
4. **System prune** de recursos órfãos
5. **Aguardar estabilização** (5s)
6. **Deploy** deixando Docker Stack gerenciar as redes
7. **Debug** com listagem de redes

### **🎯 Diferenças Chave:**
- ❌ **Antes**: Criar rede manualmente antes do deploy
- ✅ **Agora**: Deixar Docker Stack criar e gerenciar as redes
- ❌ **Antes**: Nome explícito no docker-compose
- ✅ **Agora**: Docker Stack define o nome automaticamente
- ❌ **Antes**: Retry complexo mascarando o problema
- ✅ **Agora**: Limpeza adequada + deploy direto

---

## � **FLUXO FINAL**

### **🔄 Sequência Corrigida:**
1. **Build** da imagem Docker
2. **Verificar** Docker Swarm ativo  
3. **Criar/Verificar** rede externa `labnet`
4. **Remover** stack existente
5. **Aguardar** remoção completa (60s)
6. **Aguardar** limpeza de redes (30s)
7. **Limpar** redes órfãs ⭐ **CHAVE**
8. **System prune** ⭐ **CHAVE**
9. **Estabilizar** (5s) ⭐ **CHAVE**
10. **Deploy** direto ⭐ **SIMPLIFICADO**
11. **Verificar** serviços
12. **Migrations** e setup

### **📝 Naming Convention:**
- **Externa**: `labnet` (nome explícito)
- **Interna**: `phpcodeigniter_internal_net` (Docker Stack gerencia)
- **Pattern**: `{stack_name}_{network_name}`

---

## 🧪 **RESULTADO ESPERADO**

### **✅ Deploy Bem-Sucedido:**
```bash
Networks before deploy:
NETWORK ID     NAME      DRIVER    SCOPE
abc123def456   labnet    overlay   swarm

Creating network phpcodeigniter_internal_net
Creating service phpcodeigniter_app  
Creating service phpcodeigniter_mariadb
Stack deployed successfully
```

### **� Debug Info:**
- Lista de redes antes do deploy
- Confirmação de limpeza de redes órfãs
- System prune de recursos órfãos
- Status final das redes

---

## � **POSSÍVEIS FALHAS E SOLUÇÕES**

### **Problema**: Rede ainda existe após limpeza
```bash
# Solução manual de emergência
docker network rm phpcodeigniter_internal_net --force
docker system prune -f
```

### **Problema**: Stack não remove completamente
```bash
# Force removal
docker stack rm phpcodeigniter
docker service rm $(docker service ls -q --filter label=com.docker.stack.namespace=phpcodeigniter)
docker network rm $(docker network ls -q --filter label=com.docker.stack.namespace=phpcodeigniter)
```

---

**� Correção focada na raiz do problema: conflito de nomenclatura de redes.**